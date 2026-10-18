<?php
require_once "../class/mysql.php";
require_once "../class/config.php";
require_once "../class/rbac.php";
header('Content-Type: application/json; charset=UTF-8');
$config=Configuration::getInstance();$database=Database::getInstance($config);$db=$database->getConnection();$rbacClass=new RBAC($db);
if(session_status()===PHP_SESSION_NONE)session_start();
$adminLanguage=$_COOKIE['admin_language']??$config->getConfig('defaultLanguage');$langFile="../lang/{$adminLanguage}.php";if(is_file($langFile))require $langFile;define('_lang',$config->getLang($adminLanguage));
function uvmLang($k){return _lang[$k]??$k;}function uvmOut($s,$m,$c=200){http_response_code($c);echo json_encode(['status'=>$s,'message'=>$m],JSON_UNESCAPED_UNICODE);exit;}
function uvmRole($db,$id){$s=$db->prepare("SELECT id,all_rbac FROM rbac WHERE id=? LIMIT 1");$s->bind_param('i',$id);$s->execute();$r=$s->get_result()->fetch_assoc();$s->close();return$r?:null;}
function uvmRows($db,$rid){$s=$db->prepare("SELECT COUNT(*) cnt FROM permissions_operation WHERE rbac_id=?");$s->bind_param('i',$rid);$s->execute();$n=(int)($s->get_result()->fetch_assoc()['cnt']??0);$s->close();return$n;}
function uvmNormalizeOps($db,$raw){$d=json_decode((string)$raw,true);if(!is_array($d))$d=[];$valid=[];$r=$db->query("SELECT id FROM operations");while($r&&$x=$r->fetch_assoc())$valid[(int)$x['id']]=true;$out=[];foreach($d as$id){$id=(int)$id;if($id>0&&isset($valid[$id]))$out[$id]=true;}$ids=array_keys($out);sort($ids,SORT_NUMERIC);return array_map('strval',$ids);}
if($_SERVER['REQUEST_METHOD']!=='POST')uvmOut('error',uvmLang('invalid_request'),405);
$aid=(int)($_SESSION['admin_id']??0);if($aid<=0)uvmOut('error',uvmLang('view_access_unauthorized'),401);$s=$db->prepare("SELECT status FROM admins WHERE id=? LIMIT 1");$s->bind_param('i',$aid);$s->execute();$a=$s->get_result()->fetch_assoc();$s->close();if(!$a||strcasecmp((string)$a['status'],'Active')!==0)uvmOut('error',uvmLang('view_access_unauthorized'),401);
if(!$rbacClass->checkPermissionPartByName('user_view','access'))uvmOut('error',uvmLang('view_access_forbidden'),403);
$csrf=(string)($_POST['csrf']??'');if(!$csrf||!hash_equals((string)($_SESSION['user_view_csrf']??''),$csrf))uvmOut('error',uvmLang('view_access_invalid_csrf'),403);
$action=strtolower(trim((string)($_POST['action']??'')));$rid=(int)($_POST['rbac_id']??0);$role=uvmRole($db,$rid);if(!$role)uvmOut('error',uvmLang('view_access_role_invalid'),404);if((int)$role['all_rbac']===1)uvmOut('error',uvmLang('view_access_full_access_blocked'),403);
try{
 if($action==='save'){$rows=uvmRows($db,$rid);$ops=uvmNormalizeOps($db,$_POST['operations_json']??'[]');if(!$rows&&!$ops)uvmOut('success',uvmLang('view_access_no_change'));
   $need=$rows?'edit_operation':'add_operation';if(!$rbacClass->checkPermissionOperationByName($need))uvmOut('error',uvmLang('view_access_forbidden'),403);if($rows&&count($ops)===0){if(!$rbacClass->checkPermissionOperationByName('delete_operation'))uvmOut('error',uvmLang('view_access_clear_requires_delete'),403);}
   $ser=serialize($ops);$db->begin_transaction();try{if($rows){$s=$db->prepare("UPDATE permissions_operation SET operation=? WHERE rbac_id=?");$s->bind_param('si',$ser,$rid);}else{$parts=serialize([]);$status='Active';$s=$db->prepare("INSERT INTO permissions_operation(rbac_id,parts,operation,status) VALUES(?,?,?,?)");$s->bind_param('isss',$rid,$parts,$ser,$status);}$s->execute();$s->close();$db->commit();}catch(Throwable$e){$db->rollback();throw$e;}uvmOut('success',uvmLang('view_access_save_success'));
 }
 if($action==='clear'){if(!$rbacClass->checkPermissionOperationByName('delete_operation'))uvmOut('error',uvmLang('view_access_forbidden'),403);$rows=uvmRows($db,$rid);if(!$rows)uvmOut('success',uvmLang('view_access_no_change'));$empty=serialize([]);$s=$db->prepare("UPDATE permissions_operation SET operation=? WHERE rbac_id=?");$s->bind_param('si',$empty,$rid);$s->execute();$s->close();uvmOut('success',uvmLang('view_access_clear_success'));}
 uvmOut('error',uvmLang('invalid_request'),400);
}catch(Throwable$e){uvmOut('error',uvmLang('view_access_unexpected_error'),500);}

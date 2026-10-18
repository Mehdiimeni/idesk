<?php
///ipanel/controller/structure/user_view_details.php
use ipanel\model\StructureModel;

$config = Configuration::getInstance();
$database = Database::getInstance($config);
$db = $database->getConnection();
$structureModel = new StructureModel($db);
$textToolsClass = TextTools::getInstance();
$rbacClass = new RBAC($db);

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_view_csrf'])) $_SESSION['user_view_csrf'] = bin2hex(random_bytes(32));
$userViewCsrf = $_SESSION['user_view_csrf'];

$canAddUserView = (bool)$rbacClass->checkPermissionOperationByName('add_operation');
$canEditUserView = (bool)$rbacClass->checkPermissionOperationByName('edit_operation');
$canDeleteUserView = (bool)$rbacClass->checkPermissionOperationByName('delete_operation');

function userViewDetailDecode($serialized): array
{
    if ($serialized === null || $serialized === '') return [];
    $decoded = @unserialize((string)$serialized, ['allowed_classes'=>false]);
    if (!is_array($decoded)) return [];
    $ids=[]; foreach($decoded as $id){$id=(int)$id;if($id>0)$ids[$id]=true;} $ids=array_keys($ids);sort($ids,SORT_NUMERIC);return $ids;
}
function userViewDetailCount(mysqli $db,string $table,int $rid): int
{
    $s=$db->prepare("SELECT COUNT(*) cnt FROM {$table} WHERE rbac_id=?");$s->bind_param('i',$rid);$s->execute();$n=(int)($s->get_result()->fetch_assoc()['cnt']??0);$s->close();return $n;
}
function userViewDetailCompanyCount(mysqli $db,string $table,int $cid): int
{
    $s=$db->prepare("SELECT COUNT(*) cnt FROM {$table} p JOIN units u ON u.id=p.unit_id WHERE u.company_id=?");$s->bind_param('i',$cid);$s->execute();$n=(int)($s->get_result()->fetch_assoc()['cnt']??0);$s->close();return $n;
}
function userViewDetailScope(mysqli $db,array $role): string
{
    $rid=(int)$role['id'];$cid=(int)($role['company_id']??0);$a=userViewDetailCount($db,'admins',$rid);$u=userViewDetailCount($db,'users',$rid);
    if($a&&$u)return'shared';if($a)return'ipanel';if($u)return'iweb';$ca=userViewDetailCompanyCount($db,'admins',$cid);$cu=userViewDetailCompanyCount($db,'users',$cid);if($ca&&!$cu)return'ipanel';if($cu&&!$ca)return'iweb';if($ca&&$cu)return'shared';return'unassigned';
}
function userViewOperationGroup(string $name): string
{
    $n=strtolower($name);
    if(strpos($n,'condition_')===0||$n==='condition_operation')return'status';
    if(in_array($n,['add_operation','delete_operation','edit_operation','active_operation','inactive_operation'],true))return'critical';
    if(strpos($n,'comment')!==false||strpos($n,'note_')===0||strpos($n,'message')!==false)return'communication';
    if(strpos($n,'attach_')===0||strpos($n,'accounting_')===0||$n==='signature_operation')return'files_finance';
    if(strpos($n,'schedule')!==false||strpos($n,'man_hour')!==false||strpos($n,'person_hour')!==false||strpos($n,'delivery_time')!==false||strpos($n,'project')!==false||strpos($n,'daily_report')!==false||strpos($n,'current_')===0||strpos($n,'kanban')!==false||$n==='statistics_operation')return'planning';
    if(strpos($n,'ticket')!==false||strpos($n,'assign_')===0||strpos($n,'priority')!==false||strpos($n,'mark_')===0||$n==='mark_operation'||strpos($n,'pointer_')===0||strpos($n,'change_type')===0||$n==='view_by_operation'||$n==='view_location_operation'||$n==='status_history_view_operation'||$n==='deactivation_operation'||$n==='view_deactivation_operation')return'ticket';
    return'system';
}

$roleId = isset($_GET['rbac_id']) ? (int)$_GET['rbac_id'] : 0;
if ($roleId <= 0 && isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $pid=(int)$_GET['id'];$s=$db->prepare("SELECT rbac_id FROM permissions_operation WHERE id=? LIMIT 1");$s->bind_param('i',$pid);$s->execute();$x=$s->get_result()->fetch_assoc();$s->close();$roleId=(int)($x['rbac_id']??0);
}
if($roleId<=0){echo "<script>window.location.replace('./user_view');</script>";exit();}

$s=$db->prepare("SELECT r.id,r.rbac_name,r.all_rbac,r.company_id,r.unit_id,r.manager_id,cp.company_name,un.unit_name,act.activity_name FROM rbac r LEFT JOIN company_profiles cp ON cp.id=r.company_id LEFT JOIN units un ON un.id=r.unit_id LEFT JOIN activities act ON act.id=cp.activity_id WHERE r.id=? LIMIT 1");
$s->bind_param('i',$roleId);$s->execute();$rbac_data=$s->get_result()->fetch_assoc();$s->close();
if(!$rbac_data){echo "<script>window.location.replace('./user_view');</script>";exit();}

$permissionRows=[];$s=$db->prepare("SELECT id,parts,operation,status,last_updated_date FROM permissions_operation WHERE rbac_id=? ORDER BY id");$s->bind_param('i',$roleId);$s->execute();$r=$s->get_result();while($x=$r->fetch_assoc())$permissionRows[]=$x;$s->close();
$permissionRowExists=!empty($permissionRows);$permission_data=$permissionRowExists?$permissionRows[0]:['id'=>0,'parts'=>serialize([]),'operation'=>serialize([]),'status'=>'Active','last_updated_date'=>null];
$selectedOperationIds=userViewDetailDecode($permission_data['operation']);$selectedOperationSet=array_fill_keys($selectedOperationIds,true);
$canonicalSignature=implode(',',$selectedOperationIds);$duplicateRowsDiffer=false;foreach(array_slice($permissionRows,1) as $pr){if(implode(',',userViewDetailDecode($pr['operation']))!==$canonicalSignature){$duplicateRowsDiffer=true;break;}}

$systemScope=userViewDetailScope($db,$rbac_data);$isFullAccess=(int)$rbac_data['all_rbac']===1;$canSave=!$isFullAccess&&($permissionRowExists?$canEditUserView:$canAddUserView);$canClear=!$isFullAccess&&$permissionRowExists&&$canDeleteUserView;

$operationGroups=['ticket'=>[],'status'=>[],'communication'=>[],'files_finance'=>[],'planning'=>[],'critical'=>[],'system'=>[]];
$criticalNames=['add_operation'=>true,'delete_operation'=>true,'edit_operation'=>true,'active_operation'=>true,'inactive_operation'=>true];
$ticketSendAllOperation=null;$ticketSendAllSelected=false;$totalOperationCount=0;
$res=$structureModel->getOperations();if($res){while($op=$res->fetch_assoc()){$op['id']=(int)$op['id'];$op['is_selected']=isset($selectedOperationSet[$op['id']]);$op['is_critical']=isset($criticalNames[(string)$op['operation_name']]);if((string)$op['operation_name']==='ticket_send_all_operation'){$ticketSendAllOperation=$op;$ticketSendAllSelected=!empty($op['is_selected']);$totalOperationCount++;continue;}$g=userViewOperationGroup((string)$op['operation_name']);$operationGroups[$g][]=$op;$totalOperationCount++;}}
$selectedOperationCount=count($selectedOperationIds);$inactiveOperationCount=max(0,$totalOperationCount-$selectedOperationCount);
$lastUpdated=null;foreach($permissionRows as $pr){if($lastUpdated===null||strcmp((string)$pr['last_updated_date'],(string)$lastUpdated)>0)$lastUpdated=(string)$pr['last_updated_date'];}

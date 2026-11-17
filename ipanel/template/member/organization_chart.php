<?php
$orgEscape = static function ($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>
<style>
.org6 .org6-header{background:linear-gradient(110deg,#f0f5ff,#fff);border:1px solid #dfe7f5;border-radius:16px;padding:18px;margin-bottom:16px}
.org6 .org6-tools{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:14px 0}
.org6 .org6-tools input,.org6 .org6-tools select{max-width:300px}
.org6 .org6-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.org6 .org6-unit{border:1px solid #dfe7f5;border-radius:16px;background:#fff;overflow:hidden}
.org6 .org6-unit-head{background:#f5f8fd;padding:16px;font-weight:750;display:flex;justify-content:space-between;gap:8px}
.org6 .org6-content{padding:15px}.org6 .org6-heading{font-size:13px;font-weight:750;margin:10px 0;color:#34527e}
.org6 .org6-person{border:1px solid #e4eaf4;border-radius:10px;padding:10px 12px;margin-bottom:8px;background:#fff}
.org6 .org6-person small{display:block;color:#64748b;margin-top:4px}.org6 .org6-muted{color:#7b8797;font-size:12px}
.org6 .org6-active{color:#178053}.org6 .org6-inactive{color:#9a5560}
@media(max-width:900px){.org6 .org6-grid{grid-template-columns:1fr}}
</style>
<div class="content-page org6"><div class="content"><div class="container-fluid py-3">
<div class="org6-header"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
<div><h4 class="mb-1">چارت سازمانی مدیران و کارشناسان</h4><div class="org6-muted"><?php echo $orgEscape($orgCompanyName); ?> — <?php echo count($orgUnits); ?> واحد، <?php echo $orgPeopleCount; ?> نفر</div></div>
<a href="./admins" class="btn btn-outline-primary btn-sm">مدیریت کاربران داخلی</a>
</div></div>
<div class="org6-tools"><input id="org6Search" class="form-control" type="search" placeholder="جستجوی نام، نقش یا واحد">
<select id="org6Status" class="form-select"><option value="all">همه وضعیت‌ها</option><option value="Active">فعال</option><option value="Inactive">غیرفعال</option></select>
<span id="org6Visible" class="org6-muted" aria-live="polite"></span></div>
<div class="org6-grid" id="org6Grid">
<?php foreach ($orgUnits as $unit): ?>
<section class="org6-unit" data-unit="<?php echo $orgEscape($unit['unit_name']); ?>">
<div class="org6-unit-head"><span><?php echo $orgEscape($unit['unit_name']); ?></span><span class="org6-muted"><?php echo count($unit['managers']) + count($unit['experts']) + count($unit['unknown']); ?> نفر</span></div>
<div class="org6-content">
<?php foreach (['managers' => 'مدیران و مسئولان', 'experts' => 'کارشناسان', 'unknown' => 'فاقد نقش مشخص'] as $group => $label): ?>
<?php if (empty($unit[$group])) { continue; } ?>
<div class="org6-heading"><?php echo $orgEscape($label); ?></div>
<?php foreach ($unit[$group] as $person): ?>
<div class="org6-person" data-status="<?php echo $orgEscape($person['status']); ?>" data-search="<?php echo $orgEscape(mb_strtolower($unit['unit_name'] . ' ' . $person['name'] . ' ' . ($person['rbac_name'] ?? '') . ' ' . ($person['parent_name'] ?? ''))); ?>">
<strong><?php echo $orgEscape($person['name']); ?></strong>
<small><?php echo $orgEscape($person['rbac_name'] ?: 'بدون دسترسی تعیین‌شده'); ?></small>
<?php if (!empty($person['parent_name'])): ?><small>نقش بالادستی: <?php echo $orgEscape($person['parent_name']); ?></small><?php endif; ?>
<small class="<?php echo $person['status'] === 'Active' ? 'org6-active' : 'org6-inactive'; ?>"><?php echo $person['status'] === 'Active' ? 'فعال' : 'غیرفعال'; ?></small>
</div>
<?php endforeach; endforeach; ?>
</div></section>
<?php endforeach; ?>
</div>
<?php if (!$orgUnits): ?><div class="alert alert-secondary">واحدی برای شرکت جاری ثبت نشده است.</div><?php endif; ?>
</div></div></div>
<script>
(function(){
var search=document.getElementById('org6Search'),status=document.getElementById('org6Status'),count=document.getElementById('org6Visible');
function filter(){var q=search.value.trim().toLocaleLowerCase(),s=status.value,n=0;
document.querySelectorAll('#org6Grid .org6-unit').forEach(function(unit){var visible=0;
unit.querySelectorAll('.org6-person').forEach(function(p){var show=(s==='all'||p.dataset.status===s)&&(!q||p.dataset.search.indexOf(q)>-1);
p.hidden=!show;if(show){visible++;n++;}});
unit.hidden=visible===0 && (q!==''||s!=='all');
unit.querySelectorAll('.org6-heading').forEach(function(h){var el=h.nextElementSibling,any=false;
while(el && !el.classList.contains('org6-heading')){if(el.classList.contains('org6-person')&&!el.hidden)any=true;el=el.nextElementSibling;}h.hidden=!any;});
});count.textContent=n+' نفر';}
search.addEventListener('input',filter);status.addEventListener('change',filter);filter();
})();
</script>

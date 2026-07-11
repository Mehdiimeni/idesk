<?php
///template/ticket/priority_list_logs.php
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="./priority_list" class="btn btn-sm btn-outline-secondary">
                                <?php echo _lang['back'] ?? "بازگشت"; ?>
                            </a>
                            <a href="./priority_list_details?list_id=<?php echo (int)$list['id']; ?>" class="btn btn-sm btn-outline-primary ms-1">
                                <?php echo _lang['details'] ?? "بازگشت به جزئیات"; ?>
                            </a>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['logs_comments'] ?? "لاگ‌ها و کامنت‌ها"; ?>
                            #<?php echo (int)$list['id']; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body border">
                    <div class="row">
                        <div class="col-md-4"><b><?php echo _lang['company'] ?? "شرکت"; ?>:</b> <?php echo htmlspecialchars($companyName ?? ''); ?></div>
                        <div class="col-md-3"><b><?php echo _lang['group'] ?? "گروه نوع"; ?>:</b> <?php echo htmlspecialchars($list['type_group']); ?></div>
                        <div class="col-md-3"><b><?php echo _lang['status'] ?? "وضعیت"; ?>:</b> 
                    
                    
                    <?php if ($list['status'] === 'approved'): ?>
                        <span class="badge bg-success">
                            <?php echo _lang['confirmation']; ?>
                        </span>
                        <?php elseif ($list['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger"><?php echo _lang['rejected']; ?></span>
                                   
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">
                            <?php echo _lang['need_confirmation']; ?>
                        </span>
                    <?php endif; ?></div>
                    </div>
                </div>
            </div>

            <!-- List Comments -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><?php echo _lang['list_comments'] ?? "کامنت‌های لیست"; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo _lang['date'] ?? "تاریخ"; ?></th>
                                    <th><?php echo _lang['by'] ?? "توسط"; ?></th>
                                    <th><?php echo _lang['comment'] ?? "کامنت"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($listComments)): ?>
                                    <tr><td colspan="3"><?php echo _lang['no_data'] ?? "کامنتی وجود ندارد"; ?></td></tr>
                                <?php else: ?>
                                    <?php foreach ($listComments as $c): ?>
                                        <tr>
                                            <td>
                                        <?php $dateConverter = new DateConverter($c['creation_date'], $config->getNowLanguage('a'));
                                        echo $dateConverter->convertToShamsi(); ?>
                                        </td>
                                            <td>
                                                <?php
                                                    $name = $c['admin_name'] ?? $c['user_name'] ?? '';
                                                    $role = $c['admin_id'] ? 'Admin' : 'User';
                                                    echo htmlspecialchars($role . ' ' . $name);
                                                ?>
                                            </td>
                                            <td><?php echo nl2br(htmlspecialchars($c['comment_text'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- List Logs -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><?php echo _lang['list_logs'] ?? "لاگ‌های لیست"; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo _lang['date'] ?? "تاریخ"; ?></th>
                                    <th><?php echo _lang['action'] ?? "اکشن"; ?></th>
                                    <th><?php echo _lang['by'] ?? "توسط"; ?></th>
                                    <th><?php echo _lang['note'] ?? "توضیح"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($listLogs)): ?>
                                    <tr><td colspan="4"><?php echo _lang['no_data'] ?? "لاگی وجود ندارد"; ?></td></tr>
                                <?php else: ?>
                                    <?php foreach ($listLogs as $l): ?>
                                        <tr>
                                            <td>
                                        
                                        <?php $dateConverter = new DateConverter($l['creation_date'], $config->getNowLanguage('a'));
                                        echo $dateConverter->convertToShamsi(); ?></td>
                                            <td><?php echo htmlspecialchars($l['action']); ?></td>
                                            <td>
                                                <?php
                                                    $name = $l['admin_name'] ?? $l['user_name'] ?? '';
                                                    $role = $l['admin_id'] ? 'Admin' : 'User';
                                                    echo htmlspecialchars($role . ' ' . $name);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars((string)($l['note'] ?? '')); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Item Logs -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><?php echo _lang['item_logs'] ?? "لاگ‌های تیکت‌ها"; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo _lang['date'] ?? "تاریخ"; ?></th>
                                    <th><?php echo _lang['ticket'] ?? "تیکت"; ?></th>
                                    <th><?php echo _lang['action'] ?? "اکشن"; ?></th>
                                    <th><?php echo _lang['from'] ?? "از"; ?></th>
                                    <th><?php echo _lang['to'] ?? "به"; ?></th>
                                    <th><?php echo _lang['by'] ?? "توسط"; ?></th>
                                    <th><?php echo _lang['reason'] ?? "دلیل"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($itemLogs)): ?>
                                    <tr><td colspan="7"><?php echo _lang['no_data'] ?? "لاگی وجود ندارد"; ?></td></tr>
                                <?php else: ?>
                                    <?php foreach ($itemLogs as $l): ?>
                                        <tr>
                                            <td>
                                        <?php $dateConverter = new DateConverter($l['creation_date'], $config->getNowLanguage('a'));
                                        echo $dateConverter->convertToShamsi(); ?>
                                        </td>
                                            <td><?php echo htmlspecialchars((string)$l['ticket_number']); ?> </td>
                                            <td><?php echo htmlspecialchars($l['action']); ?></td>
                                            <td><?php echo htmlspecialchars((string)($l['from_priority'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars((string)($l['to_priority'] ?? '')); ?></td>
                                            <td>
                                                <?php
                                                    $name = $l['admin_name'] ?? $l['user_name'] ?? '';
                                                    $role = $l['admin_id'] ? 'Admin' : 'User';
                                                    echo htmlspecialchars($role . ' ' . $name);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars((string)($l['reason'] ?? '')); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Item Comments -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><?php echo _lang['item_comments'] ?? "کامنت‌های تیکت‌ها"; ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo _lang['date'] ?? "تاریخ"; ?></th>
                                    <th><?php echo _lang['ticket'] ?? "تیکت"; ?></th>
                                    <th><?php echo _lang['by'] ?? "توسط"; ?></th>
                                    <th><?php echo _lang['comment'] ?? "کامنت"; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($itemComments)): ?>
                                    <tr><td colspan="4"><?php echo _lang['no_data'] ?? "کامنتی وجود ندارد"; ?></td></tr>
                                <?php else: ?>
                                    <?php foreach ($itemComments as $c): ?>
                                        <tr>
                                            <td>
                                        <?php $dateConverter = new DateConverter($c['creation_date'], $config->getNowLanguage('a'));
                                        echo $dateConverter->convertToShamsi(); ?>
                                        </td>
                                            <td><?php echo htmlspecialchars((string)$c['ticket_number']); ?> </td>
                                            <td>
                                                <?php
                                                    $name = $c['admin_name'] ?? $c['user_name'] ?? '';
                                                    $role = $c['admin_id'] ? 'Admin' : 'User';
                                                    echo htmlspecialchars($role . ' ' . $name);
                                                ?>
                                            </td>
                                            <td><?php echo nl2br(htmlspecialchars($c['comment_text'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

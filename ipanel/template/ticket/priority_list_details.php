<?php
///template/ticket/priority_list_details.php
?>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <?php if (empty($list)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i><?php echo _lang['error'] ?? "خطا"; ?>:
                    <?php echo htmlspecialchars($flash['err'] ?? "اطلاعات لیست یافت نشد"); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <a href="./priority_list" class="btn btn-outline-secondary">
                    <?php echo _lang['back'] ?? "بازگشت"; ?>
                </a>
                <?php return; ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <a href="./priority_list" class="btn btn-sm btn-outline-secondary">
                                <?php echo _lang['back'] ?? "بازگشت"; ?>
                            </a>
                            <a href="./priority_list_logs?list_id=<?php echo (int) $list['id']; ?>"
                                class="btn btn-sm btn-outline-primary ms-1">
                                <?php echo _lang['logs_comments'] ?? "لاگ/کامنت"; ?>
                            </a>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['priority_list_details'] ?? "جزئیات لیست اولویت"; ?>
                            #<?php echo (int) $list['id']; ?>
                        </h4>
                    </div>
                </div>
            </div>

            <?php if (!empty($flash['ok'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-check-line me-2"></i><?php echo htmlspecialchars($flash['ok']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flash['err'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i><?php echo htmlspecialchars($flash['err']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- List info -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border">
                            <h5 class="card-title mb-3"><?php echo _lang['list_info'] ?? "اطلاعات لیست"; ?></h5>
                            <div class="row">
                                <div class="col-md-3"><b><?php echo _lang['company'] ?? "شرکت"; ?>:</b>
                                    <?php echo htmlspecialchars($companyName ?? ''); ?></div>

                                <div class="col-md-2"><b><?php echo _lang['group'] ?? "type_group"; ?>
                                        :</b>
                                    <?php echo htmlspecialchars($list['type_group']); ?></div>
                                <div class="col-md-2"><b><?php echo _lang['revision']; ?></b>
                                    <?php echo (int) $list['revision']; ?></div>
                                <div class="col-md-3">
                                    <b><?php echo _lang['status'] ?? "وضعیت"; ?>:</b>
                                    <?php if ($list['status'] === 'approved'): ?>
                                        <span class="badge bg-success"><?php echo _lang['confirmation']; ?></span>
                                    <?php elseif ($list['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger"><?php echo _lang['rejected']; ?></span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-warning text-dark"><?php echo _lang['need_confirmation']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approve/Reject if needs_approval -->
            <?php if (!empty($list) && $list['status'] === 'needs_approval'): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h5 class="card-title mb-3"><?php echo _lang['admin_actions'] ?? "عملیات مدیر"; ?></h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <form method="post">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="list_id" value="<?php echo (int) $list['id']; ?>">
                                            <label
                                                class="form-label"><?php echo _lang['optional_comment'] ?? "کامنت (اختیاری)"; ?></label>
                                            <textarea name="comment" class="form-control" rows="3"
                                                placeholder="<?php echo _lang['comment'] ?? "کامنت"; ?>"></textarea>
                                            <button type="submit" class="btn btn-success mt-2">
                                                <?php echo _lang['approve'] ?? "تایید"; ?>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="col-md-6">
                                        <form method="post">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="list_id" value="<?php echo (int) $list['id']; ?>">
                                            <label
                                                class="form-label"><?php echo _lang['reject_comment_required'] ?? "دلیل رد (اجباری)"; ?></label>
                                            <textarea name="comment" class="form-control" rows="3" required
                                                placeholder="<?php echo _lang['reason_for_reject'] ?? "علت رد / اصلاحات مورد نیاز"; ?>"></textarea>
                                            <button type="submit" class="btn btn-danger mt-2">
                                                <?php echo _lang['reject'] ?? "رد"; ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Items -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="card-title mb-3"><?php echo _lang['list_items'] ?? "آیتم‌های لیست"; ?></h5>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th><?php echo _lang['priority'] ?? "اولویت"; ?></th>
                                            <th><?php echo _lang['ticket_number'] ?? "شماره تیکت"; ?></th>
                                            <th><?php echo _lang['title'] ?? "شماره تیکت"; ?>
                                            </th>
                                            <th><?php echo _lang['status'] ?? "وضعیت"; ?></th>
                                            <th><?php echo _lang['finance'] ?? "Finance"; ?></th>
                                            <th><?php echo _lang['last_update_date'] ?? "آخرین تاریخ بروزرسانی"; ?>
                                            </th>
                                            <th><?php echo _lang['actions'] ?? "عملیات"; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($items)): ?>
                                            <tr>
                                                <td colspan="7"><?php echo _lang['no_data'] ?? "آیتمی وجود ندارد"; ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($items as $it):

                                                $encrypted_ticket_id = $encryptorClass->encrypt($it['ticket_id']);
                                                $lastFinanceStatus = $ticketModel->getLastFinanceStatus($it['ticket_id'], 'tickets');
                                                $lastNonFinanceStatus = $ticketModel->getLastNonFinanceStatus($it['ticket_id'], 'tickets');
                                                ?>
                                                <tr>
                                                    <td><?php echo (int) $it['priority']; ?></td>
                                                    <td>
                                                        <a
                                                            href="./tickets?ticket_id=<?php echo urlencode($encrypted_ticket_id); ?>">
                                                            <?php echo htmlspecialchars($it['ticket_number']); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a
                                                            href="./tickets?ticket_id=<?php echo urlencode($encrypted_ticket_id); ?>">
                                                            <?php echo htmlspecialchars($textToolsClass->truncateText($it['ticket_title'], 120)); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (count($lastNonFinanceStatus) > 0) {
                                                            $statusName = $lastNonFinanceStatus[0]["status_name"];

                                                            if (!isset($conditionCache[$statusName])) {
                                                                $conditionCache[$statusName] = $structureModel->getConditionsByName($statusName);
                                                            }

                                                            $conditionNonFinance = $conditionCache[$statusName];

                                                            echo '<span class="badge alert-' . $conditionNonFinance['condition_color'] . '">'
                                                                . _lang[$conditionNonFinance['condition_name']]
                                                                . '</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (count($lastFinanceStatus) > 0) {
                                                            $statusName = $lastFinanceStatus[0]["status_name"];

                                                            if (!isset($conditionCache[$statusName])) {
                                                                $conditionCache[$statusName] = $structureModel->getConditionsByName($statusName);
                                                            }

                                                            $conditionFinance = $conditionCache[$statusName];

                                                            echo '<span class="badge alert-' . $conditionFinance['condition_color'] . '">'
                                                                . _lang[$conditionFinance['condition_name']]
                                                                . '</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php $dateConverter = new DateConverter($it['last_updated_date'], $config->getNowLanguage('a'));
                                                    echo $dateConverter->convertToShamsi(); ?>
                                                    </td>
                                                    <td>
                                                        <!-- Change Priority Modal trigger -->
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="modal" data-bs-target="#globalChangePriorityModal"
                                                            data-list-id="<?php echo (int) $list['id']; ?>"
                                                            data-ticket-id="<?php echo (int) $it['ticket_id']; ?>"
                                                            data-ticket-number="<?php echo htmlspecialchars($it['ticket_number']); ?>"
                                                            data-priority="<?php echo (int) $it['priority']; ?>">
                                                            <?php echo _lang['change_priority'] ?? "تغییر اولویت"; ?>
                                                        </button>

                                                        <!-- Remove Modal trigger -->
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal" data-bs-target="#globalRemoveModal"
                                                            data-list-id="<?php echo (int) $list['id']; ?>"
                                                            data-ticket-id="<?php echo (int) $it['ticket_id']; ?>"
                                                            data-ticket-number="<?php echo htmlspecialchars($it['ticket_number']); ?>">
                                                            <?php echo _lang['remove'] ?? "حذف"; ?>
                                                        </button>

                                                    </td>
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
    </div>
</div>

<!-- Global Change Priority Modal -->
<div class="modal fade" id="globalChangePriorityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _lang['change_priority'] ?? "تغییر اولویت"; ?> <span
                        id="modalTicketNumber"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="post" id="changePriorityForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="change_priority">
                    <input type="hidden" name="list_id" id="formListId">
                    <input type="hidden" name="ticket_id" id="formTicketId">

                    <div class="mb-3">
                        <label class="form-label"><?php echo _lang['new_priority'] ?? "اولویت جدید"; ?></label>
                        <select name="new_priority" id="newPrioritySelect" class="form-select form-control" required>
                            <option value="">انتخاب کنید</option>
                            <?php
                            // دریافت اولویت‌های موجود در لیست
                            $availablePriorities = array_column($items, 'priority');
                            sort($availablePriorities);
                            foreach ($availablePriorities as $priority):
                                ?>
                                <option value="<?php echo (int) $priority; ?>">
                                    <?php echo (int) $priority; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo _lang['comment_required'] ?? "کامنت اجباری"; ?></label>
                        <textarea name="comment" class="form-control" rows="3" required
                            placeholder="<?php echo _lang['reason_for_change'] ?? "علت تغییر را بنویسید"; ?>"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?php echo _lang['cancel'] ?? "انصراف"; ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _lang['save'] ?? "ثبت"; ?></button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Global Remove Modal -->
<div class="modal fade" id="globalRemoveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _lang['remove_from_list'] ?? "حذف از لیست"; ?> <span
                        id="modalRemoveTicketNumber"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="post" id="removeForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="list_id" id="removeListId">
                    <input type="hidden" name="ticket_id" id="removeTicketId">

                    <div class="mb-3">
                        <label class="form-label"><?php echo _lang['comment_required'] ?? "کامنت اجباری"; ?></label>
                        <textarea name="comment" class="form-control" rows="3" required
                            placeholder="<?php echo _lang['reason_for_removal'] ?? "علت حذف را بنویسید"; ?>"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?php echo _lang['cancel'] ?? "انصراف"; ?></button>
                    <button type="submit" class="btn btn-danger"><?php echo _lang['remove'] ?? "حذف"; ?></button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Change Priority Modal - Listen to show event
        const changePriorityModal = document.getElementById('globalChangePriorityModal');
        if (changePriorityModal) {
            changePriorityModal.addEventListener('show.bs.modal', function (e) {
                const button = e.relatedTarget;
                if (button) {
                    document.getElementById('formListId').value = button.getAttribute('data-list-id');
                    document.getElementById('formTicketId').value = button.getAttribute('data-ticket-id');
                    document.getElementById('modalTicketNumber').textContent = '#' + button.getAttribute('data-ticket-number');
                    document.getElementById('newPrioritySelect').value = '';
                }
            });
        }

        // Remove Modal - Listen to show event
        const removeModal = document.getElementById('globalRemoveModal');
        if (removeModal) {
            removeModal.addEventListener('show.bs.modal', function (e) {
                const button = e.relatedTarget;
                if (button) {
                    document.getElementById('removeListId').value = button.getAttribute('data-list-id');
                    document.getElementById('removeTicketId').value = button.getAttribute('data-ticket-id');
                    document.getElementById('modalRemoveTicketNumber').textContent = '#' + button.getAttribute('data-ticket-number');
                }
            });
        }
    });
</script>
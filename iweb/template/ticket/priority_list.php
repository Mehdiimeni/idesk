<?php
///template/ticket/priority_list.php
?>

<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">
                                    <?php echo _lang['priority_list']; ?>
                                </li>
                            </ol>
                        </div>
                        <h4 class="page-title">
                            <?php echo _lang['priority_list']; ?>
                        </h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- TypeGroup Selector Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-xl-6">
                                        <div class="mb-3 mt-3 mt-xl-0">
                                <label for="typeGroupSelector" class="form-label fw-bold"><?php echo _lang['select_typegroup']; ?></label>
                                <select id="typeGroupSelector" onchange="changeTypeGroup(this.value)" class="form-select form-control">
                                    <?php
                                    $currentTypeGroup = $typeGroup ?? ($listOfTypeGroup[0] ?? '');
                                    foreach ($listOfTypeGroup as $tg):
                                        ?>
                                        <option value="<?php echo htmlspecialchars($tg); ?>" <?php echo ($tg === $currentTypeGroup) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($tg); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

   

            <script>
                function changeTypeGroup(value) {
                    window.location.href = '?type_group=' + encodeURIComponent(value);
                }
            </script>

            <!-- Flash Messages -->
            <?php if (!empty($flash['ok'])): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-check-line me-2"></i><?php echo htmlspecialchars($flash['ok']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($flash['err'])): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-line me-2"></i><?php echo htmlspecialchars($flash['err']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- List Status Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border">
                            <h5 class="card-title mb-3"><?php echo _lang['list_status']; ?></h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <?php if ($list['status'] === 'approved'): ?>
                                            <span class="badge bg-success p-2">
                                                <?php echo _lang['approved']; ?>
                                            </span>
                                        <?php elseif ($list['status'] === 'rejected'): ?>
                                            <span class="badge bg-danger p-2">
                                                <?php echo _lang['rejected']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning p-2">
                                                <?php echo _lang['pending_approval']; ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <span class="badge bg-info p-2"><?php echo _lang['revision']; ?> <?php echo (int) $list['revision']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items List Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border">
                            <h5 class="card-title mb-4"><?php echo _lang['tickets_in_list']; ?></h5>

                            <?php if (count($items) === 0): ?>
                                <div class="alert alert-info" role="alert">
                                    <i class="ri-information-line me-2"></i><?php echo _lang['no_tickets_yet']; ?>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th><?php echo _lang['priority']; ?></th>
                                                <th><?php echo _lang['ticket_number']; ?></th>
                                                <th><?php echo _lang['title']; ?></th>
                                                <th><?php echo _lang['ticket_status']; ?></th>
                                                <th><?php echo _lang['actions']; ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $it): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo (int) $it['priority']; ?></span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($it['ticket_number']); ?></td>
                                                    <td><?php echo htmlspecialchars(
                                                    $textToolsClass->truncateText($it['ticket_title'], 140)); ?>
                                                    </td>
                                                    <td>

                                                    <?php $condition = $structureModel->getConditionsByName($it['ticket_status']); ?>
                                                    <span class="alert alert-<?php echo $condition['condition_color']; ?> rounded-pill">
                                                        <?php echo _lang[$condition['condition_name']]; ?>
                                                    </span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePriorityModal<?php echo (int) $it['ticket_id']; ?>">
                                                                    <?php echo _lang['change_priority']; ?>
                                                                </a>
                                                                <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#removeModal<?php echo (int) $it['ticket_id']; ?>">
                                                                    <?php echo _lang['remove_from_list']; ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Change Priority Modal -->
                                                <div class="modal fade" id="changePriorityModal<?php echo (int) $it['ticket_id']; ?>" tabindex="-1" aria-labelledby="changePriorityLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="changePriorityLabel"><?php echo _lang['change_priority']; ?> #<?php echo htmlspecialchars($it['ticket_number']); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="post" action="?type_group=<?php echo urlencode($typeGroup); ?>&action=change_priority">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="ticket_id" value="<?php echo (int) $it['ticket_id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label for="priority<?php echo (int) $it['ticket_id']; ?>" class="form-label"><?php echo _lang['new_priority']; ?></label>
                                                                        <select name="new_priority" id="priority<?php echo (int) $it['ticket_id']; ?>" class="form-select" required>
                                                                            <?php for ($p = 1; $p <= 5; $p++): ?>
                                                                                <option value="<?php echo $p; ?>" <?php echo ($p == (int) $it['priority']) ? 'selected' : ''; ?>>
                                                                                    <?php echo $p; ?>
                                                                                </option>
                                                                            <?php endfor; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="comment<?php echo (int) $it['ticket_id']; ?>" class="form-label"><?php echo _lang['comment_required']; ?></label>
                                                                        <textarea name="comment" id="comment<?php echo (int) $it['ticket_id']; ?>" class="form-control" rows="3" placeholder="<?php echo _lang['change_priority_reason']; ?>" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo _lang['cancel']; ?></button>
                                                                    <button type="submit" class="btn btn-primary"><?php echo _lang['save_changes']; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Remove Modal -->
                                                <div class="modal fade" id="removeModal<?php echo (int) $it['ticket_id']; ?>" tabindex="-1" aria-labelledby="removeLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="removeLabel"><?php echo _lang['remove_from_list']; ?> #<?php echo htmlspecialchars($it['ticket_number']); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="post" action="?type_group=<?php echo urlencode($typeGroup); ?>&action=remove">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="ticket_id" value="<?php echo (int) $it['ticket_id']; ?>">
                                                                    <p class="mb-3"><?php echo _lang['confirm_remove']; ?></p>
                                                                    <div class="mb-3">
                                                                        <label for="removeComment<?php echo (int) $it['ticket_id']; ?>" class="form-label"><?php echo _lang['comment_required']; ?></label>
                                                                        <textarea name="comment" id="removeComment<?php echo (int) $it['ticket_id']; ?>" class="form-control" rows="3" placeholder="<?php echo _lang['reason_for_removal']; ?>" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo _lang['cancel']; ?></button>
                                                                    <button type="submit" class="btn btn-danger"><?php echo _lang['remove_ticket']; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border">
                            <h5 class="card-title mb-4"><?php echo _lang['comments']; ?></h5>

                            <?php if (empty($listComments)): ?>
                                <div class="alert alert-info" role="alert">
                                    <i class="ri-information-line me-2"></i><?php echo _lang['no_comments_yet']; ?>
                                </div>
                            <?php else: ?>
                                <div class="comments-timeline">
                                    <?php foreach ($listComments as $comment): ?>
                                        <div class="comment-item mb-4 pb-3 border-bottom">
                                            <div class="d-flex gap-3">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-2">
                                                                <?php if ($comment['author_type'] === 'admin'): ?>
                                                                    <span class="badge bg-danger me-2">
                                                                        <i class="ri-admin-line me-1"></i><?php echo _lang['admin']; ?>
                                                                    </span>
                                                                <?php elseif ($comment['author_type'] === 'user'): ?>
                                                                    <span class="badge bg-info me-2">
                                                                        <i class="ri-user-line me-1"></i><?php echo _lang['user']; ?>
                                                                    </span>
                                                                
                                                                <?php endif; ?>
                                                                <span class="fw-bold"><?php echo htmlspecialchars($comment['author_name']); ?></span>
                                                            </h6>
                                                            <div class="alert alert-light border mb-2">
                                                                <p class="mb-1">
                                                                    <i class="ri-chat-3-line text-primary me-2"></i>
                                                                    <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                                                                </p>
                                                                <small class="text-muted d-block mt-2">
                                                                    <i class="ri-calendar-line me-1"></i>
                                                                    
                                                                    <?php $dateConverter = new DateConverter($comment['creation_date'], $config->getNowLanguage('a'));
                                                                    echo $dateConverter->convertToShamsi(); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add New Ticket Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body border">
                            <h5 class="card-title mb-4"><?php echo _lang['add_new_ticket']; ?></h5>

                            <?php if (count($items) >= 5): ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="ri-alert-line me-2"></i><?php echo _lang['list_is_full']; ?>
                                </div>
                            <?php else: ?>
                                <?php if (empty($selectableTickets)): ?>
                                    <div class="alert alert-info" role="alert">
                                        <i class="ri-information-line me-2"></i><?php echo _lang['no_selectable_tickets']; ?>
                                    </div>
                                <?php else: ?>
                                    <form method="post" action="?type_group=<?php echo urlencode($typeGroup); ?>&action=add">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="ticket_id" class="form-label"><?php echo _lang['select_ticket']; ?></label>
                                                <select name="ticket_id" id="ticket_id" class="select2 form-control select2-multiple"
                                                data-toggle="select2"  required>
                                                    <option value="">-- <?php echo _lang['select_ticket']; ?> --</option>
                                                    <?php foreach ($selectableTickets as $t): ?>
                                                        <option value="<?php echo (int) $t['ticket_id']; ?>">
                                                            # <?php echo htmlspecialchars($t['ticket_number']); ?> - <?php echo htmlspecialchars($textToolsClass->truncateText($t['ticket_title'], 140) ?? ''); ?>
                                                            
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="priority" class="form-label"><?php echo _lang['priority']; ?></label>
                                                <select name="priority" id="priority" class="form-select" required>
                                                    <option value="">-- <?php echo _lang['select_ticket']; ?> --</option>
                                                    <?php 
                                                        $usedPriorities = array_column($items, 'priority');
                                                        for ($p = 1; $p <= 5; $p++): 
                                                            if (!in_array($p, $usedPriorities)):
                                                    ?>
                                                        <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                                                    <?php 
                                                            endif;
                                                        endfor; 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment" class="form-label"><?php echo _lang['comment_required']; ?></label>
                                            <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="<?php echo _lang['comment_placeholder_add']; ?>" required></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-add-line me-1"></i><?php echo _lang['submit_for_approval']; ?>
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- end container-fluid -->
    </div> <!-- end content -->
</div> <!-- end content-page -->
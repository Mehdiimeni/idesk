<?php
///template/ticket/priority_list.php
?>


<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active"><?php echo _lang['priority_list_admin'] ?? "مدیریت لیست‌های اولویت"; ?></li>
                            </ol>
                        </div>
                        <h4 class="page-title"><?php echo _lang['priority_list_admin'] ?? "مدیریت لیست‌های اولویت"; ?></h4>
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

            <!-- Filters -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form method="get" class="row g-3">
                                <input type="hidden" name="action" value="list">

                                <div class="col-md-3">
                                    <label class="form-label"><?php echo _lang['company'] ?? "Company ID"; ?></label>
                                    <select name="company_id" class="form-select form-control">
                                        <option value=""><?php echo _lang['all'] ?? "همه"; ?></option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?php echo (int) $company['company_id']; ?>"
                                                <?php echo (($_GET['company_id'] ?? '') === (string) $company['company_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($company['company_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label"><?php echo _lang['group'] ?? "Type Group"; ?></label>
                                    <select name="type_group" class="form-select form-control">
                                        <option value=""><?php echo _lang['all'] ?? "همه"; ?></option>
                                        <?php foreach ($typeGroups as $tg): ?>
                                            <option value="<?php echo htmlspecialchars($tg); ?>"
                                                <?php echo (($_GET['type_group'] ?? '') === $tg) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tg); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label"><?php echo _lang['status'] ?? "Status"; ?></label>
                                    <select name="status" class="form-select form-control">
                                        <option value=""><?php echo _lang['all'] ?? "همه"; ?></option>
                                        <option value="needs_approval" <?php echo (($_GET['status'] ?? '') === 'needs_approval') ? 'selected' : ''; ?>>
                                            <?php echo _lang['need_confirmation']; ?>
                                        </option>
                                        <option value="rejected" <?php echo (($_GET['status'] ?? '') === 'rejected') ? 'selected' : ''; ?>>
                                            <?php echo _lang['rejected']; ?>
                                        </option>
                                        <option value="approved" <?php echo (($_GET['status'] ?? '') === 'approved') ? 'selected' : ''; ?>>
                                            <?php echo _lang['confirmation']; ?>
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" type="submit"><?php echo _lang['filter'] ?? "فیلتر"; ?></button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- List table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="card-title mb-3"><?php echo _lang['lists'] ?? "لیست‌ها"; ?></h5>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo _lang['company'] ?? "شرکت"; ?></th>
                                            <th><?php echo _lang['group'] ?? "type_group"; ?></th>
                                            <th><?php echo _lang['status'] ?? "status"; ?></th>
                                            <th><?php echo _lang['items'] ?? "items"; ?></th>
                                            <th><?php echo _lang['last_uapdate_date'] ?? "updated"; ?></th>
                                            <th><?php echo _lang['actions'] ?? "actions"; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($lists)): ?>
                                        <tr><td colspan="8"><?php echo _lang['no_data'] ?? "موردی وجود ندارد"; ?></td></tr>
                                    <?php else: ?>
                                        <?php foreach ($lists as $row): ?>
                                            <tr>
                                                <td><?php echo (int)$row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['company_name'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['type_group']); ?></td>
                                                <td>
                                                    <?php if ($row['status'] === 'approved'): ?>
                                                        <span class="badge bg-success"><?php echo _lang['confirmation']; ?></span>
                                                        <?php elseif ($row['status'] === 'rejected'): ?>
                                                            <span class="badge bg-danger">
                                                                <?php echo _lang['rejected']; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark"><?php echo _lang['need_confirmation']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo (int)$row['items_count']; ?>/5</td>
                                                <td>
                                            <?php $dateConverter = new DateConverter($row['last_updated_date'], $config->getNowLanguage('a'));
                                            echo $dateConverter->convertToShamsi(); ?>
                                            
                                            </td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary"
                                                       href="./priority_list_details?list_id=<?php echo (int)$row['id']; ?>">
                                                        <?php echo _lang['view'] ?? "مشاهده"; ?>
                                                    </a>

                                                    <a class="btn btn-sm btn-outline-secondary"
                                                       href="./priority_list_logs?list_id=<?php echo (int)$row['id']; ?>">
                                                        <?php echo _lang['logs_comments'] ?? "لاگ/کامنت"; ?>
                                                    </a>
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

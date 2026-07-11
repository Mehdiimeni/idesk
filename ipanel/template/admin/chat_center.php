<?php
///template/admin/chat_center.php
?>
<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">

                        <h4 class="page-title">
                            <?php echo _lang['chat_center']; ?>
                        </h4>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row bg-white">
                <!-- بخش چپ: لیست شرکت‌ها و اعضا -->
                <div class="col-md-3 bg-secondary text-white">
                    <div class="p-1">
                        <p><?php echo (_lang['chat_center_note']); ?></p>
                        <hr class="text-white">
                        <h5 class="mt-3"><?php echo (_lang['list']); ?></h5>
                        <ul class="side-nav">
                            <?php foreach ($companies as $companyId => $companyData) { ?>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#sidebarCompany<?php echo $companyId; ?>"
                                        aria-expanded="false" aria-controls="sidebarCompany<?php echo $companyId; ?>"
                                        class="side-nav-link text-white">
                                        <i class="mdi mdi-book-variant"></i>
                                        <span> <?php echo $companyData['company_name']; ?> </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="sidebarCompany<?php echo $companyId; ?>">
                                        <ul class="side-nav-second-level">
                                            <?php foreach ($companyData['members'] as $member) {

                                                if ($member['member_id'] == $_SESSION['admin_id'] && $member['member_type'] == 'a') {
                                                    continue;
                                                }
                                                ?>
                                                <li>
                                                    <a href="./chat_center?rId=<?php echo $member['member_id']; ?>&rt=<?php echo $member['member_type']; ?>"
                                                        class="text-white">
                                                        <?php echo $member['name']; ?>
                                                        (<?php echo $member['unit_name']; ?>)</a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <!-- بخش میانی: نمایش چت -->
                <div class="col-md-6">
                    <?php if ($showChatBox) { ?>
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><?php echo (_lang['chat']); ?></h5>
                            </div>
                            <div class="col-xxl-12 col-xl-12 order-xl-2">
                                <div class="card">
                                    <div class="card-body px-0 pb-0">
                                        <ul class="conversation-list px-3" data-simplebar
                                            style="max-height: 450px; overflow-y: auto;">
                                            <!-- پیام‌ها به صورت داینامیک لود می‌شوند -->
                                        </ul>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="message-input"
                                                    placeholder="Enter your text" required>
                                                <input type="hidden" id="receiver-id" value="<?php echo ($receiver_id); ?>">
                                                <input type="hidden" id="receiver-type"
                                                    value="<?php echo ($receiver_type); ?>">
                                                <input type="hidden" id="sender-id"
                                                    value="<?php echo ($_SESSION['admin_id']); ?>">
                                                <input type="hidden" id="admin-id"
                                                    value="<?php echo ($_SESSION['admin_id']); ?>">
                                                <input type="hidden" id="sender-type" value="a">
                                                <button class="btn btn-success" id="send-button" type="button">
                                                    <i class="uil uil-message"></i>
                                                </button>
                                                <div class="invalid-feedback"><?php echo (_lang['write_message']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>




                <!-- بخش راست: نمایش جزئیات کاربر -->
                <div class="col-md-3 ">
                    <?php if ($showChatBox) { ?>
                        <div class="card">
                            <div class="card-body text-center">

                                <h4 class="mt-3"><?php echo $receiverDetials['name']; ?></h4>

                                <hr>
                                <p class="mt-4 mb-1"><strong><?php echo (_lang['email']); ?>:</strong></p>
                                <p><?php echo $receiverDetials['email']; ?></p>

                                <p class="mt-3 mb-1"><strong><?php echo (_lang['phone']); ?>:</strong></p>
                                <p><?php echo $receiverDetials['company_phone']; ?></p>

                                <p class="mt-3 mb-1"><strong><?php echo (_lang['company']); ?>:</strong></p>
                                <p><?php echo $receiverDetials['company_name']; ?></p>


                                <p class="mt-3 mb-1"><strong><?php echo (_lang['unit']); ?>:</strong></p>
                                <p><span
                                        class="badge badge-success-lighten"><?php echo $receiverDetials['unit_name']; ?></span>
                                </p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
</div>
# IPanel / IWeb Access Redesign - Progress

## اصل ایمنی
- جداول مشترک فعلی حفظ می‌شوند.
- Routeهای Legacy تغییر نمی‌کنند مگر با تأیید جداگانه.
- منطق جاری CRUD و RBAC در هر فاز باید Regression Test شود.
- هر بخش جداگانه تکمیل، تست و تأیید می‌شود.

## وضعیت فازها
- [x] Phase 1 - `ipanel/user_access` - بازطراحی نقش‌ها و تفکیک نمایشی iPanel / iWeb
- [x] Phase 2 - `ipanel/user_stracture` - بازطراحی ساختار دسترسی
- [x] Phase 3 - `ipanel/user_operation` - بازطراحی دسترسی Group/Part/Subpart و امن‌سازی `permissions_operation.parts`
- [x] Phase 4 - `ipanel/user_view` - مدیریت امن Operation Permissionها (`permissions_operation.operation`) + تعریف صریح Ticket Send Scope
- [x] Phase 4.5 - `ipanel/tickets` - Backend Ticket Visibility Scope (Send-All / Manager Subtree / Expert Own)
- [ ] Phase 5 - هماهنگ‌سازی منوهای Access / Systems
- [ ] Phase 6 - صفحه چارت سازمانی مدیران و افراد هر واحد
- [x] Phase 7 - اعمال Backend Rule گیرنده مجاز تیکت در Ticket Detail / Forward

## Ruleهای قطعی برای Phase 3 / 7
- Permission جدید Scope ارسال باید با جداول موجود `operations` و `permissions_operation` طراحی شود؛ تا قبل از تأیید SQL اجرا نشود.
- مدیر بدون Scope عمومی: گیرنده مجاز = زیرشاخه خودش + اشخاص دارای Scope «ارسال به همه».
- مدیر دارای Scope عمومی: می‌تواند به تمام گیرندگان مجاز سیستم ارجاع دهد.
- محدودسازی فقط در UI کافی نیست و Backend نیز باید Rule را enforce کند.
- منطق Customer Portal / iWeb جدید و Anti-Automation مستقل از iPanel باقی می‌ماند.


## تصمیم‌های قطعی Phase 2 - user_stracture
- Structure Access فقط Unit ID ذخیره می‌کند؛ Company و Activity از Unit/Company مشتق می‌شوند.
- Role مورد استفاده در iWeb فقط Unitهای Company خودش را می‌گیرد.
- Role داخلی iPanel می‌تواند Multi-Company Structure Scope داشته باشد.
- Full Access Role نیاز به Structure Mapping ندارد.
- Unit اصلی Role در Save عادی حفظ می‌شود.
- Revoke کامل با `delete_operation` و بدون Physical Delete انجام می‌شود.
- Generic CRUD روی `permissions_stracture` مسدود است.


## تصمیم‌های قطعی Phase 3 - user_operation
- Route Legacy حفظ می‌شود اما مسئولیت صفحه `permissions_operation.parts` است.
- ستون `operation` در این صفحه تغییر نمی‌کند و در `user_view` مدیریت می‌شود.
- Role فاقد رکورد نیز قابل Setup است: اولین Save = INSERT، بعدی = UPDATE.
- Revoke فقط parts را خالی می‌کند؛ Physical Delete روی رکورد مشترک ممنوع است.
- Scope IPanel/IWeb در Backend اعتبارسنجی می‌شود.
- Generic CRUD/Status و Legacy mutation endpointها نمی‌توانند Endpoint امن را دور بزنند.


## تصمیم‌های قطعی Phase 4 - user_view
- `user_view` فقط ستون `permissions_operation.operation` را مدیریت می‌کند؛ `parts/status` دست‌نخورده می‌مانند.
- تمام RBACها حتی بدون رکورد Permission قابل مشاهده/Setup هستند؛ اولین Save در نبود رکورد INSERT و در وجود رکورد UPDATE است.
- Full Access (`all_rbac=1`) از محدودسازی این صفحه محافظت می‌شود چون Runtime ماتریس Operation را دور می‌زند.
- `ticket_send_all_operation` یک انتخاب صریح و حساس است: نبود آن = Own Subtree + گیرندگان دارای Send-All، وجود آن = همه گیرندگان مجاز.
- Select All مجوزهای حساس Add/Edit/Delete/Active/Inactive و Send-All را خودکار فعال نمی‌کند.
- Revoke کامل فقط با `delete_operation` و خالی‌کردن `operation` انجام می‌شود؛ Physical Delete رکورد مشترک ممنوع است.
- Duplicateهای Legacy در Read هشدار داده می‌شوند و Save فقط ستون `operation` همه رکوردهای Role را همگام می‌کند.
- Read Runtime کلاس RBAC برای Permission Rowهای تکراری روی `ORDER BY id ASC LIMIT 1` قطعی شد تا Canonical Row با UI یکسان باشد.
- Customer Portal Business Permissionها از این صفحه جدا هستند و Default Deny باقی می‌مانند.
- Endpoint Legacy `add_operation.php` مسدود و Mutation فقط از Endpoint دارای Part Permission + Operation Permission + CSRF انجام می‌شود.
- محدودیت گیرنده در Forward Backend و UI هر دو اعمال می‌شوند؛ UI به تنهایی Authorization محسوب نمی‌شود.


## تصمیم‌های قطعی Ticket Visibility - ipanel/tickets
- `ticket_send_all_operation` یا `all_rbac=1` = مشاهده تمام تیکت‌ها در Route اصلی Tickets.
- بدون Send-All، Scope از `rbac.manager_id` به صورت Recursive استخراج می‌شود.
- Manager = خودش + Adminهای تمام Descendant Roleها؛ Expert/Leaf = فقط خودش؛ Adminهای هم‌Role Peer هستند و Subtree محسوب نمی‌شوند.
- Caption نقش مبنای Authorization نیست.
- `pointer_operation` دیگر Global Visibility ایجاد نمی‌کند.
- `referred=1` History ارسال شخصی باقی می‌ماند؛ `referred=0` Inbox جاری/تیمی است.
- DataTables Endpoint و Ticket Detail هر دو Backend Enforcement دارند.
- Current Owner فقط از آخرین Forward واقعی با `receiver_type='a'` تعیین می‌شود.
- Visibility و مقصد مجاز Forward اکنون هر دو Backend Enforcement دارند.


## تصمیم‌های قطعی Phase 7 - Ticket Assignment Scope
- `assign_other_operation` فقط مجوز انجام Action تخصیص است و معیار قابل‌دریافت‌بودن شخص مقصد نیست.
- دارنده `ticket_send_all_operation` یا `all_rbac=1` می‌تواند به همه Adminهای Active به‌جز خودش تخصیص دهد.
- بدون Send-All، مقصد مجاز = Adminهای Active در RBAC Subtree خود + Adminهای Active که خودشان Send-All/Full Access دارند.
- Peer هم‌Role فقط در صورت داشتن Send-All مقصد مجاز است؛ هم‌Role بودن به‌تنهایی Subtree محسوب نمی‌شود.
- لیست UI از همان Allowed-ID Set ساخته می‌شود که Backend POST با آن Validate می‌شود.
- POST دستکاری‌شده با Receiver خارج Scope رد می‌شود و `section_element_id/section_part_name` نیز با Ticket جاری تطبیق داده می‌شود.
- `pointer_operation` هیچ Bypass برای Ticket Detail یا Assignment Scope ایجاد نمی‌کند.
- هیچ Table/View/SP/Event/Index/SQL جدیدی اضافه نشد.

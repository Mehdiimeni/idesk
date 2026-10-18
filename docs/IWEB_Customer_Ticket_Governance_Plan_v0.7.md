# برنامه توسعه مرحله‌ای IWEB برای ساختار شرکت، نقش‌ها و کنترل ارسال تیکت به INTEK

**نسخه:** 0.7  
**تاریخ:** 1405/07/27  
**وضعیت:** Phase 1 - چهار جدول Additive ساخته شده / قواعد Phase 2 و Phase 3 به‌روزرسانی شده / بازطراحی مدیریتی IPanel مرحله‌ای در حال اجرا (Activity + Company + Unit) / بدون تغییر مخرب در جداول Legacy  
**پروژه مبنا:** `icore.zip` + `back261017.sql`

> **هشدار مهم:** این سند در نسخه فعلی فقط برنامه طراحی و اجرای مرحله‌ای است. در این مرحله هیچ SQL، Migration یا تغییر کدی روی Production نباید اجرا شود.

## 0.1 مبنای فنی قطعی پروژه

از نسخه 0.2، موارد زیر به عنوان مبنای فنی قطعی پروژه در نظر گرفته می‌شوند:

- دیتابیس عملیاتی: **MariaDB 11.3.2** با سازگاری MySQL.
- فایل Baseline فعلی دیتابیس: `back261017.sql`.
- Backend: **PHP**.
- UI: **Bootstrap**.
- Engine جداول جدید: `InnoDB`.
- Character Set جداول جدید: `utf8mb4`.
- Collation جداول جدید: `utf8mb4_general_ci`.
- روی جداول جدید پروژه می‌توان Index موردنیاز ایجاد کرد.
- روی جداول موجود/Legacy هیچ Index جدیدی در این پروژه اضافه نمی‌شود، مگر با تصمیم جداگانه و تأیید صریح.
- جداول موجود به دلیل تغییر دائمی داده‌های Production نباید با Dump محیط توسعه Replace شوند.
- انتقال نسخه‌های دیتابیس باید به صورت Additive و با Scriptهای مستقل برای آبجکت‌های جدید انجام شود.
- تمام مراحل طراحی و اجرا باید **مرحله‌به‌مرحله** انجام شوند؛ بعد از پایان هر مرحله، ادامه کار فقط پس از تأیید صریح انجام می‌شود.

## 0.2 قرارداد نام‌گذاری جداول جدید

تمام جداول جدید این پروژه باید Prefix کامل و مشخص زیر را داشته باشند:

`iweb_customer_portal_`

هدف این قرارداد:

1. جداول جدید در دیتابیس فعلی با جداول Legacy قاطی نشوند.
2. بتوان تمام جداول پروژه Customer Portal را مستقل Export کرد.
3. برای استقرار نسخه جدید نیازی به Replace کردن جداول فعلی و داده‌های در حال تغییر آنها نباشد.
4. تشخیص مالکیت و Scope هر جدول جدید از روی نام آن واضح باشد.

نمونه نام‌های مصوب:

- `iweb_customer_portal_sections`
- `iweb_customer_portal_user_profiles`
- `iweb_customer_portal_ticket_drafts`
- `iweb_customer_portal_ticket_audit_logs`
- `iweb_customer_portal_ticket_views`
- `iweb_customer_portal_ticket_publish_map`

این Prefix برای تمام جداول جدید این پروژه باید حفظ شود.

---

## 0.3 قواعد قطعی بازطراحی مدیریتی، دسترسی و حذف امن

این بخش از نسخه 0.5 برای تمام صفحات مدیریتی **IPanel** و صفحات مدیریتی سمت شرکت در **IWeb** الزام‌آور است.

### 0.3.1 Permission برای هر Action

هر Action که باعث یکی از موارد زیر شود باید در **Backend** دارای Permission صریح باشد:

- Insert / Create
- Update / Edit
- Delete
- Active / Inactive
- Assign / Unassign
- Forward / Send
- Grant / Revoke
- Publish / Cancel
- هر تغییر دیگری که داده یا Business State را تغییر دهد

قواعد:

1. مخفی‌کردن دکمه در UI به‌تنهایی Authorization محسوب نمی‌شود.
2. Permission باید در Endpoint / Controller مجدداً بررسی شود.
3. Scope سازمانی مانند Company / Unit / Section باید در Backend اعتبارسنجی شود.
4. Action بدون Permission حتی با POST مستقیم باید رد شود.
5. در IPanel برای CRUD عمومی، Permissionهای موجود `add_operation`, `edit_operation`, `delete_operation` فقط زمانی معتبرند که Backend علاوه بر آنها دسترسی همان Subpart را نیز کنترل کند.
6. برای Business Actionهای خاص باید Permission اختصاصی تعریف شود.

### 0.3.2 منع حذف رکورد دارای زیرمجموعه یا وابستگی

هر رکورد مدیریتی که زیرمجموعه یا وابستگی فعال/Business Dependency دارد، از داخل Application **قابل حذف نیست؛ حتی اگر Actor مجوز Delete داشته باشد**.

نمونه‌ها:

- Activity متصل به Company یا RBAC Role
- Company دارای Unit/User/Manager/Role/Ticket وابسته
- Unit دارای User/Admin/RBAC یا سایر وابستگی‌های عملیاتی
- Role/RBAC که به User/Admin یا Permission/Workflow متصل است
- هر ساختار دیگری که حذف آن باعث Orphan، اختلال Business یا Regression شود

قاعده نهایی:

`Delete Permission != Permission To Break Dependencies`

اگر حذف چنین رکوردی واقعاً لازم باشد، فقط **مدیر سامانه / INTEK** پس از بررسی Impact و Backup می‌تواند تصمیم به حذف مستقیم از Database بگیرد. این مسیر عمداً از UI ارائه نمی‌شود.

### 0.3.3 استفاده از ساختار موجود قبل از ساخت Database Object جدید

در هر بخش ابتدا باید بررسی شود که آیا Table/View/Query موجود نیاز Business را پوشش می‌دهد یا خیر.

ترتیب ترجیح:

1. استفاده از Table و Relationهای موجود
2. Query/Model Method جدید روی ساختار موجود
3. فقط در صورت نیاز واقعی: View
4. فقط در صورت نیاز واقعی: Stored Procedure
5. فقط در صورت نیاز واقعی: Event
6. فقط در صورت نیاز واقعی: Table جدید
7. Index جدید فقط طبق قواعد Baseline و با Approval جداگانه

برای Legacy Tableها اصل عدم ایجاد Index جدید بدون تأیید صریح همچنان برقرار است.

### 0.3.4 Dictionary و بسته‌های Patch

تمام Captionهای جدید باید در سه زبان `fa/en/ar` از Dictionary خوانده شوند.

از این نسخه به بعد:

- فایل‌های کامل و Merge‌شده `icore/lang/fa.php`, `icore/lang/en.php`, `icore/lang/ar.php` داخل همان Patch قرار می‌گیرند.
- Dictionary Fragment جداگانه برای Patch کد تحویل داده نمی‌شود.
- فایل جدا فقط برای Scriptهای Database یا مستند مستقل مجاز است.
- هیچ Patch نباید فایل Lang موجود را با نسخه ناقص جایگزین کند یا Keyهای قبلی را حذف کند.

### 0.3.5 تفکیک Unit و Customer Portal Section

ساختار Legacy و Customer Portal مستقل باقی می‌مانند:

```text
IPanel / Legacy
Company
  -> Unit
      -> Unit Manager(s)
      -> Unit Member(s)

IWeb Customer Portal
Company
  -> Customer Portal Section
      -> Manager
      -> Expert
```

`Unit` و `Customer Portal Section` نباید با یکدیگر جایگزین یا از نظر معنایی یکی شوند.

### 0.3.6 Scope ارسال تیکت برای مدیران IPanel

Permission اختصاصی فعلی برای Scope عمومی ارسال:

`ticket_send_all_operation`

قواعد:

- اگر Manager این Permission را نداشته باشد، گیرندگان مجاز او شامل افراد زیرشاخه خودش **به‌علاوه افرادی است که خودشان Scope «ارسال به همه» دارند**.
- اگر Manager این Permission را داشته باشد، می‌تواند به تمام گیرندگان مجاز سیستم ارسال/Forward کند.
- این Rule باید هم در لیست گیرندگان UI و هم در Backend Forward enforce شود.
- نبود Permission حالت Default و محدود است.

### 0.3.7 تصمیم Activity Management

برای `ipanel/activity` در نسخه فعلی:

- Table جدید لازم نیست.
- از `activities`, `company_profiles`, `rbac` استفاده می‌شود.
- Add فقط با `add_operation` + دسترسی Subpart Activity.
- Edit فقط با `edit_operation` + دسترسی Subpart Activity.
- Delete فقط با `delete_operation` + دسترسی Subpart Activity **و فقط در صورت صفر بودن تمام وابستگی‌ها**.
- Activity دارای Company یا Role وابسته از UI هرگز حذف نمی‌شود.
- Generic CRUD برای Table `activities` نباید بتواند Endpoint امن Activity را دور بزند.

### 0.3.8 تصمیم Company Management

برای `ipanel/company` در نسخه فعلی:

- Table/View/SP/Event/Index جدید لازم نیست و از ساختار Legacy موجود استفاده می‌شود.
- مرجع اصلی Company همان `company_profiles` باقی می‌ماند.
- Activity شرکت از `company_profiles.activity_id -> activities.id` استفاده می‌کند.
- Add فقط با `add_operation` + دسترسی Subpart Company.
- Edit فقط با `edit_operation` + دسترسی Subpart Company.
- Delete فقط با `delete_operation` + دسترسی Subpart Company و فقط وقتی **هیچ Table پایه دارای `company_id` رکورد وابسته به آن Company نداشته باشد**.
- بررسی Delete در Backend به صورت Dependency Guard انجام می‌شود تا Tableهای Legacy و Tableهای Additive آینده نیز پوشش داده شوند.
- Company دارای Unit/User/Admin/RBAC/Ticket یا هر Business Reference دیگر از UI هرگز حذف نمی‌شود؛ حذف استثنایی فقط تصمیم مدیر سامانه و اقدام مستقیم Database پس از Impact Review/Backup است.
- Generic CRUD برای `company_profiles` نباید بتواند Endpoint امن Company را دور بزند.
- تغییر Activity شرکت در صورتی که برای آن Company نقش‌های RBAC وجود داشته باشد مسدود است تا `rbac.activity_id` و ساختار دسترسی ناسازگار نشوند.
- چهار Flag فعلی `company_development`, `delivery_development`, `company_support`, `delivery_support` حفظ می‌شوند؛ Schema جدیدی برای آنها ساخته نمی‌شود.
- مقدار پیش‌فرض ایجاد Company برای این چهار Flag برابر `1` در نظر گرفته می‌شود تا با Default دیتابیس و داده‌های فعلی سازگار باشد.

---

## 1. هدف اصلی

هدف، اضافه‌کردن ساختار سازمانی مشتریان به IWEB است، بدون اینکه سیستم تیکتینگ فعلی INTEK مختل شود.

قابلیت جدید باید سه نقش سمت شرکت مشتری داشته باشد:

- ادمین شرکت
- مدیر بخش
- کارشناس

اما هدف این قابلیت **ساخت اتوماسیون داخلی برای شرکت مشتری نیست**. تمام فرآیند جدید فقط باید برای **آماده‌سازی و ارسال درخواست به INTEK** استفاده شود.

به بیان ساده:

`کارشناس -> مدیر بخش -> INTEK`

یا در صورت نیاز به اصلاح:

`کارشناس -> مدیر -> کارشناس همان بخش -> مدیر -> INTEK`

این چرخه نباید تبدیل به سیستم گردش کار داخلی، مدیریت وظایف یا کارتابل سازمانی مستقل برای شرکت مشتری شود.

---

## 2. اصل غیرقابل مذاکره: سیستم فعلی نباید خراب شود

توسعه باید با روش **Additive / Non-Breaking** انجام شود.

### قوانین این اصل

1. جدول فعلی `tickets` در مراحل اولیه تغییر نمی‌کند.
2. مسیر فعلی ایجاد تیکت (`iweb/controller/ticket/ticket_add.php`) در مراحل اولیه دست‌نخورده می‌ماند.
3. منطق فعلی Status و `checkStatusTable()` تغییر نمی‌کند تا Bridge نهایی تست شود.
4. جدول فعلی `users` و Enum فعلی `role` در مرحله اول تغییر معنایی نمی‌کنند.
5. جدول فعلی `user_log` فقط برای Login/Logout باقی می‌ماند؛ Audit جدید در جدول مستقل ایجاد خواهد شد.
6. تمام قابلیت‌های جدید ابتدا پشت **Feature Flag** غیرفعال قرار می‌گیرند.
7. قابلیت جدید ابتدا فقط برای یک شرکت Pilot فعال می‌شود.
8. Rollback هر مرحله باید بدون حذف یا تغییر اطلاعات سیستم فعلی ممکن باشد.
9. تا قبل از مرحله Publish، هیچ Draft جدیدی وارد جدول اصلی `tickets` نمی‌شود.
10. مسیر Legacy ایجاد تیکت تا پایان Pilot فعال باقی می‌ماند.
11. روی جداول موجود/Legacy هیچ Index جدیدی در این پروژه اضافه نمی‌شود.
12. تغییرات Schema تا حد ممکن فقط با ایجاد Table/Object جدید انجام می‌شوند.
13. Dump کامل محیط توسعه هرگز جایگزین دیتابیس Production نمی‌شود؛ چون داده جداول فعلی دائماً در حال تغییر است.
14. Scriptهای استقرار دیتابیس باید مستقل، مرحله‌ای و قابل اجرای جداگانه باشند.

---

## 3. یافته‌های مهم از کد فعلی

### 3.1 ایجاد تیکت فعلی

در فایل:

`iweb/controller/ticket/ticket_add.php`

تیکت مستقیماً در جدول `tickets` درج می‌شود، سپس `checkStatusTable()` اجرا و شماره تیکت تولید می‌شود.

بنابراین این مسیر را فعلاً نباید تغییر دهیم. Workflow جدید باید ابتدا کنار آن ساخته شود و بعد از تست کامل، فقط در نقطه Publish به این مسیر متصل شود.

### 3.2 ساختار User فعلی

جدول `users` دارای موارد مهم زیر است:

- `role` با مقادیر Legacy: `all`, `admin`, `member`, `observer`
- `status`: `Active`, `Inactive`
- `unit_id` اجباری
- `rbac_id`

Login فعلی نیز Company را از مسیر زیر به دست می‌آورد:

`users.unit_id -> units.company_id`

بنابراین نقش‌های جدید **Admin / Manager / Expert** نباید در مرحله اول با تغییر مستقیم معنای `users.role` پیاده شوند. نقش سازمانی جدید در یک Extension Table مستقل نگهداری می‌شود.

### 3.3 لاگ فعلی

جدول `user_log` فقط دو Action دارد:

- `login`
- `logout`

بنابراین برای ثبت عملیات کاربران، این جدول مناسب نیست و باید Audit مستقل اضافه شود.

### 3.4 دسترسی فعلی Ticket Detail

در `iweb/controller/ticket/ticket_details.php` کنترل اصلی فعلی بر اساس `company_id` انجام می‌شود.

این رفتار برای تیکت‌های فعلی حفظ می‌شود. کنترل Section/Owner جدید ابتدا فقط روی Draft Workflow اعمال خواهد شد تا Ticketing موجود دچار Regression نشود.

---

# 4. نقش‌های نهایی

## 4.1 INTEK / IPanel

در حالت عادی، INTEK فقط **ادمین اولیه شرکت** را ایجاد می‌کند.

INTEK باید در صورت نیاز پشتیبانی بتواند:

- ادمین شرکت ایجاد کند.
- کاربر را Active/Inactive کند.
- در صورت نیاز استثنایی، برای **بخشی که قبلاً در شرکت ایجاد شده** مدیر یا کارشناس ایجاد کند.
- نقش/بخش فعلی کاربران را در لیست کاربران مشاهده کند.
- تمام Auditها را مشاهده کند.

اما روال معمول این نیست که INTEK ساختار داخلی شرکت مشتری را مدیریت کند؛ این مسئولیت ادمین همان شرکت است.

---

## 4.2 ادمین شرکت

ادمین فقط روی `company_id` خودش اختیار دارد.

### اختیارات

- ایجاد بخش‌های شرکت، مانند:
  - مالی
  - Issuer
  - Acquirer
  - توسعه
  - زیرساخت
  - یا هر نام دلخواه
- ویرایش نام/وضعیت بخش.
- مشاهده نقش فعلی، بخش و وضعیت هر User.
- ویرایش اطلاعات User شرکت خودش در محدوده مجاز.
- Active کردن User.
- Inactive کردن User.
- **عدم امکان Delete کاربر**.
- مشاهده تمام تیکت‌های شرکت به صورت مدیریتی.
- مشاهده Audit مدیران و کارشناسان شرکت.
- مشاهده گزارش عملکرد مدیران، کارشناسان و تیکت‌ها.

### قاعده جدید User Management

نقش `ADMIN` به‌تنهایی مجوز ساخت Manager یا Expert ایجاد نمی‌کند.

- ایجاد Manager توسط Company Admin فقط در صورت اعطای دسترسی صریح از سمت **INTEK / مدیریت سامانه** مجاز است.
- ایجاد Expert توسط Company Admin فقط در صورت اعطای دسترسی صریح از سمت **INTEK / مدیریت سامانه** مجاز است.
- Default برای هر دو دسترسی: **Deny / 0**.
- Customer Admin حق Grant/Revoke این دسترسی‌ها برای خودش یا دیگران را ندارد.
- اگر دسترسی داده نشده باشد، ایجاد Manager/Expert فقط توسط INTEK انجام می‌شود.
- تمام Grant/Revokeها و ایجاد کاربران باید Audit شوند.

### نکته مهم

ادمین در مسیر ارسال تیکت **Approver نیست**.

یعنی:

`Manager -> Admin -> INTEK`

وجود ندارد.

ارسال نهایی به INTEK مستقیماً توسط **مدیر بخش** انجام می‌شود.

---

## 4.3 مدیر بخش

هر مدیر فقط می‌تواند مدیر **یک بخش** باشد.

### اختیارات مدیر

- مشاهده تیکت‌های بخش خودش.
- مشاهده کامنت‌های تیکت‌های بخش خودش.
- تعیین/تغییر اولویت تیکت.
- ویرایش تیکت وقتی تیکت در اختیار مدیر است.
- دریافت تیکت ارسالی از Expert و ارسال نهایی آن به INTEK.
- ارسال نهایی تیکت به INTEK.
- مشاهده Audit کارشناسان بخش خودش.
- مشاهده گزارش عملکرد کارشناسان و تیکت‌های بخش خودش.

### قاعده جدید ایجاد Ticket توسط Manager

حق ایجاد Ticket Request توسط Manager یک Permission ثابت نیست و از وضعیت Section محاسبه می‌شود:

- اگر Section مدیر **هیچ Expert فعال** نداشته باشد، Manager به صورت پیش‌فرض می‌تواند Ticket Request جدید ایجاد کند.
- به محض فعال‌شدن حداقل یک Expert در همان Section، این حق به صورت خودکار سلب می‌شود.
- اگر بعداً تعداد Expert فعال همان Section دوباره به صفر برسد، حق پایه ایجاد Ticket برای Manager به صورت خودکار برمی‌گردد.
- INTEK می‌تواند به صورت استثنایی Permission بدهد که Manager حتی با وجود Expert فعال نیز Ticket ایجاد کند.
- Customer Admin و Manager حق فعال‌کردن این Override را ندارند.

### قاعده جدید Manager -> Expert

- برگشت/ارجاع Ticket Request از Manager به Expert برای Revision به صورت پیش‌فرض **ممنوع** است.
- این Action فقط در صورت اعطای Permission صریح توسط **INTEK / مدیریت سامانه** فعال می‌شود.
- حتی در صورت اعطای Permission، مقصد فقط Expert فعال همان Section است و هدف فقط تکمیل همان درخواست با مقصد INTEK خواهد بود.
- Manager می‌تواند Permission ایجاد Expert در Section خودش را نیز فقط در صورت Grant صریح INTEK داشته باشد؛ Default = Deny.
- Manager هیچ‌گاه مجاز به ایجاد Manager دیگر نیست.

### محدودیت

مدیر نمی‌تواند از این قابلیت برای ساخت Task داخلی مستقل استفاده کند.

هیچ وضعیت «انجام شد داخل شرکت»، «بسته شد توسط کارشناس» یا «Task Complete» برای Draft داخلی وجود نخواهد داشت.

سرنوشت Draft فقط یکی از این دو مورد است:

1. `Sent To INTEK`
2. `Cancelled`

---

## 4.4 کارشناس

### اختیارات

- ایجاد Draft برای درخواست از INTEK.
- مشاهده Draft خودش.
- ویرایش Draft تا زمانی که Owner آن خودش است.
- ارسال Draft به مدیر بخش خودش.
- دریافت تیکتی که مدیر برای تکمیل/اصلاح به او برگردانده است.
- ویرایش آن تیکت تا زمانی که در اختیار خودش است.
- ارسال مجدد آن به همان مدیر بخش.

### محدودیت‌ها

- کارشناس نمی‌تواند تیکت را مستقیم به INTEK ارسال کند.
- کارشناس نمی‌تواند تیکت را به کارشناس دیگر ارسال کند.
- کارشناس نمی‌تواند تیکت را «داخلی انجام‌شده» اعلام کند.
- کارشناس سیستم گزارش مدیریتی Audit ندارد؛ فقط History مربوط به تیکتی که اجازه مشاهده آن را دارد قابل نمایش است.

---

# 5. اصل مهم مالک فعلی Ticket Request

هر Draft دقیقاً یک `Current Owner` دارد.

فقط Current Owner حق ویرایش دارد.

### مثال

```text
کارشناس A
  ↓ ایجاد Draft
Owner = کارشناس A
  ↓ ارسال به مدیر
Owner = مدیر بخش
  ↓ ارجاع برای اصلاح به کارشناس B
Owner = کارشناس B
  ↓ ارسال مجدد به مدیر
Owner = مدیر بخش
  ↓ Send To INTEK
Draft Locked / Published
```

این قانون باید در Backend enforce شود و فقط مخفی‌کردن دکمه در UI کافی نیست.

---

# 6. جلوگیری از تبدیل سیستم به اتوماسیون داخلی رایگان مشتری

این بخش جزو الزامات اصلی محصول است.

## 6.1 محدودیت State Machine

Workflow Draft فقط این مسیرها را دارد:

```text
EXPERT_DRAFT
    ↓
MANAGER_REVIEW
    ↓
EXPERT_REVISION (اختیاری)
    ↓
MANAGER_REVIEW
    ↓
SENT_TO_INTEK
```

و در هر مرحله امکان `CANCELLED` طبق Permission تعریف‌شده وجود خواهد داشت.

### وضعیت‌هایی که عمداً وجود ندارند

- Internal Done
- Completed By Expert
- Approved Internally
- Waiting Internal Department
- Internal Task
- Sub Task
- Manager To Manager
- Expert To Expert
- Department To Department

بنابراین این سیستم قابلیت تبدیل شدن به Workflow داخلی مستقل شرکت را ندارد.

## 6.2 محدودیت ارجاع مدیر

اصل پیش‌فرض این است که Manager نباید Ticket Request را برای گردش داخلی به Expert برگرداند.

- `Manager -> Expert Revision` به صورت پیش‌فرض غیرفعال است.
- فقط INTEK می‌تواند این قابلیت را برای Manager مجاز کند.
- در صورت مجاز بودن، Manager فقط می‌تواند Ticket Request را به **یک Expert فعال همان Section** و فقط برای تکمیل/اصلاح همان درخواست INTEK ارسال کند.
- Expert فقط یک Action برای برگشت دارد: `ارسال به مدیر بخش`.
- Expert نمی‌تواند آن را به Expert دیگر، Manager دیگر یا Section دیگر گردش دهد.
- Customer Admin حق فعال‌کردن این قابلیت را ندارد.

این محدودیت یک کنترل محصولی مستقیم برای جلوگیری از تبدیل Customer Portal به اتوماسیون داخلی مشتری است.

## 6.3 عدم وجود بستن داخلی

Draft نمی‌تواند در شرکت مشتری با عنوان «انجام شد» بسته شود.

پایان معتبر Workflow:

- ارسال به INTEK
- لغو درخواست

## 6.4 Draft Aging

برای جلوگیری از نگهداری طولانی درخواست‌ها به عنوان Task داخلی، باید گزارش Aging داشته باشیم:

- Draftهای ارسال‌نشده بیش از N روز
- Ticket Requestهایی که چند بار بین مدیر و کارشناس برگشته‌اند
- Draftهایی که ایجاد شده ولی هیچ‌وقت به INTEK نرسیده‌اند

**مقدار N هنوز نهایی نشده و باید قبل از پیاده‌سازی مشخص شود.**

## 6.5 عدم افزودن قابلیت‌های Task Management

در این پروژه اضافه نمی‌شود:

- Deadline داخلی
- Checklist
- Subtask
- Project
- Sprint
- Internal Assignee Chain
- Internal Completion Percentage
- Task Board داخلی

این قابلیت عمداً فقط یک **Pre-Ticket Gateway به INTEK** است.

---

# 7. مدل دیتابیس پیشنهادی - Additive

> در این نسخه فقط نام و هدف جداول مشخص می‌شود. DDL هر جدول جداگانه و فقط پس از تأیید مرحله مربوطه ارائه خواهد شد.

### قواعد قطعی Database Change

- هر جدول جدید باید با Prefix `iweb_customer_portal_` ساخته شود.
- DDL هر جدول در یک Script مستقل نگهداری می‌شود.
- Index فقط روی جدول جدید همان مرحله ایجاد می‌شود.
- روی جدول‌های Legacy مانند `users`, `tickets`, `units`, `company_profiles` و سایر جداول موجود Index جدید اضافه نمی‌شود.
- هیچ Dump کامل دیتابیس Development برای Replace کردن Production استفاده نمی‌شود.
- Export/Import جداول جدید باید مستقل از داده‌های Legacy قابل انجام باشد.
- قبل از ساخت جدول بعدی، جدول فعلی باید توسط مالک پروژه تأیید شود.
- Charset/Collation جداول جدید باید `utf8mb4 / utf8mb4_general_ci` باشد.
- Engine جداول جدید باید `InnoDB` باشد.

## 7.1 `iweb_customer_portal_sections`

**وضعیت: ساخته شد**

بخش‌های تعریف‌شده توسط ادمین هر شرکت.

ساختار نهایی ایجادشده:

- `id`
- `company_id`
- `name`
- `is_active`
- `created_by`
- `created_at`
- `updated_by`
- `updated_at`

قواعد و Indexهای ایجادشده:

- Primary Key روی `id`.
- نام Section در یک Company تکراری نیست: Unique روی `(company_id, name)`.
- Index روی `(company_id, is_active)` برای بازیابی Sectionهای فعال.
- هیچ Foreign Key به جداول Legacy مانند `company_profiles` یا `users` ایجاد نشده است.
- حذف فیزیکی Section در Business Layer مجاز نیست و غیرفعال‌سازی با `is_active` انجام می‌شود.

---

## 7.2 `iweb_customer_portal_user_profiles`

**وضعیت: ساخته شد**

Extension جدول `users` برای نقش جدید، بدون تغییر معنای `users.role`.

ساختار نهایی ایجادشده:

- `id`
- `user_id`
- `company_id`
- `portal_role` (`ADMIN`, `MANAGER`, `EXPERT`)
- `section_id` nullable
- `is_active`
- `created_by`
- `created_at`
- `updated_by`
- `updated_at`

قواعد و Indexهای ایجادشده:

- هر User فقط یک Portal Profile دارد: Unique روی `user_id`.
- Index روی `(company_id, portal_role)`.
- Index روی `(section_id, is_active)`.
- `section_id` فقط به جدول جدید `iweb_customer_portal_sections` Foreign Key دارد.
- به `users` و `company_profiles` Foreign Key ایجاد نشده است.
- فیلد `finance_access` در این مرحله ایجاد نشد و همچنان یک تصمیم باز محصولی است.

### Rule

- Admin: `section_id = NULL`
- Manager: دقیقاً یک `section_id`
- Expert: یک Section فعال در نسخه اول

در صورت نیاز به چند Section برای Expert در آینده، این قسمت بعداً به Mapping Table تبدیل می‌شود؛ در MVP انجام نمی‌شود.

---

## 7.3 `iweb_customer_portal_ticket_drafts`

محل نگهداری درخواست قبل از ورود به `tickets` اصلی.

اطلاعات کلیدی:

- id
- company_id
- section_id
- creator_user_id
- current_owner_user_id
- manager_user_id
- title
- description
- type_id
- priority (فقط مدیر حق تعیین دارد)
- workflow_status
- published_ticket_id nullable
- created_at
- updated_at
- sent_to_intek_at nullable
- cancelled_at nullable

---

## 7.4 `iweb_customer_portal_ticket_audit_logs`

**وضعیت: ساخته شد**

Audit جدید و Append Only.

ساختار نهایی ایجادشده:

- `id`
- `company_id`
- `section_id`
- `actor_user_id`
- `actor_source` (`USER`, `ADMIN`)
- `actor_role`
- `action_type`
- `entity_type`
- `entity_id`
- `target_user_id`
- `old_data`
- `new_data`
- `ip_address`
- `user_agent`
- `created_at`

تصمیم‌های نهایی این مرحله:

- `action_type` و `entity_type` به صورت `varchar` نگهداری می‌شوند تا اضافه‌شدن Event جدید نیازمند `ALTER TABLE` نباشد.
- `actor_source` مشخص می‌کند شناسه Actor مربوط به `users` است یا `admins`.
- `old_data` و `new_data` به صورت `LONGTEXT` ساخته شده‌اند و PHP در آنها JSON ذخیره خواهد کرد.
- Indexهای گزارش/جستجو روی Company+Date، Section+Date، Actor، Entity و Action ایجاد شده‌اند.
- هیچ Foreign Key به جداول Legacy ایجاد نشده است.
- Append Only بودن توسط Backend PHP enforce می‌شود؛ Customer حق Update/Delete Audit را ندارد.

---

## 7.5 `iweb_customer_portal_ticket_views`

**وضعیت: ساخته شد**

برای ثبت مشاهده Ticket Request توسط هر کاربر.

ساختار نهایی ایجادشده:

- `id`
- `draft_id`
- `user_id`
- `first_viewed_at`
- `last_viewed_at`
- `view_count`

قواعد و Indexهای ایجادشده:

- به ازای هر `(draft_id, user_id)` فقط یک رکورد: Unique روی `(draft_id, user_id)`.
- Index روی `(user_id, last_viewed_at)`.
- به `users` Foreign Key ایجاد نشده است چون Legacy است.
- به `draft_id` نیز فعلاً Foreign Key ایجاد نشده چون جدول Draft در Phase 3 ساخته می‌شود.

اولین مشاهده علاوه بر این جدول، در Audit با Event زیر ثبت می‌شود:

`TICKET_FIRST_VIEWED`

---

## 7.6 `iweb_customer_portal_ticket_publish_map`

برای جلوگیری از تغییر زودهنگام جدول `tickets`، ارتباط Draft و Ticket اصلی در Mapping مستقل نگهداری می‌شود:

- draft_id
- ticket_id
- section_id
- published_by
- published_at

در نتیجه در مراحل اولیه نیازی به `ALTER TABLE tickets` نداریم.

---

# 8. Audit مورد نیاز

حداقل Eventهای زیر ثبت می‌شوند:

### User / Structure

- `USER_CREATED`
- `USER_UPDATED`
- `USER_ACTIVATED`
- `USER_DEACTIVATED`
- `USER_MANAGEMENT_PERMISSION_GRANTED`
- `USER_MANAGEMENT_PERMISSION_REVOKED`
- `MANAGER_CREATED`
- `EXPERT_CREATED`
- `SECTION_CREATED`
- `SECTION_UPDATED`
- `SECTION_ACTIVATED`
- `SECTION_DEACTIVATED`
- `MANAGER_ASSIGNED`
- `MANAGER_CHANGED`
- `EXPERT_ASSIGNED`
- `EXPERT_REMOVED_FROM_SECTION`

### Ticket Request

- `TICKET_DRAFT_CREATED`
- `TICKET_FIRST_VIEWED`
- `TICKET_VIEWED`
- `TICKET_EDITED`
- `TICKET_SENT_TO_MANAGER`
- `TICKET_ASSIGNED_TO_EXPERT`
- `TICKET_RETURNED_TO_MANAGER`
- `TICKET_PRIORITY_CHANGED`
- `TICKET_SENT_TO_INTEK`
- `TICKET_CANCELLED`
- `COMMENT_CREATED`
- `COMMENT_EDITED`
- `FILE_UPLOADED`
- `FILE_REMOVED`

### نکته

برای Editهای مهم، Audit باید حداقل `old_data` و `new_data` را نگه دارد تا مشخص باشد چه چیزی تغییر کرده است.

---

# 9. سطح مشاهده Audit

## INTEK

- تمام شرکت‌ها
- تمام ادمین‌ها
- تمام مدیران
- تمام کارشناسان
- تمام Ticket Requestها

## ادمین شرکت

فقط:

`company_id = Admin.company_id`

و تمام مدیران/کارشناسان/تیکت‌های شرکت خودش.

## مدیر بخش

فقط:

- `company_id = Manager.company_id`
- `section_id = Manager.section_id`

و Audit کارشناسان و Ticket Requestهای بخش خودش.

## کارشناس

صفحه Audit مدیریتی ندارد.

در Ticket Detail فقط History مجاز همان تیکت را مشاهده می‌کند.

---

# 10. گزارش عملکرد

## 10.1 گزارش ادمین شرکت

ادمین باید بتواند عملکرد کل شرکت را مشاهده کند.

موارد پیشنهادی:

- تعداد Ticket Request ایجادشده
- تعداد ارسال‌شده به INTEK
- تعداد لغوشده
- تعداد Draftهای هنوز ارسال‌نشده
- تفکیک بر اساس Section
- تفکیک بر اساس Manager
- تفکیک بر اساس Expert
- Average Time تا اولین مشاهده مدیر
- Average Time در اختیار Expert
- Average Time از ایجاد تا Send To INTEK
- تعداد دفعات برگشت Manager -> Expert
- Draft Aging
- تعداد User Active/Inactive

## 10.2 گزارش مدیر بخش

همان گزارش فقط برای Section خودش:

- کارشناسان بخش
- Ticket Requestهای بخش
- Pending Draftها
- زمان اولین مشاهده
- زمان اصلاح
- تعداد ارسال به INTEK
- تعداد لغو
- Aging

---

# 11. ایجاد و مدیریت User

## 11.1 سمت INTEK

INTEK مرجع نهایی User Management Permission است.

روال اصلی:

- ایجاد Admin اولیه شرکت.
- ایجاد Manager/Expert در صورت نیاز.
- Active/Inactive کردن User.
- Grant/Revoke دسترسی‌های محدود User Management برای Customer Admin و Manager.

تمام این عملیات باید Audit شوند.

## 11.2 سمت Admin شرکت

Company Admin فقط در `company_id` خودش عمل می‌کند و دسترسی ساخت User به صورت پیش‌فرض ندارد.

دسترسی‌های قابل اعطا توسط INTEK:

- اجازه ایجاد Manager برای Company خودش. Default = `0`.
- اجازه ایجاد Expert برای Company خودش. Default = `0`.

در صورت نداشتن Permission مربوطه، Action باید در Backend رد شود؛ مخفی‌بودن دکمه در UI کافی نیست.

Company Admin حق Grant/Revoke Permissionهای فوق را ندارد.

## 11.3 سمت Manager

Manager به صورت پیش‌فرض حق ایجاد User ندارد.

INTEK می‌تواند Permission ایجاد Expert را به Manager بدهد. در این حالت:

- فقط Expert برای Company خودش.
- فقط Expert برای Section خودش.
- Manager حق ایجاد Manager دیگر ندارد.
- Default = `0`.

## 11.4 Permissionهای منطقی مصوب

نام فنی نهایی جدول Permission هنوز طراحی نشده، اما Permissionهای Business زیر قطعی هستند:

- `customer_admin_create_manager`
- `customer_admin_create_expert`
- `manager_create_expert`
- `manager_create_ticket_with_experts`
- `manager_return_ticket_to_expert`

همه Permissionهای استثنایی فوق **Default Deny** هستند.

### Rule امنیتی مهم

`company_id` نباید از POST ادمین/مدیر پذیرفته شود.

Company و Section باید در Backend از Session + Portal Profile استخراج و مجدداً اعتبارسنجی شوند.

---

# 12. تصمیم فنی مهم درباره `unit_id` و `rbac_id`

این مورد قبل از فعال‌کردن User Creation توسط Customer Admin باید حل شود.

در سیستم فعلی:

- `users.unit_id` اجباری است.
- Login، `company_id` را از `units.company_id` استخراج می‌کند.
- `rbac_id` نیز در Session قرار می‌گیرد.

بنابراین نباید User جدید را با `unit_id` یا `rbac_id` تصادفی/عمومی ایجاد کنیم.

### تا زمان تصمیم نهایی

Feature «Create User توسط Customer Admin» پیاده‌سازی نهایی نمی‌شود.

ابتدا باید مشخص کنیم:

- آیا برای هر Company یک Unit ثابت مخصوص Portal تعریف می‌کنیم؟
- یا از Unit موجود Admin همان شرکت استفاده می‌کنیم؟
- RBAC پایه Admin/Manager/Expert چگونه Map می‌شود؟

این مورد یکی از Gateهای فاز User Management است.

---

# 13. Workflow دقیق Draft

## حالت A - ایجاد توسط Expert

1. Expert درخواست را ایجاد می‌کند.
2. Status = `EXPERT_DRAFT`
3. Owner = Expert
4. تا قبل از Send To Manager قابل ویرایش است.
5. Expert روی «ارسال به مدیر» کلیک می‌کند.
6. Status = `MANAGER_REVIEW`
7. Owner = Manager Section
8. Expert دیگر حق Edit ندارد.

## حالت B - Revision توسط Expert فقط با Permission ویژه

این مسیر **Default نیست** و فقط وقتی فعال می‌شود که `manager_return_ticket_to_expert` توسط INTEK برای Manager مجاز شده باشد.

1. Manager Ticket Request را بررسی می‌کند.
2. Backend Permission ویژه را بررسی می‌کند.
3. مقصد فقط Expert فعال همان Section است.
4. Status = `EXPERT_REVISION`.
5. Owner = Expert انتخاب‌شده.
6. Expert حق Edit همان Request را دارد.
7. Expert فقط می‌تواند آن را به همان Manager Section برگرداند.
8. Status دوباره = `MANAGER_REVIEW`.
9. Owner = Manager.

بدون Permission ویژه، Action برگشت به Expert باید هم در UI مخفی و هم در Backend رد شود.

## حالت C - ایجاد توسط Manager

قبل از ایجاد Request، Backend تعداد Expert فعال همان Section را محاسبه می‌کند.

### C1 - Section بدون Expert فعال

1. اگر `active_expert_count = 0` باشد، Manager به صورت پیش‌فرض می‌تواند Ticket Request ایجاد کند.
2. Owner = Manager.
3. Status = `MANAGER_REVIEW`.
4. Manager می‌تواند Priority تعیین و Request را مستقیماً Send To INTEK کند.

### C2 - Section دارای Expert فعال

1. اگر `active_expert_count > 0` باشد، Manager به صورت پیش‌فرض حق ایجاد Ticket Request ندارد.
2. فقط اگر INTEK Permission `manager_create_ticket_with_experts` را Grant کرده باشد، Manager می‌تواند Request ایجاد کند.
3. Customer Admin یا Manager نمی‌توانند این Permission را برای خود فعال کنند.

### تغییر خودکار Permission مؤثر

- اضافه/Active شدن اولین Expert: حق پایه Create Ticket مدیر خودکار سلب می‌شود.
- Inactive/Remove شدن آخرین Expert فعال: حق پایه Create Ticket مدیر خودکار برمی‌گردد.
- این رفتار باید در Backend بر اساس وضعیت فعلی داده محاسبه شود و صرفاً یک Flag ذخیره‌شده نباشد.

### تأکید

هدف این محدودیت جلوگیری از استفاده Customer Portal به عنوان Task/Workflow داخلی مشتری است. هر Request باید از ابتدا مقصد نهایی INTEK داشته باشد.

---

# 14. Publish به `tickets`

تنها Manager Section مجاز به Publish است.

در عملیات `Send To INTEK` باید در یک Transaction دیتابیس:

1. Draft Lock شود.
2. Permission Manager مجدداً در Backend بررسی شود.
3. Company/Section بررسی شود.
4. رکورد اصلی در `tickets` ساخته شود.
5. شماره Ticket با منطق فعلی تولید شود.
6. منطق Status فعلی سیستم اجرا شود.
7. فایل‌ها/کامنت‌های لازم طبق طراحی نهایی منتقل شوند.
8. Mapping در `iweb_customer_portal_ticket_publish_map` ثبت شود.
9. Draft به `SENT_TO_INTEK` تغییر کند.
10. Event `TICKET_SENT_TO_INTEK` در Audit ثبت شود.

اگر هر مرحله Fail شود، کل Transaction Rollback می‌شود.

در نتیجه حالت نیمه‌Publish نباید وجود داشته باشد.

---

# 15. Feature Flag

حداقل Flag پیشنهادی:

`customer_portal_workflow_enabled`

در سطح Company.

Default:

`0`

فقط Company Pilot:

`1`

اگر Flag خاموش باشد، IWEB دقیقاً با Workflow فعلی کار می‌کند.

---

# 16. برنامه اجرای مرحله‌ای

## Phase 0 - Baseline و حفاظت از سیستم فعلی

**وضعیت:** مرحله فعلی

### اقدامات

- [x] بررسی اولیه ساختار پروژه `icore.zip`
- [x] بررسی `tickets`
- [x] بررسی `users`
- [x] بررسی `user_log`
- [x] شناسایی مسیر فعلی ایجاد تیکت
- [ ] تهیه Smoke Test سیستم فعلی
- [ ] تهیه Backup کد و Database
- [ ] ایجاد Branch مجزا
- [ ] تعریف Feature Flag
- [ ] ثبت Baseline رفتار فعلی

### شرط پایان Phase 0

هیچ تغییر Functional در Production وجود نداشته باشد.

---

## Phase 1 - دیتابیس Additive برای Structure + Audit

### ایجاد فقط جداول جدید

- [x] `iweb_customer_portal_sections`
- [x] `iweb_customer_portal_user_profiles`
- [x] `iweb_customer_portal_ticket_audit_logs`
- [x] `iweb_customer_portal_ticket_views`

**وضعیت ساخت:** هر چهار جدول جدید ایجاد شده‌اند.

**وضعیت Acceptance:** هنوز باز است تا Smoke/Regression Test سیستم Legacy انجام و عدم اثر جانبی تأیید شود.

### در این Phase انجام نمی‌شود

- تغییر `tickets`
- تغییر رفتار `ticket_add.php`
- تغییر Workflow کاربران فعلی

### Acceptance

کل سیستم فعلی بعد از Migration بدون تفاوت کار کند.

### روش اجرای Phase 1

جداول این Phase یکجا ساخته نمی‌شوند. ترتیب کار:

1. طراحی یک جدول.
2. تأیید ساختار توسط مالک پروژه.
3. ارائه SQL همان جدول.
4. ایجاد و تست جدول.
5. تأیید نتیجه.
6. سپس ورود به جدول بعدی.

تا قبل از تأیید مرحله قبلی، SQL جدول بعدی ارائه یا اجرا نمی‌شود.

---

## Phase 2 - پنل Admin شرکت برای Structure

- [ ] ایجاد Section
- [ ] ویرایش Section
- [ ] Active/Inactive Section
- [ ] تعیین Manager
- [ ] تخصیص Expert
- [ ] نمایش Role/Section کاربران
- [ ] Audit تمام عملیات

### User Creation و Permission Governance

بعد از حل تصمیم `unit_id/rbac_id`:

- [ ] Create User توسط INTEK
- [ ] Permission-based Create Manager توسط Company Admin
- [ ] Permission-based Create Expert توسط Company Admin
- [ ] Permission-based Create Expert توسط Manager فقط در Section خودش
- [ ] Grant/Revoke Permission فقط از سمت INTEK
- [ ] Default Deny برای تمام Permissionهای User Creation مشتری
- [ ] Edit User محدود
- [ ] Active
- [ ] Inactive
- [ ] عدم Delete
- [ ] Audit کامل Grant/Revoke/Create/Activate/Inactive

در این Phase هنوز Draft Workflow فعال نمی‌شود.

---

## Phase 3 - Pre-Ticket Workflow مستقل

- [ ] `iweb_customer_portal_ticket_drafts`
- [ ] ایجاد Draft توسط Expert
- [ ] ایجاد Request توسط Manager فقط طبق Rule تعداد Expert فعال / Override INTEK
- [ ] Send To Manager
- [ ] Manager -> Expert Revision فقط با Permission صریح INTEK
- [ ] Expert -> Manager Return
- [ ] Priority فقط Manager
- [ ] Owner-based Edit
- [ ] First View Audit
- [ ] History

### شرط مهم

هنوز هیچ رکوردی وارد `tickets` نمی‌شود.

---

## Phase 4 - Bridge امن برای Send To INTEK

- [ ] Publish Transactional
- [ ] ساخت Ticket در جدول اصلی
- [ ] اجرای Status فعلی
- [ ] تولید Ticket Number
- [ ] Publish Mapping
- [ ] جلوگیری از Publish تکراری
- [ ] Lock Draft بعد از Publish
- [ ] Audit

### Acceptance

Ticket منتشرشده باید از دید IPanel دقیقاً مانند Ticket عادی فعلی رفتار کند.

---

## Phase 5 - Audit UI و Reports

### Admin Company

- [ ] Company Audit
- [ ] Managers Report
- [ ] Experts Report
- [ ] Tickets Report
- [ ] Aging

### Manager

- [ ] Section Audit
- [ ] Experts Report
- [ ] Section Tickets Report

### INTEK

- [ ] Global Audit
- [ ] Company Filter
- [ ] User/Role/Section Filter

---

## Phase 6 - Pilot

- [ ] فعال‌سازی برای فقط یک Company
- [ ] تست Admin
- [ ] تست Manager
- [ ] تست Expert
- [ ] تست Permission Bypass
- [ ] تست Publish
- [ ] تست Audit
- [ ] تست Report
- [ ] Regression کامل Ticketing فعلی

پس از تأیید Pilot، Rollout مرحله‌ای انجام می‌شود.

---

# 17. تست‌های Regression اجباری

قبل و بعد از هر Phase:

- Login کاربر فعلی
- Logout
- مشاهده Ticket List فعلی
- ایجاد Ticket Legacy
- مشاهده Ticket Detail
- Comment
- File Upload
- تغییر Status طبق دسترسی فعلی
- Priority List فعلی
- IPanel Ticket List
- IPanel Ticket Detail
- Search/Filter فعلی
- Notification فعلی

اگر یکی از این موارد تغییر ناخواسته داشت، Phase Release نمی‌شود.

---

# 18. تست‌های امنیتی اجباری

- Admin Company A نتواند User شرکت B را ببیند/تغییر دهد.
- Admin نتواند `company_id` را با POST دستکاری کند.
- Manager Section A نتواند Draft Section B را باز کند.
- Expert نتواند با تغییر URL Draft فرد دیگری را باز کند.
- Expert نتواند Owner را دستی عوض کند.
- Expert نتواند مستقیم Publish کند.
- Manager نتواند به Expert شرکت دیگر ارجاع دهد.
- Manager نتواند به Expert Section دیگر ارجاع دهد.
- Company Admin بدون Permission نتواند Manager/Expert بسازد، حتی با POST مستقیم.
- Manager بدون Permission نتواند Expert بسازد، حتی با POST مستقیم.
- Customer Admin/Manager نتوانند Permissionهای مدیریتی را برای خود یا دیگران Grant/Revoke کنند.
- Manager وقتی حداقل یک Expert فعال در Section دارد نتواند بدون Override INTEK Ticket Request جدید بسازد.
- اضافه/Active شدن اولین Expert باید حق پایه Create Ticket Manager را خودکار سلب کند.
- Inactive/Remove شدن آخرین Expert فعال باید حق پایه Create Ticket Manager را خودکار برگرداند.
- Manager بدون Permission `manager_return_ticket_to_expert` نتواند Request را به Expert برگرداند.
- Draft منتشرشده دوباره Publish نشود.
- Audit توسط Customer قابل Delete/Update نباشد.
- Inactive User امکان Login نداشته باشد.

---

# 19. موارد باز برای تصمیم‌گیری قبل از کدنویسی هر بخش

این موارد عمداً هنوز نهایی نشده‌اند:

1. Draft بعد از چند روز بدون ارسال به INTEK Aging/Expire شود؟
2. آیا Expert می‌تواند Draft خودش را قبل از Send To Manager حذف/Cancel کند یا فقط Cancel؟
3. اگر Permission `manager_return_ticket_to_expert` توسط INTEK داده شد، تعداد دفعات Revision محدود باشد یا نامحدود ولی قابل گزارش؟
4. دسترسی مالی Manager که قبلاً مطرح شد در نسخه اول فعال شود یا فاز بعد؟
5. Commentهای قبل از Publish آیا بعد از ارسال به INTEK هم برای تیم INTEK نمایش داده شوند یا فقط History داخلی Preparation باشند؟
6. Attachmentهای Draft هنگام Publish همگی منتقل شوند یا Manager هنگام Publish انتخاب کند؟
7. Mapping امن `unit_id/rbac_id` برای Userهای ساخته‌شده توسط Customer Admin/Manager.
8. مدل نهایی نگهداری Permissionهای Customer Portal: جدول مستقل Permission/Grant یا Extension دیگر؛ Business Ruleها قطعی ولی Schema هنوز نیازمند Approval است.

---

# 20. مرحله بعد پیشنهادی

**فعلاً هیچ Controller فعلی را تغییر ندهیم.**

### Phase 1A - وضعیت فعلی

چهار جدول Phase 1 به صورت مرحله‌ای طراحی، تأیید و ایجاد شدند:

1. [x] `iweb_customer_portal_sections`
2. [x] `iweb_customer_portal_user_profiles`
3. [x] `iweb_customer_portal_ticket_audit_logs`
4. [x] `iweb_customer_portal_ticket_views`

مرحله بعد قبل از ورود به Phase 2:

1. بررسی ساختار چهار جدول ایجادشده.
2. Smoke/Regression Test مسیرهای Legacy مطابق بخش 17.
3. تأیید اینکه هیچ جدول Legacy، Index Legacy یا Workflow فعلی تغییر نکرده است.
4. فقط پس از تأیید مالک پروژه، شروع Phase 2.

قاعده Approval-based همچنان برقرار است:

`Design -> Review -> Approval -> SQL -> Create/Test -> Approval -> Next Step`

---


## 20.2 تصمیم قطعی IPanel Unit Management

صفحه `ipanel/unit` در بازطراحی مدیریتی باید همچنان از جدول Legacy `units` استفاده کند و در این مرحله هیچ Table/View/SP/Event/Index جدیدی برای Unit ایجاد نمی‌شود.

قواعد قطعی:

- Add/Edit/Delete فقط در صورت داشتن Subpart دسترسی `unit` و Operation متناظر (`add_operation` / `edit_operation` / `delete_operation`) مجاز است و Backend باید مجدداً آن را بررسی کند.
- `units` از Generic CRUD خارج و فقط از Endpoint اختصاصی محافظت‌شده مدیریت می‌شود.
- یکتایی نام Unit در محدوده همان Company بررسی می‌شود: `(company_id, unit_name)`؛ یک نام مشابه می‌تواند در Company دیگر وجود داشته باشد.
- `unit_type` اختیاری باقی می‌ماند و معنای Legacy آن تغییر نمی‌کند.
- Unit دارای هر Dependency با `unit_id` در Base Tableها از داخل سامانه حذف نمی‌شود، حتی اگر کاربر `delete_operation` داشته باشد.
- Dependencyهای فعلی مهم شامل `users`, `admins`, `rbac`, `daily_reports` هستند؛ Delete Backend باید به صورت پویا Base Tableهای دارای `unit_id` را نیز بررسی کند.
- انتقال Unit دارای Dependency به Company دیگر ممنوع است، چون `users.unit_id -> units.company_id` مبنای Scope شرکت در Login و همچنین RBAC/Manager Context است و تغییر Company می‌تواند Scope بیزینس را ناخواسته عوض کند.
- نام و `unit_type` یک Unit وابسته همچنان قابل ویرایش هستند، چون شناسه Unit ثابت می‌ماند؛ تغییر Company آن قفل می‌شود.
- حذف استثنایی Unit وابسته فقط توسط مدیر سامانه، خارج از UI، بعد از Impact Review و Backup و با اصلاح وابستگی‌ها مجاز است.
- Captionهای جدید Unit در سه فایل اصلی `icore/lang/fa.php`, `en.php`, `ar.php` داخل همان Patch Merge می‌شوند.

---

# 21. Change Log


## v0.7 - 1405/07/27

- تصمیم Unit Management ثبت شد و جدول Legacy `units` به عنوان مرجع حفظ شد.
- برای Unit هیچ Table/View/SP/Event/Index جدیدی اضافه نشد.
- Add/Edit/Delete واحد به Subpart Unit + Operation Permission در Backend وابسته شد.
- Generic CRUD برای `units` مسدود شد تا Endpoint امن دور زده نشود.
- نام Unit در سطح همان Company یکتا شد؛ نام مشابه در Company دیگر مجاز است.
- Delete Unit در صورت وجود هر Dependency با ستون `unit_id` در Base Tableهای دیتابیس ممنوع شد.
- انتقال Unit وابسته به Company دیگر ممنوع شد تا Scope کاربران/مدیران/RBAC و Company مشتق‌شده از `unit_id` ناخواسته تغییر نکند.
- `unit_type` اختیاری و سازگار با Legacy باقی ماند.
- فایل‌های `fa/en/ar` به صورت Merge‌شده داخل Patch Unit قرار می‌گیرند.

## v0.6 - 1405/07/27

- تصمیم Company Management ثبت شد و `company_profiles` به عنوان مرجع Legacy حفظ شد.
- برای Company هیچ Table/View/SP/Event/Index جدیدی اضافه نشد.
- Add/Edit/Delete شرکت به Subpart Company + Operation Permission در Backend وابسته شد.
- Generic CRUD برای `company_profiles` باید مسدود باشد تا Endpoint امن دور زده نشود.
- Delete Company در صورت وجود هر Dependency با ستون `company_id` در Base Tableهای دیتابیس ممنوع شد.
- تغییر Activity شرکت دارای RBAC Role وابسته ممنوع شد تا ساختار Role ناسازگار نشود.
- Flagهای Development/Support موجود حفظ و Default ایجاد Company با Default دیتابیس (`1`) هم‌راستا شد.
- فایل‌های `fa/en/ar` به صورت Merge‌شده داخل Patch Company قرار می‌گیرند.

## v0.5 - 1405/07/26

- بازطراحی مدیریتی IPanel به صورت مرحله‌ای و از `ipanel/activity` آغاز شد.
- قانون سراسری Permission برای تمام Actionهای Database/Business در Backend تصویب شد.
- مخفی‌کردن Action در UI به‌تنهایی Authorization محسوب نمی‌شود.
- حذف رکورد دارای زیرمجموعه یا Business Dependency حتی برای دارنده Delete Permission از داخل سامانه ممنوع شد.
- حذف استثنایی رکورد وابسته فقط با تشخیص مدیر سامانه و اقدام مستقیم Database پس از Impact Review مجاز است.
- اصل استفاده از Tableهای موجود قبل از ایجاد View/SP/Event/Table/Index جدید تثبیت شد.
- Dictionaryهای `fa/en/ar` باید به صورت Merge‌شده داخل خود Patch باشند و Fragment جداگانه تحویل نشود.
- ساختار Legacy `Company -> Unit -> Manager/Member` از Customer Portal `Company -> Section -> Manager/Expert` به صورت صریح تفکیک شد.
- Permission `ticket_send_all_operation` و Rule ارسال مدیر به زیرشاخه خودش + گیرندگان دارای Scope عمومی ثبت شد.
- تصمیم Activity Management ثبت شد: استفاده از جداول موجود و حذف فقط در نبود کامل وابستگی.

## v0.4 - 1405/07/25

- Baseline مرجع دیتابیس برای بررسی‌های بعدی به `back261017.sql` به‌روزرسانی شد.
- User Creation سمت Customer از حالت Role-based unconditional خارج و به **Explicit Permission + Default Deny** تبدیل شد.
- Company Admin فقط با Grant صریح INTEK می‌تواند Manager یا Expert ایجاد کند.
- Manager فقط با Grant صریح INTEK می‌تواند Expert همان Section را ایجاد کند و هیچ‌گاه Manager دیگر نمی‌سازد.
- Grant/Revoke Permissionهای Customer Portal فقط در اختیار INTEK / مدیریت سامانه است.
- حق ایجاد Ticket Request توسط Manager به تعداد Expert فعال Section وابسته شد: بدون Expert مجاز، با Expert به صورت خودکار ممنوع.
- Override ایجاد Ticket توسط Manager در حضور Expert فقط با Permission صریح INTEK ممکن است.
- `Manager -> Expert Revision` از حالت پیش‌فرض خارج شد و Default آن Deny است؛ فقط با Permission INTEK فعال می‌شود.
- Anti Internal Automation تقویت شد تا Customer Portal به Workflow/Task Management داخلی مشتری تبدیل نشود.
- Audit Eventهای Grant/Revoke Permission و ایجاد Manager/Expert به الزامات اضافه شدند.
- Security Testهای مربوط به Permission Bypass، Scope و سلب/بازگشت خودکار Create Ticket اضافه شدند.
- Schema نهایی Permissionها هنوز ساخته نشده و باید طبق فرآیند Approval-based در مرحله بعد طراحی شود.

## v0.3 - 1405/07/15

- چهار جدول Phase 1 به صورت مرحله‌ای ایجاد شدند.
- `iweb_customer_portal_sections` به عنوان جدول ساختار Section ایجاد شد.
- `iweb_customer_portal_user_profiles` به عنوان Extension نقش‌های Portal ایجاد شد.
- `iweb_customer_portal_ticket_audit_logs` با پشتیبانی از `actor_source` و Audit Append Only ایجاد شد.
- `iweb_customer_portal_ticket_views` برای First View / Last View / View Count ایجاد شد.
- Foreign Key به جداول Legacy عمداً ایجاد نشد.
- تنها Foreign Key فعلی بین جداول جدید، `user_profiles.section_id -> sections.id` است.
- `finance_access` در جدول User Profile ایجاد نشد و به عنوان تصمیم باز باقی ماند.
- وضعیت Phase 1 از Design به Created تغییر کرد؛ Acceptance نهایی تا انجام Regression/Smoke Test باز می‌ماند.

## v0.2 - 1405/07/15

- Baseline دیتابیس از `back261003.sql` به `back261007.sql` به‌روزرسانی شد.
- موتور واقعی دیتابیس به عنوان MariaDB 11.3.2 / MySQL-compatible ثبت شد.
- Backend پروژه PHP و UI پروژه Bootstrap تثبیت شد.
- Charset/Collation جداول جدید روی `utf8mb4 / utf8mb4_general_ci` تثبیت شد.
- قرارداد نام‌گذاری کامل `iweb_customer_portal_` برای تمام جداول جدید تصویب شد.
- نام تمام جداول پیشنهادی جدید با Prefix مصوب اصلاح شد.
- ایجاد Index فقط روی جداول جدید پروژه مجاز شد.
- افزودن Index روی جداول Legacy موجود ممنوع شد مگر با تصمیم و تأیید جداگانه.
- Replace کردن دیتابیس Production با Dump محیط توسعه صراحتاً ممنوع شد.
- Export/Import مستقل جداول جدید به عنوان Requirement استقرار ثبت شد.
- فرآیند اجرای دیتابیس به حالت مرحله‌به‌مرحله و Approval-based تغییر کرد.

## v0.1 - 1405/07/11

- ایجاد سند اولیه.
- تثبیت سه Role: Admin / Manager / Expert.
- حذف Admin از فرآیند تأیید Ticket.
- Manager به عنوان تنها Publish کننده به INTEK تعیین شد.
- ثبت Requirement ایجاد User توسط Admin برای Company خودش.
- ثبت Active/Inactive و ممنوعیت Delete User.
- تعریف Audit کامل عملیات.
- تعریف First View Tracking.
- تعریف گزارش Admin و Manager.
- اضافه‌شدن اصل بسیار مهم **Anti Internal Automation**.
- طراحی Workflow محدود به مقصد INTEK.
- طراحی اجرای Non-Breaking و Feature Flag.
- تعیین Phase 0 تا Phase 6.


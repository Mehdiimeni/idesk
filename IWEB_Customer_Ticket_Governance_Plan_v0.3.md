# برنامه توسعه مرحله‌ای IWEB برای ساختار شرکت، نقش‌ها و کنترل ارسال تیکت به INTEK

**نسخه:** 0.3  
**تاریخ:** 1405/07/15  
**وضعیت:** Phase 1 - چهار جدول Additive ساخته شده / بدون تغییر در جداول Legacy  
**پروژه مبنا:** `icore.zip` + `back261007.sql`

> **هشدار مهم:** این سند در نسخه فعلی فقط برنامه طراحی و اجرای مرحله‌ای است. در این مرحله هیچ SQL، Migration یا تغییر کدی روی Production نباید اجرا شود.

## 0.1 مبنای فنی قطعی پروژه

از نسخه 0.2، موارد زیر به عنوان مبنای فنی قطعی پروژه در نظر گرفته می‌شوند:

- دیتابیس عملیاتی: **MariaDB 11.3.2** با سازگاری MySQL.
- فایل Baseline فعلی دیتابیس: `back261007.sql`.
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
- تعیین مدیر یک بخش.
- افزودن کارشناسان به بخش.
- ایجاد User فقط برای شرکت خودش.
- ویرایش اطلاعات User شرکت خودش در محدوده مجاز.
- Active کردن User.
- Inactive کردن User.
- **عدم امکان Delete کاربر**.
- مشاهده نقش فعلی، بخش و وضعیت هر User.
- مشاهده تمام تیکت‌های شرکت به صورت مدیریتی.
- مشاهده Audit مدیران و کارشناسان شرکت.
- مشاهده گزارش عملکرد مدیران، کارشناسان و تیکت‌ها.

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
- ایجاد Ticket Request برای ارسال به INTEK.
- تعیین/تغییر اولویت تیکت.
- ویرایش تیکت وقتی تیکت در اختیار مدیر است.
- ارسال تیکت به یکی از کارشناسان **همان بخش** فقط برای تکمیل یا اصلاح درخواست INTEK.
- دریافت مجدد تیکت از کارشناس.
- ارسال نهایی تیکت به INTEK.
- مشاهده Audit کارشناسان بخش خودش.
- مشاهده گزارش عملکرد کارشناسان و تیکت‌های بخش خودش.

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

مدیر فقط می‌تواند Ticket Request را به **یک کارشناس در بخش خودش** برای تکمیل/اصلاح همان درخواست INTEK ارسال کند.

کارشناس فقط یک Action برای برگشت دارد:

`ارسال به مدیر بخش`

یعنی کارشناس نمی‌تواند آن را به فرد بعدی گردش دهد.

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

روال اصلی:

- ایجاد Admin اولیه شرکت.

قابلیت پشتیبانی اختیاری:

- ایجاد Manager برای Section موجود.
- ایجاد Expert برای Section موجود.
- Active/Inactive کردن User.

این عملیات باید Audit شود.

## 11.2 سمت Admin شرکت

ادمین شرکت می‌تواند:

- User جدید فقط برای Company خودش ایجاد کند.
- Manager/Expert تعیین کند.
- User را Active/Inactive کند.
- User را Delete نمی‌کند.

### Rule امنیتی مهم

`company_id` نباید از POST ادمین پذیرفته شود.

Company باید در Backend از Session/Admin Profile استخراج شود.

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

## حالت B - مدیر برای اصلاح به Expert می‌دهد

1. Manager Ticket Request را بررسی می‌کند.
2. Manager می‌تواند به Expert همان Section برای **اصلاح/تکمیل درخواست INTEK** ارجاع دهد.
3. Status = `EXPERT_REVISION`
4. Owner = Expert انتخاب‌شده
5. Expert حق Edit دارد.
6. Expert فقط می‌تواند آن را به Manager همان Section برگرداند.
7. Status = `MANAGER_REVIEW`
8. Owner = Manager

## حالت C - ایجاد توسط Manager

1. Manager Ticket Request را ایجاد می‌کند.
2. Owner = Manager
3. Status = `MANAGER_REVIEW`
4. Manager می‌تواند:
   - Priority تعیین کند.
   - مستقیماً Send To INTEK کند.
   - برای تکمیل/اصلاح به Expert همان Section بدهد.

### تأکید

ایجاد توسط Manager به معنی ایجاد Task برای کارمند نیست. این رکورد از ابتدا یک **درخواست با مقصد INTEK** است.

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

### User Creation

بعد از حل تصمیم `unit_id/rbac_id`:

- [ ] Create User
- [ ] Edit User محدود
- [ ] Active
- [ ] Inactive
- [ ] عدم Delete

در این Phase هنوز Draft Workflow فعال نمی‌شود.

---

## Phase 3 - Pre-Ticket Workflow مستقل

- [ ] `iweb_customer_portal_ticket_drafts`
- [ ] ایجاد Draft توسط Expert
- [ ] ایجاد Request توسط Manager
- [ ] Send To Manager
- [ ] Manager -> Expert Revision
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
- Draft منتشرشده دوباره Publish نشود.
- Audit توسط Customer قابل Delete/Update نباشد.
- Inactive User امکان Login نداشته باشد.

---

# 19. موارد باز برای تصمیم‌گیری قبل از کدنویسی هر بخش

این موارد عمداً هنوز نهایی نشده‌اند:

1. Draft بعد از چند روز بدون ارسال به INTEK Aging/Expire شود؟
2. آیا Expert می‌تواند Draft خودش را قبل از Send To Manager حذف/Cancel کند یا فقط Cancel؟
3. Manager چند بار مجاز است Ticket را برای Revision به Expert برگرداند؟ محدود یا نامحدود ولی قابل گزارش؟
4. دسترسی مالی Manager که قبلاً مطرح شد در نسخه اول فعال شود یا فاز بعد؟
5. Commentهای قبل از Publish آیا بعد از ارسال به INTEK هم برای تیم INTEK نمایش داده شوند یا فقط History داخلی Preparation باشند؟
6. Attachmentهای Draft هنگام Publish همگی منتقل شوند یا Manager هنگام Publish انتخاب کند؟
7. Mapping امن `unit_id/rbac_id` برای Userهای ساخته‌شده توسط Customer Admin.

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

# 21. Change Log

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


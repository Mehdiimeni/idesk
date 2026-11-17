# IWEB Customer Portal - Smoke / Regression Test

## A. Legacy regression - feature flag OFF

1. Use a company with no settings row or with workflow flag = 0.
2. Login to IWEB.
3. Confirm the existing ticket list loads normally.
4. Confirm the existing New Ticket button still points to `tickets?add=r`.
5. Create a normal Legacy ticket and verify status, ticket number and attachment behavior are unchanged.
6. Confirm no Customer Portal menu is shown unless the company flag is enabled and a Portal profile exists.

## B. INTEK setup

1. Open `ipanel/customer_portal_admin`.
2. Select a Pilot company.
3. Configure Unit + Manager RBAC + Expert RBAC.
4. Create/map one Portal ADMIN.
5. Save settings with workflow still disabled.
6. Confirm invalid Unit/RBAC from another company is rejected server-side.
7. Enable workflow.
8. Confirm enable is rejected when required mappings or an active Portal ADMIN are missing.
9. After enabling, confirm IPanel rejects deactivating/demoting the last active Portal ADMIN until another active ADMIN exists.

## C. Company Admin

1. Login as Portal ADMIN.
2. Create Section A and Section B.
3. Rename a Section.
4. Inactivate/reactivate an unused Section.
5. Confirm physical Delete is unavailable.
6. Confirm another company Section ID cannot be changed by POST tampering.
7. Confirm User creation is denied by default.
8. Grant `customer_admin_create_manager`; create a Manager in an active Section.
9. Grant `customer_admin_create_expert`; create an Expert in a Section that has an active Manager.
10. Confirm creating an Expert in a Section without an active Manager is rejected.
11. Confirm self-inactivation is rejected.
12. Confirm inactivating the last Manager while active Experts exist is rejected.
13. Confirm moving/inactivating a user who currently owns an open Draft is rejected.

## D. Manager creation rule

1. Use Section A with zero active Experts: Manager must be able to create a new request.
2. Add/activate the first Expert: Manager Create Request must disappear/be rejected.
3. Grant `manager_create_ticket_with_experts`: Manager Create Request must be allowed again.
4. Revoke it: Manager Create Request must immediately be denied again while Experts exist.

## E. Expert workflow

1. Expert creates Draft -> status `EXPERT_DRAFT`, Owner = Expert.
2. Expert edits title/description/type while Owner.
3. Expert cannot set/change priority.
4. Expert sends to Manager -> `MANAGER_REVIEW`, Owner = same Section Manager.
5. Expert cannot edit after ownership moved.
6. Expert cannot send directly to INTEK.
7. Expert cannot assign to another Expert.

## F. Manager revision workflow

1. Without `manager_return_ticket_to_expert`, Manager -> Expert action must be hidden and direct POST denied.
2. Grant permission.
3. Manager returns Request to an active Expert in the same Section -> `EXPERT_REVISION`.
4. Expert edits and returns to Manager -> `MANAGER_REVIEW`.
5. Expert from another Section must never be accepted.

## G. Comments / files / first view

1. Upload a file while current owner; verify it appears in Draft.
2. Remove an owned Draft file before Publish.
3. Add comments as Manager/Expert.
4. Confirm Company Admin is read-only for workflow comments/actions.
5. Open a Draft for the first time and verify one `TICKET_FIRST_VIEWED` Audit event.
6. Re-open and verify view_count increases without another first-view Audit event.

## H. Publish to INTEK

1. Manager owns a `MANAGER_REVIEW` Draft.
2. Click Send To INTEK.
3. Verify transaction creates exactly one row in `tickets`.
4. Verify initial row in `status` is created with `condition_pending` default behavior.
5. Verify Legacy ticket number is populated.
6. Verify Draft comments and files are moved from `customer_portal_draft` scope to `tickets` scope.
7. Verify publish map exists.
8. Verify Draft becomes `SENT_TO_INTEK` and is locked.
9. Retry publish directly and confirm duplicate publish is rejected.
10. Open the resulting Legacy ticket from IPanel and confirm it behaves like a normal IWEB ticket.

## I. Scope / security

1. Tamper `company_id`: Customer Portal endpoints must ignore it and derive company server-side.
2. Tamper Section IDs across companies: reject.
3. Tamper Draft IDs across companies/sections: reject.
4. Tamper User IDs across companies: reject.
5. Submit actions without CSRF: reject.
6. Confirm Default Deny for every exceptional business permission.
7. Confirm Customer Admin/Manager cannot grant their own permissions.
8. Confirm Expert has no management Audit/Report page.

## J. Reports / Audit

1. Company Admin sees company-wide Draft/Section/User metrics.
2. Manager sees only own Section metrics and Audit.
3. Aging count uses company `draft_aging_days` (default 14).
4. Verify all significant create/update/assign/status/publish/permission actions are appended to Audit.

## K. INTEK Global Audit

1. Open `ipanel/customer_portal_admin`.
2. In the Global Audit block select All Companies and confirm cross-company events are visible only to authorized IPanel users.
3. Filter by Company, Portal User, Role, Section and Event and verify each scope.
4. Confirm audit data is read-only and no Customer-side page exposes Grant/Revoke controls.

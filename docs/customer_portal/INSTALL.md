# IWEB Customer Portal - Installation Guide

## Scope

This package implements the new company/section/customer workflow while keeping the current Legacy IWEB ticket flow intact for companies whose feature flag is disabled.

No existing Legacy table is altered by the SQL package. All new database objects use the `iweb_customer_portal_` prefix.

## Required order

1. Take a code backup and database backup.
2. Apply the PHP files from this patch.
3. Execute `database/customer_portal/INSTALL_ALL.sql` on the target database.
4. Execute `database/customer_portal/007_verify.sql` and confirm that all eight Customer Portal tables are available.
5. Log in to IPanel with an account that has access to `systems / members / users` and `edit_operation`.
6. Open `ipanel/customer_portal_admin`.
7. Select the Pilot company.
8. Configure:
   - Portal legacy Unit.
   - Manager legacy RBAC.
   - Expert legacy RBAC.
   - Optional Admin RBAC mapping.
   - Draft aging days (default: 14).
9. Create or map at least one existing company user to Portal role `ADMIN` from the same IPanel page.
10. Define Sections and map/assign Manager/Expert profiles.
11. Grant only the exceptional permissions that are actually needed. All exceptional permissions are Default Deny.
12. Enable `customer_portal_workflow_enabled` only after the mappings and at least one active Portal Admin exist.
13. Run `SMOKE_TEST.md` on Test/Staging before Production.

## Feature flag behavior

- No settings row = workflow disabled.
- `customer_portal_workflow_enabled = 0` = Legacy IWEB behaves as before.
- `customer_portal_workflow_enabled = 1` = direct Legacy ticket creation is blocked for that company and the Customer Portal request workflow is used.

The flag is company-scoped. Pilot rollout can therefore be performed one company at a time.

## Optional legacy profile migration

`008_legacy_profile_migration_optional.sql` is intentionally NOT included in `INSTALL_ALL.sql`.

It can be used to create pending Portal profiles from Legacy RBAC captions:

- caption containing `مدیر` -> `MANAGER`
- caption containing `کارشناس` -> `EXPERT`

It never guesses a Section from the Legacy Unit. Migrated Manager/Expert profiles remain `section_id = NULL` until a real Section is assigned.

Before running it:

1. Set `@actor_admin_id` to the real INTEK Admin ID.
2. Run/review the Preview SELECT first.
3. Execute the migration only if the preview is correct.

## New IWEB route

`/iweb/customer_portal`

Views are provided for Dashboard, Sections, Users, Requests, New Request, Request Detail, Audit and Reports according to the signed-in Portal role.

## New IPanel route

`/ipanel/customer_portal_admin`

This page is the INTEK control plane for:

- Feature Flag
- Legacy Unit/RBAC mapping
- Portal profiles
- Default-Deny permission grant/revoke
- Aging threshold
- Read-only Global Audit with Company/User/Role/Section/Event filters

When the workflow is enabled, the last active Portal ADMIN of a company cannot be demoted or deactivated until another active Portal ADMIN exists.

## Deployment note

The service deliberately treats a missing settings/profile table as Feature Disabled while reading user context. This allows staged code deployment without enabling the new business path accidentally. Nevertheless, the supported deployment order is still SQL + code backup + staging validation before enabling any company.

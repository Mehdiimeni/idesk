# IWEB Customer Portal - Full Scenario Implementation

## Implemented business scope

- Company-scoped Feature Flag with Default OFF.
- Portal roles: `ADMIN`, `MANAGER`, `EXPERT` independent from Legacy `users.role`.
- Section create/edit/active/inactive with no physical delete.
- IPanel control plane for profile setup, mappings and permission grant/revoke.
- IPanel global Audit browser with Company/User/Role/Section/Event filters.
- Customer-side Manager/Expert user creation with explicit Default-Deny permissions.
- Manager/Expert assignment rules and company/section scope validation in Backend.
- Manager Create Request dynamic rule based on active Expert count + INTEK override permission.
- Expert -> Manager workflow.
- Manager -> Expert Revision only with explicit permission.
- Current-owner edit rule.
- Manager-only priority control and Manager-only Publish.
- Draft comments, attachments, first-view tracking and history.
- Transactional Publish into the existing Legacy `tickets` + `status` model.
- Publish mapping and duplicate publish protection.
- Audit and role-scoped Reports.
- Legacy ticket creation preserved when flag is OFF and blocked as a bypass when flag is ON.

## Exceptional permissions

All are Default Deny and only INTEK/IPanel can Grant/Revoke:

- `customer_admin_create_manager`
- `customer_admin_create_expert`
- `manager_create_expert`
- `manager_create_ticket_with_experts`
- `manager_return_ticket_to_expert`
- `customer_admin_assign_manager_to_section`
- `manager_assign_unassigned_expert`
- `customer_admin_reassign_expert`

## Database isolation

New tables only:

- `iweb_customer_portal_sections`
- `iweb_customer_portal_user_profiles`
- `iweb_customer_portal_ticket_audit_logs`
- `iweb_customer_portal_ticket_views`
- `iweb_customer_portal_company_settings`
- `iweb_customer_portal_permissions`
- `iweb_customer_portal_ticket_drafts`
- `iweb_customer_portal_ticket_publish_map`

No ALTER and no new Index is applied to Legacy tables.

## Operational safeguards

- Company/Section are always verified server-side.
- Customer-side code never trusts `company_id` from POST.
- CSRF is enforced for Customer Portal and IPanel control actions.
- Manager/Expert without Section can exist only as transition/pending profiles and are non-operational.
- Active Expert requires an active Manager for the Section.
- Customer-side Manager assignment only assigns an unassigned Manager and cannot silently replace/move another operational Manager.
- Users who own open Drafts cannot be inactivated or moved through Portal governance until the Drafts are resolved.
- A Company cannot enable the Feature Flag before required mapping and at least one active Portal Admin exist.
- While the workflow is enabled, IPanel cannot deactivate or demote the last active Portal Admin.
- Publish uses a DB transaction and `SELECT ... FOR UPDATE` to prevent partial/duplicate publishing.

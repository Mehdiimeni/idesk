# IWEB Customer Portal - Rollback

## Fast functional rollback

1. In IPanel `customer_portal_admin`, set `customer_portal_workflow_enabled = 0` for the affected company.
2. Confirm IWEB returns to the Legacy ticket creation path for that company.
3. Revert the code patch if required.

This is the preferred rollback because new Customer Portal records remain available for investigation and no Legacy schema rollback is needed.

## Database rollback

The package does not ALTER Legacy tables, so there is no Legacy DDL to undo.

Do NOT drop Customer Portal tables after real data has been created.

In an empty Test environment only, and only when no Customer Portal data must be preserved, the new tables can be dropped in reverse dependency order:

1. `iweb_customer_portal_ticket_publish_map`
2. `iweb_customer_portal_ticket_drafts`
3. `iweb_customer_portal_permissions`
4. `iweb_customer_portal_company_settings`
5. `iweb_customer_portal_ticket_views`
6. `iweb_customer_portal_ticket_audit_logs`
7. `iweb_customer_portal_user_profiles`
8. `iweb_customer_portal_sections`

Do not use this destructive rollback in Production after Pilot data exists.

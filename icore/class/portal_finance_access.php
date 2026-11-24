<?php
/** Financial access for active customer-portal managers only.
 *  Resolve company exactly as the legacy ticket detail page does: for older tickets,
 *  tickets.company_id may be NULL and company comes from the ticket creator's unit.
 *  Never use a client-supplied finance flag to grant access.
 */
if (!function_exists('iwebFinanceAllowed')) {
    function iwebFinanceAllowed($db, int $ticketId): bool
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $companyId = (int)($_SESSION['company_id'] ?? 0);
        if (!$db || $userId <= 0 || $companyId <= 0 || $ticketId <= 0) return false;
        try {
            // Authorization is independent from whether the NEW draft/publishing workflow
            // is enabled; a manager may have a valid finance grant on legacy tickets too.
            $sql = "SELECT 1
                    FROM iweb_customer_portal_user_profiles p
                    JOIN users u ON u.id=p.user_id AND u.status='Active'
                    JOIN iweb_customer_portal_sections s ON s.id=p.section_id
                        AND s.company_id=p.company_id AND s.is_active=1
                    JOIN iweb_customer_portal_permissions perm
                        ON perm.user_id=p.user_id AND perm.company_id=p.company_id
                        AND perm.permission_name='manager_finance_access' AND perm.is_allowed=1
                    JOIN tickets t ON t.id=?
                    LEFT JOIN users_details_view owner ON owner.user_id=t.user_id
                    LEFT JOIN admins_details_view admin_owner ON admin_owner.admin_id=t.admin_id
                    WHERE p.user_id=? AND p.company_id=? AND p.is_active=1
                      AND p.portal_role='MANAGER'
                      AND COALESCE(NULLIF(t.company_id,0), owner.company_id, admin_owner.company_id)=p.company_id
                      AND (t.user_id=p.user_id
                           OR EXISTS (
                               SELECT 1 FROM iweb_customer_portal_user_profiles ex
                               WHERE ex.user_id=t.user_id AND ex.company_id=p.company_id
                                 AND ex.section_id=p.section_id AND ex.portal_role='EXPERT' AND ex.is_active=1
                           )
                           OR EXISTS (
                               SELECT 1 FROM iweb_customer_portal_ticket_publish_map pm
                               WHERE pm.ticket_id=t.id AND pm.section_id=p.section_id
                                 AND EXISTS (SELECT 1 FROM iweb_customer_portal_ticket_drafts pd
                                     WHERE pd.id=pm.draft_id AND pd.company_id=p.company_id)
                           )
                           OR EXISTS (
                               SELECT 1 FROM forwards f
                               WHERE f.section_element_id=t.id
                                 AND f.section_part_name='tickets'
                                 AND f.receiver_type='u' AND f.receiver_person_id=p.user_id
                           )
                      ) LIMIT 1";
            $stmt = $db->prepare($sql);
            if (!$stmt) return false;
            $stmt->bind_param('iii', $ticketId, $userId, $companyId);
            $stmt->execute();
            $res=$stmt->get_result();
            $allowed=(bool)($res && $res->fetch_assoc());
            $stmt->close();
            return $allowed;
        } catch (Throwable $e) {
            error_log('[IWEB Finance Access] Verification failed: '.$e->getMessage());
            return false;
        }
    }
}

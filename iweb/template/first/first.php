<?php ///template/first/first.php ?>
<style>

   .dashboard-hero,
    .dashboard-stat-card,
    .dashboard-panel {
    background: var(--dashboard-card-bg, #ffffff);
    border: 1px solid var(--dashboard-border, #e9eef5);
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
}
.dashboard-actions,
    .dashboard-action-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.dashboard-action-bar {
    justify-content: flex-end;
    margin-bottom: 14px;
    background: var(--dashboard-card-bg, #ffffff);
    border: 1px solid var(--dashboard-border, #e9eef5);
    border-radius: 14px;
    padding: 10px;
}
.dashboard-soft-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .15s ease;
}
.dashboard-soft-btn:hover {
    transform: translateY(-1px);
    text-decoration: none;
}
.soft-secondary {
    background: #f3f5f7;
    color: #687789;
    border-color: #e3e8ee;
}
.soft-dark {
    background: #eef0f3;
    color: #3f4a59;
    border-color: #dde2e8;
}
.dashboard-stat-card {
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
    transition: all .18s ease;
}
.dashboard-stat-card:hover,
    .condition-row:hover,
    .kanban-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, .07);
}
.dashboard-panel-title,
    .dashboard-panel-header h4 {
    font-size: 15px;
    font-weight: 800;
    color: var(--dashboard-title, #263447);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.dashboard-panel-header span {
    display: block;
    margin-top: 4px;
    color: var(--dashboard-muted, #8a97a6);
    font-size: 12px;
}
.condition-row {
    text-decoration: none;
    color: var(--dashboard-text, #334155);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 4px;
    border-bottom: 1px solid var(--dashboard-border, #e9eef5);
    transition: all .16s ease;
}
.condition-row:last-child {
    border-bottom: 0;
}
.condition-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.dashboard-table {
    border-collapse: separate;
    border-spacing: 0 8px;
}
.dashboard-table thead th {
    background: transparent;
    border: 0;
    color: var(--dashboard-muted, #8a97a6);
    font-size: 12px;
    font-weight: 800;
    padding: 8px 12px;
    white-space: nowrap;
}
.dashboard-table tbody tr {
    background: var(--dashboard-row-bg, #fbfcfe);
    box-shadow: inset 0 0 0 1px var(--dashboard-border, #e9eef5);
    transition: all .15s ease;
}
.dashboard-table tbody tr:hover {
    box-shadow: inset 0 0 0 1px var(--dashboard-border-hover, #dfe7f1), 0 8px 18px rgba(15, 23, 42, .05);
}
.dashboard-table tbody td {
    border: 0;
    padding: 13px 12px;
    color: var(--dashboard-text, #334155);
    vertical-align: middle;
}
.dashboard-table tbody td:first-child {
    border-radius: 12px 0 0 12px;
}
.dashboard-table tbody td:last-child {
    border-radius: 0 12px 12px 0;
}
.ticket-number,
    .confirm-badge,
    .type-badge,
    .priority-badge,
    .kanban-tag,
    .soft-status-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.4;
    border: 1px solid transparent;
}
.type-badge {
    background: #eef3ff;
    color: #4b6cb7;
    border-color: #dbe6ff;
}
.soft-success {
    background: #eefaf3;
    color: #2f8f5b;
    border-color: #d7f0e2;
}
.soft-danger {
    background: #fff0f0;
    color: #d64545;
    border-color: #ffd7d7;
}
.soft-warning {
    background: #fff7e6;
    color: #b7791f;
    border-color: #ffe6b3;
}
.soft-info {
    background: #eef8ff;
    color: #3182bd;
    border-color: #d9efff;
}
.soft-primary {
    background: #eef3ff;
    color: #4b6cb7;
    border-color: #dbe6ff;
}
.soft-secondary,
    .soft-dark,
    .soft-light {
    background: #f4f6f8;
    color: #64748b;
    border-color: #e6ebf0;
}
.table-actions {
    display: flex;
    justify-content: center;
    gap: 7px;
}
.action-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all .15s ease;
    border: 1px solid transparent;
}
.action-icon:hover {
    transform: translateY(-1px);
}
.action-icon.primary {
    background: #eef3ff;
    color: #4b6cb7;
    border-color: #dbe6ff;
}
.action-icon.secondary {
    background: #f4f6f8;
    color: #64748b;
    border-color: #e6ebf0;
}
.kanban-list {
    padding-right: 4px;
}
.kanban-tag {
    background: var(--dashboard-card-bg, #ffffff);
    color: #4b6cb7;
    border-color: #dbe6ff;
    flex: 0 0 auto;
}
html[data-layout-color="dark"] .dashboard-page,
    body[data-layout-color="dark"] .dashboard-page,
    body[data-bs-theme="dark"] .dashboard-page,
    [data-bs-theme="dark"] .dashboard-page {
    --dashboard-bg: #161b22;
    --dashboard-card-bg: #1f2630;
    --dashboard-row-bg: #242c37;
    --dashboard-border: #323b48;
    --dashboard-border-hover: #465365;
    --dashboard-title: #eef2f7;
    --dashboard-text: #d7dee8;
    --dashboard-muted: #98a6b8;
}
html[data-layout-color="dark"] .soft-primary,
    body[data-layout-color="dark"] .soft-primary,
    body[data-bs-theme="dark"] .soft-primary,
    [data-bs-theme="dark"] .soft-primary {
    background: rgba(75, 108, 183, .16);
    color: #a9c0ff;
    border-color: rgba(169, 192, 255, .18);
}
html[data-layout-color="dark"] .soft-warning,
    body[data-layout-color="dark"] .soft-warning,
    body[data-bs-theme="dark"] .soft-warning,
    [data-bs-theme="dark"] .soft-warning {
    background: rgba(183, 121, 31, .16);
    color: #ffd58a;
    border-color: rgba(255, 213, 138, .18);
}
html[data-layout-color="dark"] .soft-danger,
    body[data-layout-color="dark"] .soft-danger,
    body[data-bs-theme="dark"] .soft-danger,
    [data-bs-theme="dark"] .soft-danger {
    background: rgba(214, 69, 69, .14);
    color: #ffaaaa;
    border-color: rgba(255, 170, 170, .18);
}
html[data-layout-color="dark"] .soft-status-badge,
    body[data-layout-color="dark"] .soft-status-badge,
    body[data-bs-theme="dark"] .soft-status-badge,
    [data-bs-theme="dark"] .soft-status-badge,
    html[data-layout-color="dark"] .ticket-number,
    body[data-layout-color="dark"] .ticket-number,
    body[data-bs-theme="dark"] .ticket-number,
    [data-bs-theme="dark"] .ticket-number,
    html[data-layout-color="dark"] .confirm-badge,
    body[data-layout-color="dark"] .confirm-badge,
    body[data-bs-theme="dark"] .confirm-badge,
    [data-bs-theme="dark"] .confirm-badge,
    html[data-layout-color="dark"] .type-badge,
    body[data-layout-color="dark"] .type-badge,
    body[data-bs-theme="dark"] .type-badge,
    [data-bs-theme="dark"] .type-badge {
    background: rgba(255, 255, 255, .06);
    border-color: rgba(255, 255, 255, .10);
    color: #d7dee8;
}
html[data-layout-color="dark"] .stat-icon,
    body[data-layout-color="dark"] .stat-icon,
    body[data-bs-theme="dark"] .stat-icon,
    [data-bs-theme="dark"] .stat-icon,
    html[data-layout-color="dark"] .action-icon.primary,
    body[data-layout-color="dark"] .action-icon.primary,
    body[data-bs-theme="dark"] .action-icon.primary,
    [data-bs-theme="dark"] .action-icon.primary {
    background: rgba(75, 108, 183, .16);
    color: #a9c0ff;
    border-color: rgba(169, 192, 255, .18);
}
html[data-layout-color="dark"] .action-icon.secondary,
    body[data-layout-color="dark"] .action-icon.secondary,
    body[data-bs-theme="dark"] .action-icon.secondary,
    [data-bs-theme="dark"] .action-icon.secondary {
    background: rgba(255, 255, 255, .06);
    color: #b7c1ce;
    border-color: rgba(255, 255, 255, .10);
}
.dashboard-top-summary {
    background: var(--dashboard-card-bg, #ffffff);
    border: 1px solid var(--dashboard-border, #e9eef5);
    border-radius: 18px;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
}
.dashboard-ticket-total {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 220px;
    color: inherit;
}
.dashboard-ticket-total:hover {
    color: inherit;
}
.stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: #eef3ff;
    color: #4b6cb7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}
.stat-label {
    margin-top: 6px;
    color: var(--dashboard-muted, #8a97a6);
    font-size: 14px;
}
.dashboard-main-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}
.dashboard-main-btn {
    min-width: 126px;
    min-height: 42px;
    padding: 9px 16px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .15s ease;
}
.dashboard-main-btn:hover {
    transform: translateY(-1px);
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
}
.soft-primary-btn {
    background: #eef3ff;
    color: #4b6cb7;
    border-color: #dbe6ff;
}
.soft-success-btn {
    background: #eefaf3;
    color: #2f8f5b;
    border-color: #d7f0e2;
}
.soft-danger-btn {
    background: #fff0f0;
    color: #d64545;
    border-color: #ffd7d7;
}
.soft-warning-btn {
    background: #fff7e6;
    color: #b7791f;
    border-color: #ffe6b3;
}
.dashboard-command-bar {
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid var(--dashboard-border, #e9eef5);
    border-radius: 18px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .035);
}
.dashboard-command-title {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 190px;
}
.dashboard-command-title strong {
    display: block;
    color: #263447;
    font-size: 14px;
    font-weight: 900;
}
.dashboard-command-title small {
    color: #8a97a6;
    font-size: 11px;
}
.dashboard-live-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #36b37e;
    box-shadow: 0 0 0 5px rgba(54, 179, 126, .12);
    flex: 0 0 auto;
}
.soft-neutral-btn {
    background: #f5f7fa;
    color: #526174;
    border-color: #e7ebf0;
}
.dashboard-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}
.dashboard-kpi-card {
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 18px;
    padding: 17px;
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 126px;
    color: #263447;
    box-shadow: 0 6px 18px rgba(15, 23, 42, .035);
    transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
}
.dashboard-kpi-card::after {
    content: "";
    position: absolute;
    inset-inline-end: -22px;
    top: -22px;
    width: 82px;
    height: 82px;
    border-radius: 50%;
    background: currentColor;
    opacity: .035;
}
.dashboard-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
    border-color: #dde6f1;
}
.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex: 0 0 auto;
}
.kpi-body {
    min-width: 0;
}
.kpi-body>span {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: #7f8b99;
    margin-bottom: 5px;
}
.kpi-body strong {
    display: block;
    font-size: 27px;
    line-height: 1.05;
    font-weight: 900;
    color: #263447;
}
.kpi-body small {
    display: block;
    color: #97a2af;
    margin-top: 7px;
    font-size: 11px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-primary .kpi-icon {
    background: #eef3ff;
    color: #4b6cb7;
}
.kpi-info .kpi-icon {
    background: #eaf8ff;
    color: #168aad;
}
.kpi-warning .kpi-icon {
    background: #fff6df;
    color: #b7791f;
}
.kpi-success .kpi-icon {
    background: #ebf9f1;
    color: #2f8f5b;
}
.dashboard-insight-row>div {
    display: flex;
}
.dashboard-activity-panel,
    .dashboard-insight-row .dashboard-panel {
    width: 100%;
}
.activity-metric-grid {
    display: grid;
    grid-template-columns: 1fr 1fr .8fr;
    gap: 12px;
    margin-top: 12px;
}
.activity-metric {
    border: 1px solid #edf1f5;
    border-radius: 15px;
    padding: 14px;
    background: #fbfcfe;
}
.activity-metric-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #66758a;
    font-size: 12px;
    font-weight: 800;
    margin-bottom: 13px;
}
.activity-metric-head i {
    font-size: 18px;
    color: #8ca2bf;
}
.activity-pair {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.activity-pair>div {
    background: #ffffff;
    border: 1px solid #edf1f5;
    border-radius: 12px;
    padding: 10px;
}
.activity-pair strong,
    .activity-single-value {
    display: block;
    font-size: 23px;
    font-weight: 900;
    color: #263447;
    line-height: 1.1;
}
.activity-pair span,
    .compact-metric small {
    color: #8a97a6;
    font-size: 11px;
    display: block;
    margin-top: 5px;
}
.compact-metric {
    background: linear-gradient(145deg, #fffaf0, #ffffff);
}
.compact-metric .activity-single-value {
    color: #b7791f;
    font-size: 30px;
}
.status-overview-list {
    display: grid;
    gap: 11px;
    margin-top: 12px;
}
.status-overview-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
}
.status-overview-head strong {
    color: #445267;
    font-size: 13px;
}
.status-progress {
    width: 100%;
    height: 5px;
    border-radius: 999px;
    background: #edf1f5;
    overflow: hidden;
}
.status-progress span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #8aa7d8, #4b6cb7);
}
.dashboard-view-all {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #4b6cb7;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
}
.dashboard-view-all:hover {
    color: #324e86;
}
.dashboard-recent-panel {
    padding-bottom: 9px;
}
.dashboard-recent-table tbody td {
    padding-top: 10px;
    padding-bottom: 10px;
    font-size: 12px;
}
.recent-date {
    color: #7b8797 !important;
    white-space: nowrap;
    font-size: 11px !important;
}
.dashboard-empty-state {
    color: #8a97a6;
    font-size: 12px;
    text-align: center;
    padding: 22px 8px;
    border: 1px dashed #dfe6ee;
    border-radius: 12px;
    background: #fbfcfe;
}
.dashboard-hero .dashboard-title,
    .dashboard-hero .dashboard-subtitle,
    .dashboard-hero .dashboard-user-info strong,
    .dashboard-hero .dashboard-user-info span {
    color: #fff !important;
}
.kpi-blocker .kpi-icon {
    background: #fff0f0;
    color: #dc3545;
}
.kpi-critical .kpi-icon {
    background: #fff6df;
    color: #d97706;
}
.kpi-normal .kpi-icon {
    background: #eef3ff;
    color: #4b6cb7;
}
.dashboard-period-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--bs-tertiary-bg, #f3f6fb);
    border: 1px solid var(--bs-border-color, #e9eef5);
    color: var(--bs-secondary-color, #6c757d);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.monthly-activity-total {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    margin-top: 12px;
    border-radius: 14px;
    background: var(--bs-tertiary-bg, #f8fafc);
    border: 1px solid var(--bs-border-color, #e9eef5);
}
.monthly-activity-total span,
    .monthly-activity-total small {
    color: var(--bs-secondary-color, #6c757d);
}
.monthly-activity-total strong {
    display: block;
    margin-top: 4px;
    font-size: 30px;
    line-height: 1;
    color: var(--bs-body-color, #263447);
}
.priority-guide-list {
    display: grid;
    gap: 10px;
    margin-top: 14px;
}
.priority-guide-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 14px;
    border: 1px solid var(--bs-border-color, #e9eef5);
    border-radius: 13px;
    background: var(--bs-secondary-bg, #fff);
}
.priority-guide-main {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: 150px;
}
.priority-guide-main b {
    min-width: 28px;
    padding: 2px 8px;
    border-radius: 999px;
    text-align: center;
    background: var(--bs-tertiary-bg, #f3f6fb);
    color: var(--bs-body-color, #263447);
}
.priority-guide-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    flex: 0 0 auto;
}
.priority-guide-item.blocker .priority-guide-dot {
    background: #dc3545;
}
.priority-guide-item.critical .priority-guide-dot {
    background: #d97706;
}
.priority-guide-item.normal .priority-guide-dot {
    background: #4b6cb7;
}
.priority-guide-item small {
    color: var(--bs-secondary-color, #6c757d);
    text-align: end;
    line-height: 1.6;
}
.dashboard-finance-panel {
    padding-bottom: 18px;
}
.finance-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 10px;
    margin-top: 12px;
}
.finance-status-item {
    margin: 0;
    padding: 12px 14px;
    border: 1px solid var(--bs-border-color, #e9eef5);
    border-radius: 13px;
    background: var(--bs-secondary-bg, #fff);
}
.finance-progress span {
    background: linear-gradient(90deg, #e7b24d, #c78b19);
}
.finance-status-grid .dashboard-empty-state {
    grid-column: 1 / -1;
}
.dashboard-hero .dashboard-title,
    .dashboard-hero .dashboard-user-info strong {
    color: var(--cu-text) !important;
}
.dashboard-hero .dashboard-subtitle,
    .dashboard-hero .dashboard-user-info span {
    color: var(--cu-muted) !important;
    opacity: 1 !important;
}
.dashboard-hero .btn-light {
    background: #fff;
    border-color: #dfe6f1;
    color: #435067;
}
.portal-workspace {
    border: 1px solid var(--cu-border);
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(29, 42, 69, .05);
    overflow: hidden;
}
.portal-workspace-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    padding: 18px 20px;
    background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
    border-bottom: 1px solid var(--cu-border);
}
.portal-workspace-title-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}
.portal-workspace-icon {
    width: 48px;
    height: 48px;
    border-radius: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #edf3ff;
    color: var(--cu-primary);
    font-size: 23px;
    flex: 0 0 auto;
}
.portal-workspace-eyebrow {
    color: var(--cu-primary);
    font-size: 11px;
    font-weight: 800;
    margin-bottom: 2px;
}
.portal-workspace h4 {
    margin: 0;
    color: var(--cu-text);
    font-size: 17px;
    font-weight: 800;
}
.portal-workspace p {
    margin: 4px 0 0;
    color: var(--cu-muted);
    font-size: 12px;
}
.portal-workspace-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.portal-role-badge,
    .portal-state-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 999px;
    padding: 7px 10px;
    font-size: 11px;
    font-weight: 700;
}
.portal-role-badge {
    background: #eef3ff;
    color: var(--cu-primary);
    border: 1px solid #d8e5ff;
}
.portal-state-badge.active {
    background: #edfdf3;
    color: #15803d;
    border: 1px solid #c7f2d5;
}
.portal-state-badge.inactive {
    background: #fff1f2;
    color: #b91c1c;
    border: 1px solid #ffd6db;
}
.portal-workspace-nav {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    padding: 14px;
}
.portal-nav-item {
    min-height: 74px;
    border: 1px solid var(--cu-border);
    border-radius: 14px;
    background: #fff;
    color: #435067;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    font-weight: 700;
    transition: .18s ease;
}
.portal-nav-item i {
    width: 34px;
    height: 34px;
    border-radius: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef3ff;
    color: var(--cu-primary);
    font-size: 17px;
    flex: 0 0 auto;
}
.portal-nav-item:hover {
    transform: translateY(-2px);
    border-color: #cbd8fb;
    box-shadow: 0 10px 24px rgba(53, 109, 243, .08);
    color: var(--cu-primary);
}
.portal-workspace-disabled-note {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 14px;
    padding: 12px 14px;
    border: 1px solid #fde7b0;
    background: #fffaf0;
    color: #9a6700;
    border-radius: 12px;
    font-size: 12px;
}
.dashboard-command-bar,
    .dashboard-kpi-card,
    .dashboard-panel {
    border-color: var(--cu-border) !important;
    box-shadow: 0 8px 25px rgba(29, 42, 69, .04) !important;
}
.dashboard-page,
    .dashboard-page .content,
    .dashboard-page .row,
    .dashboard-page .row>* {
    min-width: 0;
}
.dashboard-page>.content>.container-fluid {
    width: 100%;
    max-width: 1320px !important;
    margin-inline: auto !important;
    padding: 18px 18px 30px !important;
    box-sizing: border-box;
}
.dashboard-page .dashboard-hero,
    .dashboard-page .portal-workspace,
    .dashboard-page .dashboard-command-bar,
    .dashboard-page .dashboard-kpi-card,
    .dashboard-page .dashboard-panel {
    border: 1px solid #e5eaf2 !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 25px rgba(29, 42, 69, .04) !important;
}
.dashboard-page .dashboard-hero {
    padding: 18px 20px !important;
    border-radius: 18px !important;
    gap: 14px;
    flex-wrap: wrap;
}
.dashboard-page .dashboard-title {
    font-size: clamp(1.03rem, 2.1vw, 1.25rem);
    line-height: 1.55;
}
.dashboard-page .dashboard-subtitle,
    .dashboard-page .dashboard-user-info span {
    font-size: .84rem;
    line-height: 1.8;
}
.dashboard-page .dashboard-user {
    min-width: 0;
    max-width: 100%;
    flex-wrap: wrap;
}
.dashboard-page .dashboard-user-info {
    min-width: 0;
    overflow-wrap: anywhere;
}
.dashboard-page .dashboard-actions {
    min-width: 0;
    max-width: 100%;
}
.dashboard-page .dashboard-actions .btn {
    min-height: 36px;
}
.dashboard-page .portal-workspace-head {
    padding: 14px 18px;
    gap: 12px;
}
.dashboard-page .portal-workspace-title-wrap {
    flex: 1 1 230px;
    min-width: 0;
}
.dashboard-page .portal-workspace-title-wrap>div,
    .dashboard-page .portal-workspace-meta {
    min-width: 0;
}
.dashboard-page .portal-workspace p {
    line-height: 1.8;
}
.dashboard-page .portal-workspace-nav {
    grid-template-columns: repeat(auto-fit, minmax(142px, 1fr));
    gap: 10px;
    padding: 12px;
}
.dashboard-page .portal-nav-item {
    min-width: 0;
    min-height: 44px;
    padding: 8px 10px;
    justify-content: center;
    border: 1px solid transparent;
    background: #f6f8fc;
    border-radius: 12px;
    font-size: .83rem;
    text-align: center;
    overflow-wrap: anywhere;
}
.dashboard-page .portal-nav-item i {
    width: auto;
    height: auto;
    border: 0;
    background: transparent;
    border-radius: 0;
    font-size: 17px;
}
.dashboard-page .portal-nav-item:hover {
    transform: none;
    background: #eef3ff;
    border-color: #d8e5ff;
}
.dashboard-page .dashboard-command-bar,
    .dashboard-page .dashboard-panel-header {
    flex-wrap: wrap;
}
.dashboard-page .dashboard-command-bar {
    gap: 12px;
    padding: 12px 14px;
}
.dashboard-page .dashboard-command-title {
    min-width: 0;
    flex: 1 1 180px;
}
.dashboard-page .dashboard-main-actions {
    min-width: 0;
    max-width: 100%;
    gap: 8px;
}
.dashboard-page .dashboard-main-btn {
    min-width: 0;
    min-height: 38px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: .8rem;
    flex: 0 1 auto;
}
.dashboard-page .dashboard-kpi-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}
.dashboard-page .dashboard-kpi-card {
    min-width: 0;
    min-height: 104px;
    padding: 14px;
    gap: 10px;
}
.dashboard-page .dashboard-kpi-card .kpi-body,
    .dashboard-page .dashboard-kpi-card .kpi-body>span,
    .dashboard-page .dashboard-kpi-card .kpi-body small {
    min-width: 0;
    overflow-wrap: anywhere;
}
.dashboard-page .dashboard-kpi-card .kpi-body strong {
    font-size: clamp(1.25rem, 2.1vw, 1.7rem);
}
.dashboard-page .dashboard-kpi-card .kpi-icon {
    width: 40px;
    height: 40px;
    font-size: 19px;
}
.dashboard-page .dashboard-panel {
    min-width: 0;
    overflow: hidden;
}
.dashboard-page .dashboard-panel-header>div {
    min-width: 0;
}
.dashboard-page .table-responsive {
    min-width: 0;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.dashboard-page .table-responsive table {
    width: 100%;
}
.dashboard-page>.content {
    width: 100% !important;
    max-width: none !important;
    margin-inline: 0 !important;
    box-sizing: border-box;
}

.dashboard-page {
    --cu-border: #e5eaf2;
    --cu-text: #172033;
    --cu-muted: #7a8699;
    --cu-soft: #f6f8fc;
    --cu-primary: #356df3;
    background: #f8fbff !important;
    width: 100% !important;
    max-width: none !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    box-sizing: border-box;
    --db-border: #e5eaf2;
    --db-text: #172033;
    --db-muted: #7a8699;
    --db-soft: #f6f8fc;
    --db-primary: #356df3;
    --db-success: #20a66a;
    background: #f8fbff;
    margin-top: 0;
    min-height: calc(100vh - 130px);
}

.dashboard-hero {
    border: 1px solid #e2e8f5 !important;
    border-radius: 20px !important;
    padding: 22px 24px !important;
    background: radial-gradient(circle at top left, rgba(53, 109, 243, .11), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%) !important;
    box-shadow: 0 12px 35px rgba(37, 54, 89, .06) !important;
    color: var(--cu-text) !important;
    border: 1px solid #e2e8f5;
    border-radius: 20px;
    padding: 22px 24px;
    background: radial-gradient(circle at top left, rgba(53, 109, 243, .11), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f6f9ff 100%);
    box-shadow: 0 12px 35px rgba(37, 54, 89, .06);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
}

.dashboard-page .container-fluid {
    width: 100%;
    max-width: none;
    margin: 0 auto;
    padding-left: 18px;
    padding-right: 18px;
}
.dashboard-hero-main {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}
.dashboard-hero-icon {
    width: 48px;
    height: 48px;
    border-radius: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #edf3ff;
    color: var(--db-primary);
    font-size: 23px;
    flex: 0 0 auto;
}
.dashboard-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
    color: var(--db-text);
    font-weight: 800;
    font-size: 1.3rem;
}
.dashboard-subtitle {
    margin-top: 6px;
    color: var(--db-muted);
    font-size: 12px;
}
.dashboard-user,
    .dashboard-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.dashboard-user {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 8px 10px 8px 12px;
    border: 1px solid var(--db-border);
    border-radius: 15px;
    background: rgba(255, 255, 255, .76);
}
.dashboard-user-info {
    text-align: right;
    min-width: 150px;
}
.dashboard-user-info strong {
    display: block;
    color: var(--db-text);
    font-size: 13px;
    font-weight: 800;
}
.dashboard-user-info span {
    display: block;
    margin-top: 3px;
    color: var(--db-muted);
    font-size: 10px;
}
.dashboard-actions .btn {
    border-radius: 10px;
    min-height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    box-shadow: none;
}
.dashboard-actions .btn-light {
    color: #40506a;
    border: 1px solid #dce3ee;
    background: #fff;
}
.dashboard-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
}
.dashboard-summary-card {
    min-height: 158px;
    background: #fff;
    color: var(--db-text);
    border: 1px solid var(--db-border);
    border-radius: 16px;
    padding: 16px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 7px 20px rgba(28, 41, 67, .04);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}
.dashboard-summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(53, 109, 243, .08);
    border-color: #cbd8fb;
}
.dashboard-summary-card::after {
    content: "";
    position: absolute;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    inset: auto -36px -44px auto;
    background: rgba(53, 109, 243, .035);
    pointer-events: none;
}
.dashboard-summary-card-primary,
    .dashboard-summary-card-success,
    .dashboard-period-week,
    .dashboard-period-month {
    border-top-width: 1px;
}
.summary-card-top,
    .activity-card-header,
    .activity-values {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.stat-icon,
    .activity-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.stat-icon.primary {
    background: #eef3ff;
    color: var(--db-primary);
}
.stat-icon.success {
    background: #edf9f3;
    color: var(--db-success);
}
.activity-icon.weekly {
    color: #7c3aed;
    background: #f3efff;
}
.activity-icon.monthly {
    color: #d97706;
    background: #fff7e8;
}
.summary-open-icon {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #8b98ad;
    background: var(--db-soft);
    border: 1px solid var(--db-border);
}
.summary-card-body {
    margin-top: 14px;
}
.stat-label,
    .activity-eyebrow {
    color: var(--db-muted);
    font-size: 11px;
    font-weight: 700;
}
.stat-number {
    margin-top: 5px;
    color: var(--db-text);
    font-size: 28px;
    line-height: 1;
    font-weight: 800;
}
.summary-caption {
    margin-top: 7px;
    color: var(--db-muted);
    font-size: 10px;
}
.summary-mini-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}
.summary-mini-stats span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 7px;
    border-radius: 999px;
    background: var(--db-soft);
    border: 1px solid var(--db-border);
    color: var(--db-muted);
    font-size: 9px;
}
.summary-mini-stats strong {
    color: var(--db-text);
    font-size: 10px;
}
.dashboard-activity-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.activity-card-header strong {
    display: block;
    margin-top: 3px;
    font-size: 13px;
    color: var(--db-text);
}
.activity-values {
    margin-top: 18px;
    background: var(--db-soft);
    border: 1px solid var(--db-border);
    border-radius: 12px;
    padding: 10px 12px;
    gap: 10px;
}
.activity-value {
    flex: 1;
    min-width: 0;
}
.activity-value span {
    display: block;
    color: var(--db-muted);
    font-size: 9px;
    margin-bottom: 3px;
}
.activity-value strong {
    display: block;
    color: var(--db-text);
    font-size: 19px;
    line-height: 1.1;
}
.activity-divider {
    width: 1px;
    align-self: stretch;
    background: var(--db-border);
}
.dashboard-lower-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    align-items: start;
}
.dashboard-panel,
    .dashboard-stat-card {
    background: #fff;
    color: var(--db-text);
    border: 1px solid var(--db-border);
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(29, 42, 69, .04);
}
.dashboard-grid-panel {
    min-width: 0;
    margin-bottom: 0 !important;
    overflow: hidden;
}
.dashboard-panel {
    padding: 0;
}
.dashboard-panel-header {
    padding-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 16px 18px;
    border-bottom: 1px solid var(--db-border);
    margin-bottom: 0;
    background: linear-gradient(180deg, #fff 0%, #fbfdff 100%);
}
.dashboard-panel-header h4 {
    font-size: 14px;
    font-weight: 800;
    color: var(--db-text);
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}
.dashboard-grid-panel .kanban-list,
    .dashboard-grid-panel .todo-list {
    max-height: 360px !important;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-gutter: stable;
    padding: 14px;
}
.kanban-item,
    .todo-item {
    background: #fbfcfe;
    color: var(--db-text);
    border: 1px solid #e8edf3;
    border-radius: 14px;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
}
.kanban-item:hover,
    .todo-item:hover {
    transform: translateY(-1px);
    border-color: #cbd8fb;
    box-shadow: 0 8px 20px rgba(53, 109, 243, .06);
}
.kanban-item {
    border: 1px solid var(--dashboard-border, #e9eef5);
    border-radius: 16px;
    background: var(--dashboard-row-bg, #fbfcfe);
    transition: all .18s ease;
    padding: 14px;
    margin-bottom: 10px;
}
.todo-item {
    padding: 12px;
    margin-bottom: 9px;
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 9px 12px;
}
.kanban-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
    margin-bottom: 8px;
}
.kanban-date {
    margin-left: auto;
    color: var(--db-muted);
    font-size: 10px;
}
.kanban-title,
    .todo-title a {
    color: var(--db-text);
    font-weight: 800;
    text-decoration: none;
}
.kanban-title {
    color: var(--dashboard-title, #263447);
    font-weight: 800;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    font-size: 12px;
}
.kanban-description {
    color: var(--db-muted);
    font-size: 11px;
    line-height: 1.8;
}
.todo-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
}
.todo-title i {
    color: var(--db-primary);
}
.todo-progress {
    grid-column: 1 / -1;
}
.todo-progress .progress {
    height: 5px;
    border-radius: 999px;
    background: #e7edf6;
}
.todo-progress .progress-bar {
    border-radius: 999px;
}
.dashboard-native-scroll {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    overscroll-behavior: contain;
    scrollbar-gutter: stable;
}
.dashboard-native-scroll::-webkit-scrollbar {
    width: 6px;
}
.dashboard-native-scroll::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, .24);
    border-radius: 999px;
}
.dashboard-native-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.dashboard-requests-panel {
    overflow: hidden;
    padding: 0;
}
.dashboard-request-count {
    min-width: 30px;
    height: 30px;
    padding: 0 9px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef3ff;
    color: var(--db-primary);
    border: 1px solid #d8e5ff;
    font-size: 11px;
    font-weight: 800;
}
.dashboard-requests-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    max-height: 420px;
    padding: 14px;
}
.dashboard-request-card {
    min-width: 0;
    margin: 0;
    padding: 12px;
    border-radius: 14px;
    background: #fbfcfe;
    border: 1px solid #e8edf3;
    box-shadow: none;
    transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
}
.dashboard-request-card:hover {
    transform: translateY(-1px);
    border-color: #cbd8fb;
    box-shadow: 0 8px 20px rgba(53, 109, 243, .06);
}
.dashboard-request-card-confirm {
    border-inline-start: 3px solid #72d5a4;
}
.dashboard-request-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
}
.dashboard-request-type {
    min-width: 0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: var(--db-muted);
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}
.dashboard-request-type i {
    font-size: 14px;
    color: var(--db-primary);
}
.dashboard-request-card-body {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: end;
    gap: 10px;
}
.dashboard-request-field {
    min-width: 0;
}
.dashboard-request-field-label {
    display: block;
    margin-bottom: 5px;
    color: var(--db-muted);
    font-size: 9px;
    font-weight: 700;
}
.dashboard-request-card .dashboard-input {
    width: 100% !important;
    max-width: 220px;
    min-width: 0;
    direction: ltr;
    text-align: center;
}
.dashboard-request-response {
    min-height: 34px;
    max-width: 100%;
    padding: 7px 10px;
    display: inline-flex;
    align-items: center;
    border-radius: 10px;
    background: #fff;
    border: 1px solid var(--db-border);
    color: var(--db-text);
    font-size: 11px;
    font-weight: 700;
    word-break: break-word;
}
.dashboard-request-action {
    min-width: 88px;
    height: 34px;
    border-radius: 10px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    box-shadow: none !important;
    font-size: 10px;
}
.dashboard-request-action:disabled {
    cursor: wait;
    opacity: .7;
}
.ticket-number,
    .kanban-tag,
    .type-badge {
    background: #eef3ff;
    color: var(--db-primary);
    border: 1px solid #d8e5ff;
}
.status-badge,
    .confirm-badge,
    .type-badge,
    .priority-badge,
    .kanban-tag {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: 9px;
    font-weight: 700;
}
.ticket-number {
    color: #4b6cb7;
    background: #eef3ff;
    border-color: #dbe6ff;
    font-weight: 800;
    padding: 4px 8px;
    border-radius: 999px;
    display: inline-block;
    text-decoration: none;
    font-size: 10px;
}
.confirm-badge {
    border-color: #e6ebf0;
    background: var(--db-soft);
    color: var(--db-text);
    border: 1px solid var(--db-border);
}
.priority-low {
    border-color: #dceeff;
    background: #eef3ff;
    color: #356df3;
}
.priority-medium {
    border-color: #ffe6b3;
    background: #fff7e8;
    color: #d97706;
}
.priority-high {
    border-color: #ffd7d7;
    background: #fff0f2;
    color: #d43f55;
}
.dashboard-input,
    .dashboard-page .form-control {
    background: #fff;
    color: var(--db-text);
    border: 1px solid #dbe2ed;
    border-radius: 10px;
    min-height: 34px;
    font-size: 11px;
}
.dashboard-page .form-control::placeholder {
    color: #9aa5b7;
}
 
@media (max-width: 575.98px) {
    .dashboard-page>.content>.container-fluid {
        padding: 12px 12px 24px !important;
    }
}
    /* Single content wrapper: global menu already opens .content-page. */
    .dashboard-page {
        width: 100%;
        max-width: none;
        margin: 0;
        min-width: 0;
        box-sizing: border-box;
    }
    .dashboard-page > .content > .container-fluid {
        width: 100%;
        max-width: none !important;
        margin-inline: auto !important;
        padding-inline: 12px !important;
        box-sizing: border-box;
    }
    .dashboard-page .row,
    .dashboard-page .row > * { min-width: 0; }
    @media (max-width: 767.98px) {
        .dashboard-page > .content > .container-fluid { padding-inline: 12px !important; }
        .dashboard-page .dashboard-hero { flex-direction: column; align-items: stretch; }
        .dashboard-page .dashboard-user { width: 100%; flex-wrap: wrap; }
    }
</style>

<div class="content-page dashboard-page">
    <div class="content">
        <div class="container-fluid">

            <div class="dashboard-hero mb-4">
                <div>
                    <h3 class="dashboard-title">
                        <i class="ri-dashboard-line"></i>
                        <?php echo _lang['my_dashboard']; ?>
                    </h3>
                    <div class="dashboard-subtitle">
                        <?php echo _lang['project_name_owner']; ?>
                    </div>
                </div>

                <div class="dashboard-user">
                    <div class="dashboard-user-info">
                        <strong><?php echo $_SESSION["name"]; ?></strong>
                        <span>
                            <?php
                            if (!empty($customerPortalFirstContext['profile_valid'])) {
                                $portalRole = strtolower((string) ($customerPortalFirstContext['portal_role'] ?? ''));
                                $roleLabelKey = 'cp_role_' . $portalRole;
                                $roleLabel = isset(_lang[$roleLabelKey]) ? _lang[$roleLabelKey] : $portalRole;
                                $sectionLabel = trim((string) ($customerPortalFirstContext['section_name'] ?? ''));
                                echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8');
                                if ($sectionLabel !== '') {
                                    echo ' / ' . htmlspecialchars($sectionLabel, ENT_QUOTES, 'UTF-8');
                                }
                            } else {
                                echo htmlspecialchars((string) ($_SESSION['user_company'] ?? ''), ENT_QUOTES, 'UTF-8');
                            }
                            ?>
                        </span>
                    </div>

                    <div class="dashboard-actions">
                        <a href="./myaccount" class="btn btn-light btn-sm">
                            <i class="ri-user-settings-line me-1"></i>
                            <?php echo _lang['my_account']; ?>
                        </a>

                        <a href="./chat_center" class="btn btn-warning btn-sm">
                            <i class="ri-chat-3-line me-1"></i>
                            <?php echo _lang['chat_center']; ?>
                        </a>
                    </div>
                </div>
            </div>

            <?php
            $dashboardPortalProfileValid = !empty($customerPortalFirstContext['profile_valid']);
            $dashboardPortalEnabled = !empty($customerPortalFirstContext['company_workflow_enabled']);
            $dashboardPortalRole = strtoupper((string) ($customerPortalFirstContext['portal_role'] ?? ''));
            $dashboardPortalIsAdmin = $dashboardPortalRole === \iweb\model\CustomerPortalService::ROLE_ADMIN;
            $dashboardPortalIsManager = $dashboardPortalRole === \iweb\model\CustomerPortalService::ROLE_MANAGER;
            $dashboardPortalIsExpert = $dashboardPortalRole === \iweb\model\CustomerPortalService::ROLE_EXPERT;
            ?>

            <?php if ($dashboardPortalProfileValid): ?>
                <div class="portal-workspace mb-4 <?php echo $dashboardPortalEnabled ? 'is-enabled' : 'is-disabled'; ?>">
                    <div class="portal-workspace-head">
                        <div class="portal-workspace-title-wrap">
                            <span class="portal-workspace-icon"><i class="ri-building-4-line"></i></span>
                            <div>
                                <div class="portal-workspace-eyebrow"><?php echo _lang['cp_title']; ?></div>
                                <h4><?php echo _lang['cp_dashboard']; ?></h4>
                                <p><?php echo _lang['cp_subtitle_' . strtolower($dashboardPortalRole)] ?? _lang['cp_subtitle']; ?>
                                </p>
                            </div>
                        </div>
                        <div class="portal-workspace-meta">
                            <span class="portal-role-badge"><i
                                    class="ri-shield-user-line"></i><?php echo htmlspecialchars(_lang['cp_role_' . strtolower($dashboardPortalRole)] ?? $dashboardPortalRole, ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="portal-state-badge <?php echo $dashboardPortalEnabled ? 'active' : 'inactive'; ?>">
                                <?php echo $dashboardPortalEnabled ? _lang['active'] : _lang['inactive']; ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($dashboardPortalEnabled): ?>
                        <div class="portal-workspace-nav">
                            <a href="./customer_portal?view=dashboard" class="portal-nav-item"><i
                                    class="ri-dashboard-line"></i><span><?php echo _lang['cp_dashboard']; ?></span></a>
                            <a href="./customer_portal?view=drafts" class="portal-nav-item"><i
                                    class="ri-file-list-3-line"></i><span><?php echo _lang['cp_requests']; ?></span></a>
                            <?php if (!empty($customerPortalFirstContext['can_create_draft'])): ?>
                                <a href="./customer_portal?view=new" class="portal-nav-item"><i
                                        class="ri-add-circle-line"></i><span><?php echo _lang['cp_new_request']; ?></span></a>
                            <?php endif; ?>
                            <?php if ($dashboardPortalIsAdmin): ?>
                                <a href="./customer_portal?view=sections" class="portal-nav-item"><i
                                        class="ri-node-tree"></i><span><?php echo _lang['cp_sections']; ?></span></a>
                                <a href="./customer_portal?view=company_tickets" class="portal-nav-item"><i
                                        class="ri-ticket-2-line"></i><span><?php echo _lang['cp_company_tickets']; ?></span></a>
                            <?php endif; ?>
                            <?php if ($dashboardPortalIsAdmin || $dashboardPortalIsManager): ?>
                                <a href="./customer_portal?view=users" class="portal-nav-item"><i
                                        class="ri-team-line"></i><span><?php echo _lang['cp_users']; ?></span></a>
                                <a href="./customer_portal?view=reports" class="portal-nav-item"><i
                                        class="ri-bar-chart-box-line"></i><span><?php echo _lang['cp_reports']; ?></span></a>
                            <?php endif; ?>
                            <?php if ($dashboardPortalIsAdmin): ?>
                                <a href="./customer_portal?view=audit" class="portal-nav-item"><i
                                        class="ri-history-line"></i><span><?php echo _lang['cp_audit']; ?></span></a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="portal-workspace-disabled-note">
                            <i class="ri-information-line"></i>
                            <span><?php echo _lang['cp_error_disabled']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">

                <?php
                $monthlyTotal = (int) ($dashboardStats['total_30d'] ?? 0);
                $monthlyBlocker = (int) ($dashboardStats['blocker_30d'] ?? 0);
                $monthlyCritical = (int) ($dashboardStats['critical_30d'] ?? 0);
                $monthlyNormal = (int) ($dashboardStats['normal_30d'] ?? 0);
                $monthlyFinanceTotal = 0;
                foreach (($dashboardFinanceStatusSummary ?? []) as $financeStatusItem) {
                    $monthlyFinanceTotal += (int) ($financeStatusItem['total'] ?? 0);
                }
                $dashboardScopeLabel = $dashboardCompanyId !== null
                    ? _lang['dashboard_company_scope']
                    : _lang['dashboard_personal_scope'];
                $dashboardMonthlyTotalLabel = $dashboardCompanyId !== null
                    ? _lang['dashboard_company_tickets_30d']
                    : _lang['dashboard_my_tickets_30d'];
                ?>

                <div class="col-12">
                    <div class="dashboard-command-bar">
                        <div class="dashboard-command-title">
                            <span class="dashboard-live-dot"></span>
                            <div>
                                <strong><?php echo _lang['dashboard_monthly_overview']; ?></strong>
                                <small><?php echo $dashboardScopeLabel; ?></small>
                            </div>
                        </div>

                        <div class="dashboard-main-actions">
                            <?php if (!empty($dashboardNewTicketVisible)): ?>
                                <a href="<?php echo htmlspecialchars($dashboardNewTicketUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    class="dashboard-main-btn soft-danger-btn">
                                    <i class="ri-add-circle-line"></i>
                                    <?php echo _lang['new_ticket']; ?>
                                </a>
                            <?php endif; ?>

                            <a href="./tickets" class="dashboard-main-btn soft-neutral-btn">
                                <i class="ri-file-list-3-line"></i>
                                <?php echo _lang['tickets']; ?>
                            </a>

                            <?php if ($rbacClass->checkPermissionOperationByName('view_all_ticket_operation', 'u')): ?>
                                <a href="./priority_list" class="dashboard-main-btn soft-warning-btn">
                                    <i class="ri-file-list-2-line"></i>
                                    <?php echo _lang['priority_list']; ?>
                                </a>
                            <?php endif; ?>

                            <a href="./kanban_board" class="dashboard-main-btn soft-primary-btn">
                                <i class="ri-layout-grid-line"></i>
                                <?php echo _lang['kanban_board']; ?>
                            </a>

                            <a href="./marking_tags" class="dashboard-main-btn soft-success-btn">
                                <i class="ri-bookmark-3-line"></i>
                                <?php echo _lang['marking_tags']; ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="dashboard-kpi-grid">
                        <a href="./tickets" class="dashboard-kpi-card kpi-primary text-decoration-none">
                            <div class="kpi-icon"><i class="ri-file-list-3-line"></i></div>
                            <div class="kpi-body">
                                <span><?php echo $dashboardMonthlyTotalLabel; ?></span>
                                <strong><?php echo number_format($monthlyTotal); ?></strong>
                                <small><?php echo _lang['dashboard_last_30_days']; ?></small>
                            </div>
                        </a>

                        <a href="./tickets" class="dashboard-kpi-card kpi-blocker text-decoration-none">
                            <div class="kpi-icon"><i class="ri-stop-circle-line"></i></div>
                            <div class="kpi-body">
                                <span><?php echo _lang['dashboard_priority_blocker']; ?></span>
                                <strong><?php echo number_format($monthlyBlocker); ?></strong>
                                <small><?php echo _lang['dashboard_blocker_guide']; ?></small>
                            </div>
                        </a>

                        <a href="./tickets" class="dashboard-kpi-card kpi-critical text-decoration-none">
                            <div class="kpi-icon"><i class="ri-error-warning-line"></i></div>
                            <div class="kpi-body">
                                <span><?php echo _lang['dashboard_priority_critical']; ?></span>
                                <strong><?php echo number_format($monthlyCritical); ?></strong>
                                <small><?php echo _lang['dashboard_critical_guide']; ?></small>
                            </div>
                        </a>

                        <a href="./tickets" class="dashboard-kpi-card kpi-normal text-decoration-none">
                            <div class="kpi-icon"><i class="ri-checkbox-circle-line"></i></div>
                            <div class="kpi-body">
                                <span><?php echo _lang['dashboard_priority_normal']; ?></span>
                                <strong><?php echo number_format($monthlyNormal); ?></strong>
                                <small><?php echo _lang['dashboard_normal_guide']; ?></small>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-4 dashboard-insight-row">
                        <div class="col-xl-7 col-lg-7 col-md-12">
                            <div class="dashboard-panel dashboard-activity-panel h-100">
                                <div class="dashboard-panel-header border-0 mb-1 pb-1">
                                    <div>
                                        <h4>
                                            <i class="ri-calendar-2-line"></i>
                                            <?php echo _lang['dashboard_monthly_activity_summary']; ?>
                                        </h4>
                                        <span><?php echo _lang['dashboard_monthly_activity_note']; ?></span>
                                    </div>
                                    <span
                                        class="dashboard-period-badge"><?php echo _lang['dashboard_last_30_days']; ?></span>
                                </div>

                                <div class="monthly-activity-total">
                                    <div>
                                        <span><?php echo _lang['dashboard_total_sent_tickets']; ?></span>
                                        <strong><?php echo number_format($monthlyTotal); ?></strong>
                                    </div>
                                    <small><?php echo $dashboardScopeLabel; ?></small>
                                </div>

                                <div class="priority-guide-list">
                                    <div class="priority-guide-item blocker">
                                        <div class="priority-guide-main">
                                            <span class="priority-guide-dot"></span>
                                            <strong><?php echo _lang['dashboard_priority_blocker']; ?></strong>
                                            <b><?php echo number_format($monthlyBlocker); ?></b>
                                        </div>
                                        <small><?php echo _lang['dashboard_blocker_guide']; ?></small>
                                    </div>

                                    <div class="priority-guide-item critical">
                                        <div class="priority-guide-main">
                                            <span class="priority-guide-dot"></span>
                                            <strong><?php echo _lang['dashboard_priority_critical']; ?></strong>
                                            <b><?php echo number_format($monthlyCritical); ?></b>
                                        </div>
                                        <small><?php echo _lang['dashboard_critical_guide']; ?></small>
                                    </div>

                                    <div class="priority-guide-item normal">
                                        <div class="priority-guide-main">
                                            <span class="priority-guide-dot"></span>
                                            <strong><?php echo _lang['dashboard_priority_normal']; ?></strong>
                                            <b><?php echo number_format($monthlyNormal); ?></b>
                                        </div>
                                        <small><?php echo _lang['dashboard_normal_guide']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5 col-lg-5 col-md-12">
                            <div class="dashboard-panel h-100">
                                <div class="dashboard-panel-header border-0 mb-1 pb-1">
                                    <div>
                                        <h4>
                                            <i class="ri-pie-chart-2-line"></i>
                                            <?php echo _lang['dashboard_status_overview_30d']; ?>
                                        </h4>
                                        <span><?php echo _lang['dashboard_status_overview_note_30d']; ?></span>
                                    </div>
                                </div>

                                <div class="status-overview-list">
                                    <?php if (!empty($dashboardStatusSummary)): ?>
                                        <?php foreach ($dashboardStatusSummary as $statusItem):
                                            $statusKey = strtolower($statusItem['status_name'] ?? 'condition_pending');
                                            $statusCount = (int) ($statusItem['total'] ?? 0);
                                            $statusPercent = $monthlyTotal > 0 ? max(2, round(($statusCount / $monthlyTotal) * 100)) : 0;
                                            $meta = $dashboardConditionMeta[$statusKey] ?? null;
                                            $statusLabel = isset(_lang[$statusKey]) ? _lang[$statusKey] : $statusKey;
                                            $statusColor = $meta['condition_color'] ?? 'primary';
                                            ?>
                                            <div class="status-overview-item">
                                                <div class="status-overview-head">
                                                    <span
                                                        class="soft-status-badge soft-<?php echo htmlspecialchars($statusColor, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <strong><?php echo number_format($statusCount); ?></strong>
                                                </div>
                                                <div class="status-progress"><span
                                                        style="width: <?php echo (int) $statusPercent; ?>%"></span></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="dashboard-empty-state"><?php echo _lang['dashboard_no_data']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="dashboard-panel dashboard-finance-panel">
                        <div class="dashboard-panel-header border-0 mb-1 pb-1">
                            <div>
                                <h4>
                                    <i class="ri-bank-card-line"></i>
                                    <?php echo _lang['dashboard_finance_status_overview_30d']; ?>
                                </h4>
                                <span><?php echo _lang['dashboard_finance_status_overview_note_30d']; ?></span>
                            </div>
                            <span class="dashboard-period-badge"><?php echo _lang['dashboard_last_30_days']; ?></span>
                        </div>

                        <div class="finance-status-grid">
                            <?php if (!empty($dashboardFinanceStatusSummary)): ?>
                                <?php foreach ($dashboardFinanceStatusSummary as $financeItem):
                                    $financeKey = strtolower($financeItem['status_name'] ?? '');
                                    $financeCount = (int) ($financeItem['total'] ?? 0);
                                    $financePercent = $monthlyFinanceTotal > 0 ? max(2, round(($financeCount / $monthlyFinanceTotal) * 100)) : 0;
                                    $financeMeta = $dashboardConditionMeta[$financeKey] ?? null;
                                    $financeLabel = isset(_lang[$financeKey]) ? _lang[$financeKey] : $financeKey;
                                    $financeColor = $financeMeta['condition_color'] ?? 'warning';
                                    ?>
                                    <div class="status-overview-item finance-status-item">
                                        <div class="status-overview-head">
                                            <span
                                                class="soft-status-badge soft-<?php echo htmlspecialchars($financeColor, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($financeLabel, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                            <strong><?php echo number_format($financeCount); ?></strong>
                                        </div>
                                        <div class="status-progress finance-progress"><span
                                                style="width: <?php echo (int) $financePercent; ?>%"></span></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dashboard-empty-state"><?php echo _lang['dashboard_no_finance_data_30d']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="dashboard-panel dashboard-recent-panel">
                        <div class="dashboard-panel-header">
                            <div>
                                <h4><i class="ri-history-line"></i><?php echo _lang['dashboard_recent_tickets']; ?></h4>
                                <span><?php echo _lang['dashboard_recent_tickets_note']; ?></span>
                            </div>
                            <a href="./tickets" class="dashboard-view-all">
                                <?php echo _lang['dashboard_view_all']; ?>
                                <i class="ri-arrow-left-line"></i>
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table dashboard-table dashboard-recent-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th><?php echo _lang['ticket_number']; ?></th>
                                        <th><?php echo _lang['title']; ?></th>
                                        <th><?php echo _lang['type']; ?></th>
                                        <th><?php echo _lang['status']; ?></th>
                                        <th><?php echo _lang['dashboard_last_update']; ?></th>
                                        <th class="text-center"><?php echo _lang['action']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dashboardRecentTickets)): ?>
                                        <?php foreach ($dashboardRecentTickets as $recentTicket):
                                            $recentStatusKey = strtolower($recentTicket['ticket_status'] ?? 'condition_pending');
                                            $recentMeta = $dashboardConditionMeta[$recentStatusKey] ?? null;
                                            $recentStatusLabel = isset(_lang[$recentStatusKey]) ? _lang[$recentStatusKey] : $recentStatusKey;
                                            $recentStatusColor = $recentMeta['condition_color'] ?? 'primary';
                                            $recentDate = new DateConverter($recentTicket['last_updated_date'], $config->getNowLanguage('a'));
                                            ?>
                                            <tr>
                                                <td><span
                                                        class="ticket-number"><?php echo htmlspecialchars($recentTicket['ticket_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                </td>
                                                <td data-bs-toggle="tooltip"
                                                    title="<?php echo htmlspecialchars($recentTicket['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <?php echo $textToolsClass->truncateText($recentTicket['ticket_title'], 58); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($recentTicket['type_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="soft-status-badge soft-<?php echo htmlspecialchars($recentStatusColor, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($recentStatusLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                </td>
                                                <td class="recent-date"><?php echo $recentDate->convertToShamsi(); ?></td>
                                                <td class="text-center">
                                                    <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                        <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($recentTicket['ticket_id']); ?>"
                                                            class="action-icon primary"
                                                            title="<?php echo _lang['dashboard_view']; ?>">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <?php echo _lang['dashboard_no_data']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row g-4">

                        <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')): ?>
                            <div class="col-xl-6 col-lg-6 col-md-12">
                                <div class="dashboard-panel h-100">
                                    <div class="dashboard-panel-header">
                                        <div>
                                            <h4>
                                                <i class="ri-alarm-warning-line"></i>
                                                <?php echo _lang['description']; ?>
                                            </h4>
                                            <span><?php echo _lang['dashboard_description_note_14d']; ?></span>
                                        </div>
                                        <span
                                            class="dashboard-period-badge"><?php echo _lang['dashboard_last_14_days']; ?></span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table dashboard-table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _lang['ticket_number']; ?></th>
                                                    <th><?php echo _lang['title']; ?></th>
                                                    <th><?php echo _lang['status']; ?></th>
                                                    <th class="text-center"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($ticket_data = $commentTicketsNeed->fetch_assoc()):
                                                    $conditionsState = $structureModel->getLastConditionDescription($ticket_data['ticket_id'], 'tickets');
                                                    if ($conditionsState['view_to'])
                                                        continue;
                                                    $condition = $structureModel->getConditionsByName($ticket_data['ticket_status']);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <span class="ticket-number">
                                                                <?php echo $ticket_data['ticket_number']; ?>
                                                            </span>
                                                        </td>

                                                        <td data-bs-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                        </td>

                                                        <td>
                                                            <span
                                                                class="soft-status-badge soft-<?php echo $condition['condition_color']; ?>">
                                                                <?php echo _lang[$condition['condition_name']]; ?>
                                                            </span>
                                                        </td>




                                                        <td>
                                                            <div class="table-actions">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                    <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>&rgid=<?php echo $conditionsState['id']; ?>"
                                                                        class="action-icon primary">
                                                                        <i class="ri-eye-line"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                    <span class="action-icon secondary">
                                                                        <i class="ri-attachment-2"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>

                                                <?php while ($ticket_data = $commentTicketsReject->fetch_assoc()):
                                                    $conditionsState = $structureModel->getLastConditionDescription($ticket_data['ticket_id'], 'tickets');
                                                    if ($conditionsState['view_to'])
                                                        continue;
                                                    $condition = $structureModel->getConditionsByName($ticket_data['ticket_status']);
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <span class="ticket-number">
                                                                <?php echo $ticket_data['ticket_number']; ?>
                                                            </span>
                                                        </td>

                                                        <td data-bs-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                        </td>

                                                        <td>
                                                            <span
                                                                class="soft-status-badge soft-<?php echo $condition['condition_color']; ?>">
                                                                <?php echo _lang[$condition['condition_name']]; ?>
                                                            </span>
                                                        </td>



                                                        <td>
                                                            <div class="table-actions">
                                                                <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                    <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>&rgid=<?php echo $conditionsState['id']; ?>"
                                                                        class="action-icon primary">
                                                                        <i class="ri-eye-line"></i>
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                    <span class="action-icon secondary">
                                                                        <i class="ri-attachment-2"></i>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u') || $rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u')): ?>
                            <div class="col-xl-6 col-lg-6 col-md-12">
                                <div class="dashboard-panel h-100">
                                    <div class="dashboard-panel-header">
                                        <div>
                                            <h4>
                                                <i class="ri-alert-line"></i>
                                                <?php echo _lang['need_confirmation']; ?>
                                            </h4>
                                            <span><?php echo _lang['dashboard_confirmation_note_14d']; ?></span>
                                        </div>
                                        <span
                                            class="dashboard-period-badge"><?php echo _lang['dashboard_last_14_days']; ?></span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table dashboard-table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th><?php echo _lang['ticket_number']; ?></th>
                                                    <th><?php echo _lang['title']; ?></th>
                                                    <th><?php echo _lang['type']; ?></th>
                                                    <th><?php echo _lang['need_confirmation']; ?></th>
                                                    <th class="text-center"><?php echo _lang['action']; ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_invoice', 'u')): ?>
                                                    <?php foreach ($invoiceTickets as $ticket_data): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="ticket-number">
                                                                    <?php echo $ticket_data['ticket_number']; ?>
                                                                </span>
                                                            </td>

                                                            <td data-bs-toggle="tooltip"
                                                                title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                            </td>

                                                            <td><?php echo $ticket_data['type_name']; ?></td>

                                                            <td>
                                                                <span class="confirm-badge">
                                                                    <?php echo _lang['confirmation_invoce']; ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <div class="table-actions">
                                                                    <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                        <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>"
                                                                            class="action-icon primary">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                        <span class="action-icon secondary">
                                                                            <i class="ri-attachment-2"></i>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>

                                                <?php if ($rbacClass->checkPermissionOperationByName('condition_acepted_test', 'u')): ?>
                                                    <?php foreach ($testTickets as $ticket_data): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="ticket-number">
                                                                    <?php echo $ticket_data['ticket_number']; ?>
                                                                </span>
                                                            </td>

                                                            <td data-bs-toggle="tooltip"
                                                                title="<?php echo htmlspecialchars($ticket_data['ticket_title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <?php echo $textToolsClass->truncateText($ticket_data['ticket_title'], 70); ?>
                                                            </td>

                                                            <td><?php echo $ticket_data['type_name']; ?></td>

                                                            <td>
                                                                <span class="confirm-badge">
                                                                    <?php echo _lang['confirmation_test']; ?>
                                                                </span>
                                                            </td>

                                                            <td>
                                                                <div class="table-actions">
                                                                    <?php if ($rbacClass->checkPermissionOperationByName('view_operation', 'u')): ?>
                                                                        <a href="./tickets?ticket_id=<?php echo $encryptorClass->encrypt($ticket_data['ticket_id']); ?>"
                                                                            class="action-icon primary">
                                                                            <i class="ri-eye-line"></i>
                                                                        </a>
                                                                    <?php endif; ?>

                                                                    <?php if ($ticketModel->getCountFile($ticket_data['ticket_id'], 'tickets') > 0): ?>
                                                                        <span class="action-icon secondary">
                                                                            <i class="ri-attachment-2"></i>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <?php if ($permissionKabanBoard && $allKanbanTag->num_rows > 0) { ?>
                    <div class="col-12">
                        <div class="dashboard-panel">
                            <div class="dashboard-panel-header">
                                <div>
                                    <h4>
                                        <i class="ri-kanban-view"></i>
                                        <?php echo (_lang['kanban_board']); ?>
                                    </h4>
                                </div>
                            </div>

                            <div class="kanban-list" data-simplebar style="max-height: 320px;">
                                <?php while ($kanban_tag = $allKanbanTag->fetch_assoc()) { ?>
                                    <?php
                                    $AllKabanByTagId = $ticketModel->getAllKabanByTagId($kanban_tag['id']);
                                    while ($kaban_details = $AllKabanByTagId->fetch_assoc()) {
                                        $condition = $structureModel->getConditionsByName($kaban_details['status']);

                                        $showPriority = '';
                                        $priority = $kaban_details['priority'];

                                        if ($priority == 'low') {
                                            $showPriority = '<span class="priority-badge priority-low">' . _lang[$priority] . '</span>';
                                        } elseif ($priority == 'medium') {
                                            $showPriority = '<span class="priority-badge priority-medium">' . _lang[$priority] . '</span>';
                                        } elseif ($priority == 'high') {
                                            $showPriority = '<span class="priority-badge priority-high">' . _lang[$priority] . '</span>';
                                        }

                                        if ($kaban_details['part_name'] == 'tickets') {
                                            $urlSet = "./tickets?ticket_id=";
                                        } elseif ($kaban_details['part_name'] == 'projects') {
                                            $urlSet = "./projects?id=";
                                        } else {
                                            $urlSet = "";
                                        }

                                        $dateConverter = new DateConverter($kaban_details['last_updated_date'], $config->getNowLanguage('a'));
                                        $convertedDate = $dateConverter->convertToShamsi();
                                        ?>

                                        <div class="kanban-item">
                                            <div class="kanban-meta">
                                                <span class="soft-status-badge soft-<?php echo $condition['condition_color']; ?>">
                                                    <?php echo _lang[$condition['condition_name']]; ?>
                                                </span>

                                                <span class="type-badge">
                                                    <?php echo $kaban_details['type_group']; ?>
                                                </span>

                                                <?php echo $showPriority; ?>

                                                <span class="kanban-date">
                                                    <?php echo $convertedDate; ?>
                                                </span>
                                            </div>

                                            <a href="<?php echo $urlSet . $encryptorClass->encrypt($kaban_details['part_id']); ?>"
                                                class="kanban-title">
                                                <span class="kanban-tag">
                                                    <?php echo ($kanban_tag["board_tag"]); ?>
                                                </span>

                                                <span>
                                                    <?php echo '( ' . $kaban_details['ticket_number'] . ' ) ' . $textToolsClass->truncateText($kaban_details['ticket_title'], 250); ?>
                                                </span>
                                            </a>

                                            <div class="kanban-description">
                                                <?php echo $textToolsClass->truncateText($kaban_details['description'], length: 350); ?>
                                            </div>
                                        </div>

                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
<style>
/* ===================== PAGE BACKGROUND ===================== */
.pmpage {
    background: #f1f5f9;
    padding: 24px 0 60px;
    color: #1e293b;
    font-size: 14px;
    line-height: 1.5;
}
.pmpage * { box-sizing: border-box; }

/* ===================== BREADCRUMB ===================== */
.pmpage-breadcrumb { margin: 0 0 24px; }
.pmpage-breadcrumb ol {
    list-style: none; margin: 0; padding: 0;
    display: flex; align-items: center; gap: 8px;
    font-size: 14px;
}
.pmpage-breadcrumb li { display: flex; align-items: center; }
.pmpage-breadcrumb a { color: #64748b; text-decoration: none; }
.pmpage-breadcrumb a:hover { color: #2563eb; }
.pmpage-breadcrumb i { color: #cbd5e1; font-size: 12px; }
.pmpage-breadcrumb .is-current { color: #1e293b; font-weight: 600; }

/* ===================== ALERTS ===================== */
.pmpage-alert {
    margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; font-size: 14px;
}
.pmpage-alert.is-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.pmpage-alert.is-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.pmpage-alert ul { margin: 0; padding-left: 18px; }

/* ===================== GRID 3 COLUMNS ===================== */
.pmpage-grid {
    display: grid;
    grid-template-columns: 260px minmax(0,1fr) 300px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 1199px) {
    .pmpage-grid { grid-template-columns: 240px 1fr; }
    .pmpage-aside { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
}
@media (max-width: 767px) {
    .pmpage-grid { grid-template-columns: 1fr; }
    .pmpage-aside { grid-template-columns: 1fr; }
}

/* ===================== CARD BASE ===================== */
.pmpage-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
}
.pmpage-card--padded { padding: 24px; }

/* ===================== LEFT SIDEBAR ===================== */
.pmpage-sidebar {
    position: sticky; top: 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 1px 2px rgba(15,23,42,.04);
    overflow: hidden;
}
.pmpage-user {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
}
.pmpage-user-avatar {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, #e0f2fe 0%, #cbd5e1 100%);
    display: inline-flex; align-items: center; justify-content: center;
    color: #475569; font-weight: 700; font-size: 28px;
    margin-bottom: 12px;
    border: 3px solid #f8fafc;
    box-shadow: 0 2px 6px rgba(15,23,42,.06);
}
.pmpage-user-info strong {
    display: block; font-size: 15px; font-weight: 700; color: #0f172a;
    margin-bottom: 2px;
}
.pmpage-user-info > span {
    display: block; font-size: 13px; color: #64748b; margin-bottom: 8px;
}
.pmpage-user-info a {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 600;
}
.pmpage-user-info a:hover { text-decoration: underline; }

.pmpage-nav { list-style: none; padding: 8px 0; margin: 0; }
.pmpage-nav li a {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 20px; color: #475569; text-decoration: none;
    font-size: 14px; font-weight: 500;
    transition: background .15s, color .15s;
    border-left: 3px solid transparent;
}
.pmpage-nav li a:hover { background: #f8fafc; color: #2563eb; }
.pmpage-nav li.is-active a {
    background: #eff6ff;
    color: #2563eb;
    font-weight: 700;
    border-left-color: #2563eb;
}
.pmpage-nav li a i {
    width: 18px; text-align: center; font-size: 15px; color: inherit;
}
.pmpage-nav-sep {
    height: 1px; background: #f1f5f9; margin: 8px 16px;
}
.pmpage-nav-logout a { color: #dc2626 !important; }
.pmpage-nav-logout a:hover { background: #fef2f2 !important; }

/* ===================== MAIN ===================== */
.pmpage-main { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

/* HEAD */
.pmpage-head {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
}
.pmpage-head-text h1 {
    margin: 0 0 4px; font-size: 20px; font-weight: 800; color: #0f172a;
}
.pmpage-head-text p { margin: 0; color: #64748b; font-size: 14px; }

/* ===================== STATS ===================== */
.pmpage-stats {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
}
@media (max-width: 991px) { .pmpage-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .pmpage-stats { grid-template-columns: 1fr; } }

.pmpage-stat {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 1px 2px rgba(15,23,42,.04);
}
.pmpage-stat-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.pmpage-stat-icon--blue  { background: #dbeafe; color: #2563eb; }
.pmpage-stat-icon--orange{ background: #fef3c7; color: #f59e0b; }
.pmpage-stat-icon--cyan  { background: #cffafe; color: #0891b2; }
.pmpage-stat-icon--navy  { background: #e0e7ff; color: #4338ca; }
.pmpage-stat-body { display: flex; flex-direction: column; min-width: 0; }
.pmpage-stat-body strong { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1; }
.pmpage-stat-label { font-size: 13px; font-weight: 600; color: #1e293b; margin-top: 4px; }
.pmpage-stat-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* ===================== METHODS LIST ===================== */
.pmpage-methods-title {
    margin: 0 0 16px; font-size: 15px; font-weight: 700; color: #0f172a;
}
.pmpage-method-list { display: flex; flex-direction: column; }

.pmpage-method {
    display: grid;
    grid-template-columns: 88px minmax(0, 1.4fr) auto auto;
    align-items: center;
    gap: 16px;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}
.pmpage-method:last-child { border-bottom: 0; padding-bottom: 0; }
.pmpage-method:first-child { padding-top: 0; }

.pmpage-method-logo {
    width: 70px; height: 44px; border-radius: 8px;
    background: #f8fafc;
    display: flex; align-items: center; justify-content: center;
    padding: 6px; flex-shrink: 0;
    border: 1px solid #f1f5f9;
}
.pmpage-method-logo svg { max-width: 100%; max-height: 100%; }
.pmpage-method-logo--momo { background: #fff; }
.pmpage-method-logo--cod {
    background: #fef3c7; color: #d97706; font-size: 22px; border-color: #fde68a;
}

.pmpage-method-info { min-width: 0; }
.pmpage-method-title-row {
    display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap;
}
.pmpage-method-title-row strong {
    font-size: 15px; font-weight: 700; color: #0f172a;
}
.pmpage-method-mask {
    font-size: 14px; color: #1e293b; font-weight: 600; letter-spacing: .5px;
}
.pmpage-method-meta {
    display: flex; gap: 16px; flex-wrap: wrap; margin-top: 2px;
}
.pmpage-method-meta span { font-size: 12px; color: #64748b; }

.pmpage-method-status { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.pmpage-method-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.pmpage-method-actions--note span {
    font-size: 12px; color: #64748b; font-style: italic;
}

@media (max-width: 991px) {
    .pmpage-method {
        grid-template-columns: 70px 1fr;
        grid-template-areas:
            "logo info"
            "status status"
            "actions actions";
    }
    .pmpage-method-logo { grid-area: logo; }
    .pmpage-method-info { grid-area: info; }
    .pmpage-method-status { grid-area: status; }
    .pmpage-method-actions { grid-area: actions; }
}

/* ===================== BADGES ===================== */
.pmpage-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 999px;
    font-size: 12px; font-weight: 600; line-height: 1;
}
.pmpage-badge--default { background: #10b981; color: #fff; padding: 6px 12px; }
.pmpage-badge--ok { color: #047857; background: transparent; padding: 4px 0; }
.pmpage-badge--ok i { color: #10b981; }
.pmpage-badge--info { color: #2563eb; background: transparent; padding: 4px 0; }
.pmpage-badge--info i { color: #3b82f6; }

/* ===================== BUTTONS ===================== */
.pmpage-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
    text-decoration: none; border: 1px solid transparent;
    cursor: pointer; transition: all .15s; line-height: 1;
    background: transparent;
}
.pmpage-btn:focus { outline: none; }
.pmpage-btn--lg { padding: 12px 20px; font-size: 14px; border-radius: 12px; }
.pmpage-btn--block { width: 100%; }

.pmpage-btn--primary {
    background: #1e3a8a; color: #fff;
    box-shadow: 0 2px 6px rgba(30, 58, 138, .25);
}
.pmpage-btn--primary:hover { background: #1e40af; color: #fff; }

.pmpage-btn--ghost {
    background: #fff; color: #1e293b; border-color: #e5e7eb;
}
.pmpage-btn--ghost:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }

.pmpage-btn--danger-ghost {
    background: #fff; color: #ef4444; border-color: #fecaca;
}
.pmpage-btn--danger-ghost:hover { background: #fef2f2; border-color: #fca5a5; color: #dc2626; }

.pmpage-btn--outline {
    background: #fff; color: #2563eb; border: 1px solid #bfdbfe;
}
.pmpage-btn--outline:hover { background: #eff6ff; color: #1d4ed8; }

/* ===================== RIGHT ASIDE ===================== */
.pmpage-aside { display: flex; flex-direction: column; gap: 12px; }

.pmpage-aside-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 1px 2px rgba(15,23,42,.04);
}
.pmpage-aside-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.pmpage-aside-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 16px;
}
.pmpage-aside-icon--blue { background: #dbeafe; color: #2563eb; }
.pmpage-aside-card h4 {
    margin: 0; font-size: 14px; font-weight: 700; color: #0f172a;
}
.pmpage-aside-card p {
    margin: 0 0 10px; font-size: 12px; color: #64748b; line-height: 1.5;
}
.pmpage-aside-status {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600;
}
.pmpage-aside-status.is-ok { color: #047857; }
.pmpage-aside-status.is-ok i { color: #10b981; }

.pmpage-aside-row {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
}

.pmpage-aside-tips {
    list-style: none; padding: 0; margin: 0 0 8px;
    display: flex; flex-direction: column; gap: 6px;
}
.pmpage-aside-tips li {
    display: flex; align-items: flex-start; gap: 8px;
    font-size: 12px; color: #475569;
}
.pmpage-aside-tips li i { color: #10b981; font-size: 12px; margin-top: 2px; flex-shrink: 0; }

.pmpage-aside-link {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 600;
}
.pmpage-aside-link:hover { text-decoration: underline; }

.pmpage-aside-contact {
    list-style: none; padding: 0; margin: 0 0 12px;
    display: flex; flex-direction: column; gap: 6px;
}
.pmpage-aside-contact li {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: #64748b;
}
.pmpage-aside-contact li i { color: #2563eb; width: 14px; }
.pmpage-aside-contact li strong { color: #0f172a; font-weight: 600; }

/* Bank discount card */
.pmpage-aside-card--bank .pmpage-aside-discount {
    margin-bottom: 4px;
}
.pmpage-aside-discount strong {
    font-size: 18px; font-weight: 800; color: #2563eb; display: block;
}
.pmpage-aside-discount span { font-size: 12px; color: #64748b; }

/* ===================== SWITCH ===================== */
.pmpage-switch {
    position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0;
}
.pmpage-switch input { opacity: 0; width: 0; height: 0; }
.pmpage-switch span {
    position: absolute; cursor: pointer; inset: 0;
    background: #cbd5e1; border-radius: 999px; transition: .2s;
}
.pmpage-switch span::before {
    content: ""; position: absolute; height: 16px; width: 16px;
    left: 3px; top: 3px; background: #fff; border-radius: 50%;
    transition: .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.pmpage-switch input:checked + span { background: #10b981; }
.pmpage-switch input:checked + span::before { transform: translateX(18px); }

/* ===================== PROMO OFFERS ===================== */
.pmpage-promo {}
.pmpage-promo-head {
    padding: 16px 24px 12px; font-size: 15px; font-weight: 700; color: #0f172a;
}
.pmpage-promo-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
    padding: 0 24px 20px;
}
@media (max-width: 991px) { .pmpage-promo-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .pmpage-promo-grid { grid-template-columns: 1fr; } }

.pmpage-promo-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px;
    border: 1px solid #f1f5f9; border-radius: 12px;
    background: #fafbfc;
}
.pmpage-promo-logo {
    width: 56px; height: 40px; border-radius: 8px; flex-shrink: 0;
    background: #fff; border: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: center; padding: 6px;
}
.pmpage-promo-logo svg { max-width: 100%; max-height: 100%; }
.pmpage-promo-logo--momo { background: #A50064; border-color: #A50064; }

.pmpage-promo-body { display: flex; flex-direction: column; min-width: 0; }
.pmpage-promo-body strong { font-size: 14px; font-weight: 800; color: #ef4444; }
.pmpage-promo-sub { font-size: 11px; color: #475569; }
.pmpage-promo-desc { font-size: 12px; color: #64748b; margin-top: 2px; }
.pmpage-promo-link {
    margin-top: 4px;
    font-size: 12px; color: #2563eb; text-decoration: none; font-weight: 600;
    display: inline-flex; align-items: center; gap: 3px;
}
.pmpage-promo-link:hover { text-decoration: underline; }

/* ===================== HELP BAR ===================== */
.pmpage-help {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 20px;
    margin-top: 24px;
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;
    box-shadow: 0 1px 2px rgba(15,23,42,.04);
}
@media (max-width: 991px) { .pmpage-help { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .pmpage-help { grid-template-columns: 1fr; } }

.pmpage-help-item { display: flex; align-items: center; gap: 12px; }
.pmpage-help-icon {
    width: 40px; height: 40px; border-radius: 50%;
    background: #dbeafe; color: #2563eb;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.pmpage-help-item strong { display: block; font-size: 13px; color: #0f172a; font-weight: 700; }
.pmpage-help-item span { display: block; font-size: 12px; color: #64748b; margin-top: 2px; }

/* ===================== FORM (other sections) ===================== */
.pmpage-section-head { margin-bottom: 16px; }
.pmpage-section-head h2 { margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; }
.pmpage-section-sub { color: #64748b; margin: 4px 0 0; font-size: 13px; }

.pmpage-form { display: flex; flex-direction: column; gap: 12px; }
.pmpage-form label {
    display: flex; flex-direction: column; gap: 6px;
    font-size: 13px; font-weight: 600; color: #1e293b;
}
.pmpage-form input, .pmpage-form textarea {
    padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px;
    font-size: 14px; color: #0f172a; background: #fff;
    font-family: inherit;
}
.pmpage-form input:focus, .pmpage-form textarea:focus {
    outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
}
.pmpage-form button { align-self: flex-start; margin-top: 8px; }

.pmpage-detail-list { display: flex; flex-direction: column; }
.pmpage-detail-row {
    display: flex; align-items: center; padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.pmpage-detail-row:last-child { border-bottom: 0; }
.pmpage-detail-row span { width: 180px; color: #64748b; font-weight: 600; font-size: 13px; }
.pmpage-detail-row strong { color: #0f172a; font-weight: 700; font-size: 14px; }
</style>

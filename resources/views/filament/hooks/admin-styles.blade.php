{{-- Polished admin panel chrome for Si COMA --}}
<style>
    /* Opaque sticky topbar so content never shows through */
    .fi-topbar,
    .fi-topbar-ctn {
        position: sticky;
        top: 0;
        background-color: #ffffff !important;
        border-bottom: 1px solid rgb(226 232 240 / 0.95);
        box-shadow: 0 1px 0 rgb(15 23 42 / 0.04);
        z-index: 40;
    }

    .dark .fi-topbar,
    .dark .fi-topbar-ctn {
        background-color: rgb(3 7 18) !important;
        border-bottom-color: rgb(255 255 255 / 0.08);
        box-shadow: none;
    }

    /* Comfortable main content spacing */
    .fi-main-ctn,
    .fi-main {
        padding-bottom: 1.75rem;
    }

    .fi-main {
        padding-top: 0.35rem;
    }

    .fi-page-header {
        margin-bottom: 0.5rem;
        padding-top: 0.25rem;
    }

    .fi-page-content {
        gap: 1.25rem;
    }

    .fi-page-content,
    .fi-wi-widget {
        min-width: 0;
    }

    /* Dashboard widget grid: avoid cramped half-width tables */
    .fi-dashboard-page .fi-wi-table {
        overflow: hidden;
    }

    .fi-dashboard-page .fi-ta-header-cell,
    .fi-dashboard-page .fi-ta-cell {
        vertical-align: top;
    }

    .fi-dashboard-page .fi-ta-text-item {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    /* Sidebar: quieter scrollbar + brand breathing room */
    .fi-sidebar-nav {
        scrollbar-width: thin;
        scrollbar-color: rgb(148 163 184 / 0.55) transparent;
    }

    .fi-sidebar-nav::-webkit-scrollbar {
        width: 6px;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgb(148 163 184 / 0.55);
        border-radius: 999px;
    }

    .fi-sidebar-header {
        padding-block: 0.85rem;
    }

    .fi-logo {
        max-width: 9.5rem;
        object-fit: contain;
    }

    /* Footer credit: light strip (match previous admin look) */
    .fi-footer {
        display: block;
        margin-top: 1.25rem;
        padding: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
    }

    .fi-footer > * {
        width: 100%;
    }

    .sicoma-admin-credit {
        margin: 0;
        border-top: 1px solid rgb(226 232 240);
        background: #fff;
        padding: 1rem 1.25rem 1.35rem;
        text-align: center;
        font-size: 0.75rem;
        line-height: 1.5;
        color: rgb(100 116 139);
    }

    .dark .sicoma-admin-credit {
        border-top-color: rgb(255 255 255 / 0.08);
        background: rgb(3 7 18);
        color: rgb(148 163 184);
    }

    .sicoma-admin-credit a {
        font-weight: 600;
        color: rgb(51 65 85);
        text-decoration: none;
    }

    .sicoma-admin-credit a:hover {
        color: #0a7a3e;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .dark .sicoma-admin-credit a {
        color: rgb(226 232 240);
    }

    .sicoma-admin-credit__dev {
        margin-top: 0.35rem;
    }

    /* Hide accidental icons/buttons leaking into footer strip */
    .fi-footer .sicoma-admin-credit svg,
    .fi-footer .sicoma-admin-credit button,
    .fi-footer > button,
    .fi-footer > .fi-icon-btn {
        display: none !important;
    }

    /* Stats cards breathe a bit */
    .fi-wi-stats-overview-stat {
        min-height: 5.5rem;
    }
</style>

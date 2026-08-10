<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .dash {
        --dash-bg: #f8fafc;
        --dash-surface: #ffffff;
        --dash-border: #e2e8f0;
        --dash-text: #0f172a;
        --dash-muted: #64748b;
        --dash-accent: #6366f1;
        --dash-accent-soft: #eef2ff;
        --dash-success: #10b981;
        --dash-warning: #f59e0b;
        --dash-radius: 16px;
        --dash-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px rgba(15, 23, 42, .06);
        font-family: 'Inter', system-ui, sans-serif;
        color: var(--dash-text);
        margin: -1.25rem -1rem 0;
        padding: 1.5rem;
        background: var(--dash-bg);
        min-height: calc(100vh - 120px);
    }

    .dash * { box-sizing: border-box; }

    .dash-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .dash-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.03em;
    }

    .dash-header p {
        margin: 0.35rem 0 0;
        color: var(--dash-muted);
        font-size: 0.9375rem;
    }

    .dash-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.875rem;
        border-radius: 999px;
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--dash-muted);
        box-shadow: var(--dash-shadow);
    }

    .dash-alert {
        padding: 0.875rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .dash-alert--success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .dash-alert--error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .dash-grid { display: grid; gap: 1rem; }
    .dash-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

    .dash-card {
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        border-radius: var(--dash-radius);
        box-shadow: var(--dash-shadow);
        overflow: hidden;
    }

    .dash-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.125rem 1.25rem;
        border-bottom: 1px solid var(--dash-border);
    }

    .dash-card__title {
        margin: 0;
        font-size: 0.9375rem;
        font-weight: 600;
    }

    .dash-card__body { padding: 1.25rem; }
    .dash-card__body--flush { padding: 0; }

    .dash-link {
        color: var(--dash-accent);
        font-weight: 600;
        font-size: 0.8125rem;
        text-decoration: none;
    }

    .dash-link:hover { text-decoration: underline; }

    .dash-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .dash-tab {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--dash-muted);
        background: var(--dash-surface);
        border: 1px solid var(--dash-border);
        transition: background .15s, border-color .15s, color .15s;
    }

    .dash-tab:hover {
        border-color: #c7d2fe;
        color: var(--dash-accent);
    }

    .dash-tab.is-active {
        background: var(--dash-accent-soft);
        border-color: #c7d2fe;
        color: #4338ca;
    }

    .dash-search {
        display: flex;
        flex-wrap: wrap;
        gap: 0.625rem;
        align-items: center;
    }

    .dash-input {
        flex: 1;
        min-width: 220px;
        padding: 0.625rem 0.875rem;
        border-radius: 10px;
        border: 1px solid var(--dash-border);
        font: inherit;
        font-size: 0.875rem;
        background: var(--dash-surface);
        color: var(--dash-text);
    }

    .dash-input:focus {
        outline: none;
        border-color: #a5b4fc;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    }

    .dash-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border-radius: 10px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .dash-btn--primary { background: var(--dash-accent); color: #fff; }
    .dash-btn--ghost {
        background: var(--dash-surface);
        color: var(--dash-text);
        border-color: var(--dash-border);
    }

    .dash-table-wrap { overflow-x: auto; }

    .dash-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .dash-table th {
        padding: 0.75rem 1.25rem;
        text-align: left;
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--dash-muted);
        background: #f8fafc;
        border-bottom: 1px solid var(--dash-border);
        white-space: nowrap;
    }

    .dash-table td {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--dash-border);
        vertical-align: middle;
    }

    .dash-table tbody tr:hover { background: #f8fafc; }
    .dash-table tbody tr:last-child td { border-bottom: none; }

    .dash-user {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .dash-user__name {
        font-weight: 600;
        color: var(--dash-accent);
        text-decoration: none;
    }

    .dash-user__name:hover { text-decoration: underline; }

    .dash-user__meta {
        font-size: 0.75rem;
        color: var(--dash-muted);
    }

    .dash-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        font-size: 0.6875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .dash-badge--indigo { background: #eef2ff; color: #4338ca; }
    .dash-badge--emerald { background: #ecfdf5; color: #047857; }
    .dash-badge--gray { background: #f1f5f9; color: #475569; }
    .dash-badge--amber { background: #fffbeb; color: #b45309; }

    .dash-muted { color: var(--dash-muted); font-size: 0.8125rem; }

    .dash-empty {
        padding: 2.5rem 1.25rem;
        text-align: center;
        color: var(--dash-muted);
        font-size: 0.875rem;
    }

    .dash-pagination {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--dash-border);
        display: flex;
        justify-content: center;
    }

    .dash-pagination .pagination { margin: 0; gap: 0.25rem; }
    .dash-pagination .page-link {
        border-radius: 8px;
        font-size: 0.8125rem;
        color: var(--dash-text);
        border-color: var(--dash-border);
    }

    .dash-pagination .page-item.active .page-link {
        background: var(--dash-accent);
        border-color: var(--dash-accent);
    }

    .dash-section { margin-bottom: 1.25rem; }

    @media (max-width: 1200px) {
        .dash-grid--4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 640px) {
        .dash { padding: 1rem; margin: -0.75rem -0.5rem 0; }
        .dash-grid--4 { grid-template-columns: 1fr; }
        .dash-header h1 { font-size: 1.375rem; }
        .dash-table th, .dash-table td { padding: 0.75rem 1rem; }
    }
</style>

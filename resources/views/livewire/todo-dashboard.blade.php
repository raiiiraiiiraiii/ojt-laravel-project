<div
    class="kp-page"
    x-data="{
        draggingId: null,
        overStatus: null,
        dragStart(id, event) {
            this.draggingId = id;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', id);
        },
        clearDrag() {
            this.draggingId = null;
            this.overStatus = null;
        },
        dropOn(status, event) {
            const todoId = this.draggingId || event.dataTransfer.getData('text/plain');
            if (! todoId) {
                this.clearDrag();
                return;
            }
            this.clearDrag();
            $wire.updateTodoStatus(Number(todoId), status);
        }
    }"
>
    @php
        $allTodos = $todosByStatus->flatMap(fn ($items) => $items);
        $totalTasks = $allTodos->count();
        $todoCount = $todosByStatus['todo']->count();
        $progressCount = $todosByStatus['in_progress']->count();
        $reviewCount = $todosByStatus['review']->count();
        $doneCount = $todosByStatus['done']->count();
        $overdueCount = $allTodos->filter(function ($todo) {
            return $todo->deadline
                && \Illuminate\Support\Carbon::parse($todo->deadline)->isBefore(now()->startOfDay())
                && $todo->status !== 'done';
        })->count();
        $completionRate = $totalTasks > 0 ? round(($doneCount / $totalTasks) * 100) : 0;
    @endphp

    <style>
        .kp-page {
            min-height: 100vh;
            padding: 30px;
            color: #111827;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 12% 0%, rgba(255, 213, 0, .36), transparent 34%),
                radial-gradient(circle at 90% 12%, rgba(255, 231, 97, .48), transparent 28%),
                linear-gradient(135deg, #FFF9E2 0%, #FFFCED 54%, #FFF3B8 100%);
        }
        .kp-shell { max-width: 1560px; margin: 0 auto; }
        .kp-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(234,179,8,.28);
            border-radius: 34px;
            padding: 26px;
            background: linear-gradient(135deg, rgba(255,255,255,.94), rgba(255,249,226,.83));
            box-shadow: 0 24px 60px rgba(67, 56, 20, .14);
            margin-bottom: 18px;
        }
        .kp-hero::after {
            content: "";
            position: absolute;
            right: -110px;
            bottom: -160px;
            width: 390px;
            height: 390px;
            border-radius: 50%;
            background: rgba(255,213,0,.24);
        }
        .kp-hero-grid { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(0,1.5fr) minmax(340px,.55fr); gap: 22px; }
        .kp-brand-row { display: flex; align-items: center; gap: 18px; }
        .kp-logo {
            width: 72px; height: 72px; display: grid; place-items: center; flex-shrink: 0;
            border-radius: 24px; background: #111827; color: #FFD500; box-shadow: 0 18px 38px rgba(17,24,39,.18);
        }
        .kp-logo svg { width: 38px; height: 38px; }
        .kp-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            border: 1px solid rgba(234,179,8,.36); border-radius: 999px; padding: 7px 11px;
            background: rgba(255,255,255,.75); color: #8a6500; font-size: 12px; font-weight: 900; letter-spacing: .04em; text-transform: uppercase;
        }
        .kp-dot { width: 9px; height: 9px; border-radius: 50%; background: #FFD500; box-shadow: 0 0 0 5px rgba(255,213,0,.17); }
        .kp-title { margin: 10px 0 0; font-size: clamp(42px,5vw,74px); line-height: .92; font-weight: 1000; letter-spacing: -.075em; }
        .kp-title span { color: #c99600; }
        .kp-subtitle { max-width: 760px; margin: 18px 0 0; color: #475569; font-size: 16px; line-height: 1.65; font-weight: 650; }
        .kp-hero-chips { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px; }
        .kp-chip { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; border: 1px solid rgba(234,179,8,.33); background: rgba(255,255,255,.82); padding: 10px 13px; color: #475569; font-size: 13px; font-weight: 900; }
        .kp-pulse {
            border-radius: 28px; border: 1px solid rgba(17,24,39,.08); background: #111827; color: white;
            padding: 22px; box-shadow: 0 22px 46px rgba(17,24,39,.22);
        }
        .kp-pulse-label { margin: 0 0 14px; color: #fef3c7; font-size: 12px; font-weight: 1000; letter-spacing: .08em; text-transform: uppercase; }
        .kp-pulse-grid { display: grid; grid-template-columns: 112px 1fr; gap: 18px; align-items: center; }
        .kp-ring { position: relative; width: 112px; height: 112px; display: grid; place-items: center; border-radius: 50%; background: conic-gradient(#FFD500 calc({{ $completionRate }} * 1%), rgba(255,255,255,.14) 0); }
        .kp-ring::before { content: ""; position: absolute; width: 78px; height: 78px; border-radius: 50%; background: #111827; }
        .kp-ring strong { position: relative; z-index: 1; font-size: 28px; font-weight: 1000; letter-spacing: -.04em; }
        .kp-pulse-title { margin: 0; font-size: 24px; line-height: 1.1; font-weight: 1000; letter-spacing: -.04em; }
        .kp-pulse-copy { margin: 8px 0 0; color: #cbd5e1; font-size: 13px; line-height: 1.55; font-weight: 650; }
        .kp-stats { display: grid; grid-template-columns: repeat(5,minmax(140px,1fr)); gap: 12px; margin: 18px 0 22px; }
        .kp-stat { border: 1px solid rgba(234,179,8,.24); border-radius: 24px; padding: 16px; background: rgba(255,255,255,.84); box-shadow: 0 12px 32px rgba(67,56,20,.09); }
        .kp-stat small { color: #64748b; font-size: 12px; font-weight: 950; text-transform: uppercase; letter-spacing: .05em; }
        .kp-stat strong { display: block; margin-top: 7px; font-size: 34px; line-height: 1; font-weight: 1000; letter-spacing: -.06em; }
        .kp-stat span { display: block; margin-top: 8px; color: #8a6500; font-size: 12px; font-weight: 800; }
        .kp-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 15px; }
        .kp-section-title { margin: 0; font-size: 23px; font-weight: 1000; letter-spacing: -.04em; }
        .kp-hint { display: inline-flex; align-items: center; gap: 10px; border: 1px solid rgba(234,179,8,.3); border-radius: 999px; background: rgba(255,255,255,.84); padding: 11px 14px; color: #475569; font-size: 13px; font-weight: 900; box-shadow: 0 10px 30px rgba(67,56,20,.08); }
        .kp-board-scroll { overflow-x: auto; padding-bottom: 14px; }
        .kp-board { display: grid; grid-template-columns: repeat(4,minmax(300px,1fr)); gap: 18px; min-width: 1260px; align-items: start; }
        .kp-column { overflow: hidden; border: 1px solid rgba(17,24,39,.08); border-radius: 30px; background: rgba(255,255,255,.9); box-shadow: 0 12px 32px rgba(67,56,20,.09); transition: 180ms ease; }
        .kp-column.is-over { outline: 5px solid rgba(255,213,0,.32); transform: translateY(-3px); box-shadow: 0 24px 60px rgba(67,56,20,.14); }
        .kp-column-bar { height: 10px; }
        .kp-column-head { display: flex; justify-content: space-between; gap: 14px; padding: 18px; border-bottom: 1px solid rgba(226,232,240,.92); }
        .kp-column-title-wrap { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .kp-icon-box { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 17px; flex-shrink: 0; }
        .kp-column-title { margin: 0; font-size: 18px; font-weight: 1000; letter-spacing: -.04em; }
        .kp-column-desc { margin: 3px 0 0; color: #64748b; font-size: 12px; line-height: 1.35; font-weight: 650; }
        .kp-count { min-width: 42px; height: 42px; display: grid; place-items: center; border-radius: 16px; background: #f8fafc; color: #334155; font-weight: 1000; }
        .kp-column-body { display: flex; flex-direction: column; gap: 13px; padding: 14px; min-height: 315px; }
        .kp-add-summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; border: 1px dashed #d6a600; background: linear-gradient(135deg,#FFF9E2,#fff); color: #8a6500; border-radius: 21px; padding: 14px; font-size: 14px; font-weight: 1000; transition: 170ms ease; }
        .kp-add-summary:hover { background: #FFF394; transform: translateY(-1px); }
        .kp-add-summary::-webkit-details-marker { display: none; }
        .kp-form, .kp-edit-form { margin-top: 12px; border: 1px solid rgba(234,179,8,.35); background: #FFFCED; border-radius: 24px; padding: 15px; }
        .kp-edit-form { margin-top: 0; background: linear-gradient(135deg,#fff,#FFF9E2); }
        .kp-form-title { margin: 0 0 12px; font-size: 16px; font-weight: 1000; letter-spacing: -.03em; }
        .kp-field { margin-bottom: 11px; }
        .kp-field label { display: block; margin-bottom: 5px; font-size: 12px; font-weight: 950; color: #374151; }
        .kp-input, .kp-textarea, .kp-select { width: 100%; box-sizing: border-box; border: 1px solid #facc15; background: white; border-radius: 15px; padding: 11px 12px; font-size: 14px; color: #111827; outline: none; transition: 160ms ease; }
        .kp-input:focus, .kp-textarea:focus, .kp-select:focus { border-color: #FFD500; box-shadow: 0 0 0 4px rgba(255,213,0,.2); }
        .kp-textarea { resize: vertical; min-height: 84px; }
        .kp-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .kp-error { margin: 5px 0 0; font-size: 12px; color: #dc2626; font-weight: 800; }
        .kp-save-btn, .kp-secondary-btn, .kp-ghost-btn { border: 0; border-radius: 999px; padding: 10px 13px; font-size: 13px; font-weight: 1000; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 160ms ease; }
        .kp-save-btn { width: 100%; border-radius: 17px; background: #111827; color: white; padding: 12px 14px; }
        .kp-secondary-btn { background: #FFD500; color: #422006; }
        .kp-ghost-btn { background: white; color: #475569; border: 1px solid #e5e7eb; }
        .kp-save-btn:hover, .kp-secondary-btn:hover, .kp-ghost-btn:hover { transform: translateY(-1px); }
        .kp-card { background: white; border: 1px solid rgba(17,24,39,.08); border-radius: 24px; padding: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.07); cursor: grab; transition: 170ms ease; }
        .kp-card:hover { transform: translateY(-3px); border-color: rgba(234,179,8,.35); box-shadow: 0 18px 36px rgba(15,23,42,.11); }
        .kp-card.is-dragging { opacity: .45; transform: scale(.98); }
        .kp-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .kp-card-title { margin: 0; color: #111827; font-size: 15px; line-height: 1.35; font-weight: 1000; word-break: break-word; }
        .kp-card-desc { margin: 7px 0 0; color: #64748b; font-size: 13px; line-height: 1.5; font-weight: 600; word-break: break-word; }
        .kp-card-menu { display: flex; gap: 7px; flex-shrink: 0; }
        .kp-icon-btn, .kp-delete-btn { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 14px; cursor: pointer; transition: 160ms ease; }
        .kp-icon-btn { border: 1px solid #e5e7eb; background: #f8fafc; color: #475569; }
        .kp-icon-btn:hover { background: #FFF9E2; border-color: #FFE761; color: #8a6500; }
        .kp-delete-btn { border: 1px solid #fecaca; background: #fef2f2; color: #dc2626; }
        .kp-delete-btn:hover { background: #fee2e2; transform: translateY(-1px); }
        .kp-card-meta { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 13px; }
        .kp-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 7px 9px; font-size: 11px; font-weight: 1000; border: 1px solid #e5e7eb; background: #f8fafc; color: #475569; }
        .kp-pill-medium { background: #FFF9E2; color: #9a6b00; border-color: #FFE761; }
        .kp-pill-high, .kp-pill-overdue { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        .kp-empty { border: 1px dashed #d1d5db; border-radius: 24px; padding: 34px 16px; text-align: center; color: #94a3b8; font-size: 13px; font-weight: 800; background: linear-gradient(135deg,#f8fafc,#fff); }
        .kp-empty strong { display: block; color: #64748b; font-size: 15px; margin-bottom: 5px; }
        .kp-yellow { background: #FFD500; } .kp-blue { background: #38bdf8; } .kp-purple { background: #a78bfa; } .kp-green { background: #34d399; }
        .kp-soft-yellow { background: #FFF9E2; color: #9a6b00; } .kp-soft-blue { background: #e0f2fe; color: #0369a1; } .kp-soft-purple { background: #f3e8ff; color: #7e22ce; } .kp-soft-green { background: #dcfce7; color: #15803d; }
        .kp-svg { width: 18px; height: 18px; }
        .kp-loading { opacity: .7; }


        /* Premium minimalist hero refresh */
        .kp-page {
            background:
                radial-gradient(circle at 16% 0%, rgba(255, 213, 0, .26), transparent 28%),
                radial-gradient(circle at 86% 8%, rgba(255, 231, 97, .34), transparent 24%),
                linear-gradient(135deg, #FFF9E2 0%, #FFFCF1 48%, #FFF5C7 100%);
        }
        .kp-hero-minimal {
            border-radius: 30px;
            padding: 22px 26px;
            background:
                linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,252,237,.90)),
                radial-gradient(circle at 100% 0%, rgba(255,213,0,.16), transparent 40%);
            box-shadow: 0 18px 52px rgba(67, 56, 20, .12);
            margin-bottom: 14px;
        }
        .kp-hero-minimal::after { display: none; }
        .kp-hero-minimal .kp-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            align-items: center;
            gap: 20px;
        }
        .kp-hero-minimal .kp-brand-row { align-items: center; gap: 15px; }
        .kp-hero-minimal .kp-logo {
            width: 60px;
            height: 60px;
            border-radius: 22px;
            box-shadow: 0 14px 28px rgba(17,24,39,.16);
        }
        .kp-hero-minimal .kp-logo svg { width: 32px; height: 32px; }
        .kp-hero-minimal .kp-eyebrow {
            padding: 6px 10px;
            font-size: 11px;
            background: rgba(255,255,255,.78);
        }
        .kp-hero-minimal .kp-title {
            margin-top: 7px;
            font-size: clamp(42px, 4.4vw, 62px);
            line-height: .95;
            letter-spacing: -.065em;
        }
        .kp-hero-minimal .kp-subtitle {
            max-width: 560px;
            margin-top: 8px;
            color: #475569;
            font-size: 14px;
            line-height: 1.45;
            font-weight: 700;
        }
        .kp-hero-minimal .kp-pulse {
            border-radius: 24px;
            padding: 18px;
            box-shadow: 0 16px 34px rgba(17,24,39,.18);
        }
        .kp-hero-minimal .kp-pulse-label {
            margin-bottom: 11px;
            font-size: 11px;
            color: #fde68a;
        }
        .kp-hero-minimal .kp-pulse-grid {
            grid-template-columns: 78px 1fr;
            gap: 14px;
        }
        .kp-hero-minimal .kp-ring { width: 78px; height: 78px; }
        .kp-hero-minimal .kp-ring::before { width: 54px; height: 54px; }
        .kp-hero-minimal .kp-ring strong { font-size: 19px; }
        .kp-hero-minimal .kp-pulse-title { font-size: 19px; }
        .kp-hero-minimal .kp-pulse-copy { margin-top: 5px; font-size: 12px; line-height: 1.35; }
        .kp-stats {
            grid-template-columns: repeat(5, minmax(120px, 1fr));
            gap: 10px;
            margin: 14px 0 18px;
        }
        .kp-stat {
            border-radius: 20px;
            padding: 13px 14px;
            background: rgba(255,255,255,.78);
            box-shadow: 0 10px 26px rgba(67,56,20,.07);
        }
        .kp-stat small { font-size: 11px; letter-spacing: .055em; }
        .kp-stat strong { margin-top: 6px; font-size: 28px; }
        .kp-stat span { margin-top: 5px; font-size: 11px; color: #a16207; }
        .kp-toolbar { margin-bottom: 12px; }
        .kp-section-title { font-size: 22px; }
        .kp-hint { padding: 9px 13px; font-size: 12px; }
        @media (max-width: 1050px) {
            .kp-hero-minimal .kp-hero-grid { grid-template-columns: 1fr; }
            .kp-hero-minimal .kp-pulse { max-width: 420px; }
        }
        @media (max-width: 640px) {
            .kp-hero-minimal { padding: 18px; }
            .kp-hero-minimal .kp-brand-row { align-items: flex-start; }
            .kp-hero-minimal .kp-logo { width: 54px; height: 54px; }
            .kp-hero-minimal .kp-title { font-size: 42px; }
        }


        /* Compact premium workspace: fewer empty spaces, more board area */
        .kp-page {
            padding: 18px 22px;
            background:
                radial-gradient(circle at 12% 0%, rgba(255, 213, 0, .20), transparent 24%),
                radial-gradient(circle at 92% 6%, rgba(255, 231, 97, .24), transparent 22%),
                linear-gradient(135deg, #FFF9E2 0%, #FFFCF1 52%, #FFF6CF 100%);
        }
        .kp-shell {
            max-width: 1760px;
        }
        .kp-command-bar {
            display: grid;
            grid-template-columns: minmax(280px, .7fr) minmax(520px, 1.25fr) minmax(230px, .55fr);
            gap: 12px;
            align-items: stretch;
            border: 1px solid rgba(234,179,8,.26);
            border-radius: 28px;
            padding: 12px;
            margin-bottom: 12px;
            background: rgba(255, 255, 255, .86);
            box-shadow: 0 16px 44px rgba(67, 56, 20, .11);
            backdrop-filter: blur(10px);
        }
        .kp-compact-brand,
        .kp-metrics-strip,
        .kp-board-compact-status {
            border-radius: 22px;
            min-height: 76px;
        }
        .kp-compact-brand {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            background: linear-gradient(135deg, rgba(255,255,255,.88), rgba(255,249,226,.64));
        }
        .kp-compact-logo {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 18px;
            background: #111827;
            color: #FFD500;
            box-shadow: 0 12px 26px rgba(17,24,39,.16);
        }
        .kp-compact-logo svg { width: 29px; height: 29px; }
        .kp-compact-kicker {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #9a6b00;
            font-size: 10px;
            font-weight: 1000;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .kp-compact-title {
            margin: 2px 0 0;
            font-size: clamp(30px, 3vw, 44px);
            line-height: .95;
            letter-spacing: -.065em;
            font-weight: 1000;
            color: #111827;
        }
        .kp-metrics-strip {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
            padding: 8px;
            background: linear-gradient(135deg, rgba(255,249,226,.72), rgba(255,255,255,.74));
        }
        .kp-metric-pill {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 3px;
            border: 1px solid rgba(234,179,8,.20);
            border-radius: 17px;
            padding: 10px 11px;
            background: rgba(255,255,255,.76);
        }
        .kp-metric-pill small {
            color: #64748b;
            font-size: 10px;
            font-weight: 1000;
            letter-spacing: .07em;
            text-transform: uppercase;
        }
        .kp-metric-pill strong {
            color: #111827;
            font-size: 26px;
            line-height: .9;
            font-weight: 1000;
            letter-spacing: -.06em;
        }
        .kp-metric-pill span {
            color: #a16207;
            font-size: 10px;
            font-weight: 900;
        }
        .kp-board-compact-status {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            background: #111827;
            color: white;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
        }
        .kp-mini-ring {
            position: relative;
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 50%;
            background: conic-gradient(#FFD500 calc({{ $completionRate }} * 1%), rgba(255,255,255,.14) 0);
        }
        .kp-mini-ring::before {
            content: "";
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #111827;
        }
        .kp-mini-ring strong {
            position: relative;
            z-index: 1;
            font-size: 15px;
            font-weight: 1000;
            letter-spacing: -.04em;
        }
        .kp-status-text small {
            display: block;
            margin-bottom: 3px;
            color: #fde68a;
            font-size: 10px;
            font-weight: 1000;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .kp-status-text strong {
            display: block;
            font-size: 19px;
            line-height: 1.05;
            font-weight: 1000;
            letter-spacing: -.04em;
        }
        .kp-status-text span {
            display: block;
            margin-top: 4px;
            color: #cbd5e1;
            font-size: 11px;
            font-weight: 800;
        }
        .kp-toolbar {
            margin: 0 0 10px;
            padding: 0 2px;
        }
        .kp-section-title {
            font-size: 19px;
        }
        .kp-hint {
            padding: 8px 12px;
            font-size: 12px;
            box-shadow: none;
        }
        .kp-board-scroll {
            padding-bottom: 8px;
        }
        .kp-board {
            grid-template-columns: repeat(4, minmax(280px, 1fr));
            min-width: 1120px;
            gap: 14px;
        }
        .kp-column {
            border-radius: 24px;
        }
        .kp-column-head {
            padding: 14px;
        }
        .kp-column-body {
            padding: 12px;
            gap: 10px;
            min-height: 300px;
        }
        .kp-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 15px;
        }
        .kp-column-title { font-size: 17px; }
        .kp-column-desc { display: none; }
        .kp-count {
            min-width: 36px;
            height: 36px;
            border-radius: 14px;
        }
        .kp-card {
            border-radius: 20px;
            padding: 12px;
        }
        .kp-card:hover {
            transform: translateY(-2px);
        }
        .kp-card-desc {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .kp-card-meta {
            margin-top: 10px;
        }
        .kp-add-summary {
            padding: 11px 12px;
            border-radius: 18px;
        }
        .kp-empty {
            padding: 22px 14px;
            border-radius: 20px;
        }
        @media (max-width: 1180px) {
            .kp-command-bar {
                grid-template-columns: 1fr;
            }
            .kp-metrics-strip {
                grid-template-columns: repeat(5, minmax(100px, 1fr));
                overflow-x: auto;
            }
            .kp-board-compact-status {
                justify-content: space-between;
            }
        }
        @media (max-width: 640px) {
            .kp-page { padding: 14px; }
            .kp-command-bar { border-radius: 24px; padding: 10px; }
            .kp-metrics-strip { grid-template-columns: repeat(5, 104px); }
            .kp-compact-title { font-size: 34px; }
        }

        @media (max-width: 1050px) { .kp-hero-grid { grid-template-columns: 1fr; } .kp-stats { grid-template-columns: repeat(2,minmax(0,1fr)); } }
        @media (max-width: 640px) { .kp-page { padding: 18px; } .kp-hero { border-radius: 26px; padding: 20px; } .kp-brand-row { align-items: flex-start; } .kp-logo { width: 58px; height: 58px; border-radius: 20px; } .kp-toolbar { align-items: flex-start; flex-direction: column; } }

        .kp-subtasks {
            margin-top: 10px;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }

        .kp-subtask-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .kp-subtask-list {
            display: grid;
            gap: 6px;
            margin-bottom: 8px;
        }

        .kp-subtask-row {
            display: grid;
            grid-template-columns: 18px minmax(0, 1fr) 24px;
            align-items: center;
            gap: 8px;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 12px;
            padding: 7px 8px;
            font-size: 12px;
            font-weight: 750;
            color: #334155;
            overflow: hidden;
        }

        .kp-subtask-row span {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.35;
        }

        .kp-subtask-row input {
            accent-color: #FFD500;
        }

        .kp-subtask-done {
            color: #94a3b8;
            text-decoration: line-through;
        }

        .kp-subtask-delete {
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #dc2626;
            cursor: pointer;
            font-weight: 900;
        }

        .kp-subtask-delete:hover {
            background: #fee2e2;
        }

        .kp-subtask-form {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 6px;
            align-items: start;
        }

        .kp-subtask-input {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #FFE761;
            border-radius: 12px;
            padding: 8px 9px;
            font-size: 12px;
            font-weight: 750;
            outline: none;
        }

        .kp-subtask-add,
        .kp-subtask-cancel,
        .kp-subtask-start {
            border: 1px solid #FFE761;
            border-radius: 999px;
            background: #FFF9E2;
            color: #8a6500;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            white-space: nowrap;
        }

        .kp-subtask-start {
            width: 100%;
            margin-top: 2px;
            background: white;
            color: #475569;
            border-color: #e5e7eb;
        }


        .kp-attachments {
            margin-top: 10px;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }

        .kp-attachment-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .kp-attachment-list {
            display: grid;
            gap: 6px;
            margin-bottom: 8px;
        }

        .kp-attachment-row {
            display: grid;
            grid-template-columns: 20px minmax(0, 1fr) auto 24px;
            align-items: center;
            gap: 8px;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 12px;
            padding: 7px 8px;
            font-size: 12px;
            font-weight: 750;
            color: #334155;
            overflow: hidden;
        }

        .kp-attachment-name {
            min-width: 0;
            overflow-wrap: anywhere;
            word-break: break-word;
            line-height: 1.35;
            color: #334155;
            text-decoration: none;
        }

        .kp-attachment-name:hover {
            color: #8a6500;
            text-decoration: underline;
        }

        .kp-attachment-size {
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            white-space: nowrap;
        }

        .kp-attachment-delete {
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #dc2626;
            cursor: pointer;
            font-weight: 900;
        }

        .kp-attachment-delete:hover {
            background: #fee2e2;
        }

        .kp-attachment-form {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 6px;
            align-items: center;
        }

        .kp-attachment-input {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #FFE761;
            border-radius: 12px;
            padding: 8px 9px;
            font-size: 12px;
            font-weight: 750;
            outline: none;
            background: white;
        }

        .kp-attachment-add,
        .kp-attachment-cancel,
        .kp-attachment-start {
            border: 1px solid #FFE761;
            border-radius: 999px;
            background: #FFF9E2;
            color: #8a6500;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            white-space: nowrap;
        }

        .kp-attachment-start {
            width: 100%;
            margin-top: 2px;
            background: white;
            color: #475569;
            border-color: #e5e7eb;
        }


        .kp-head-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kp-mini-action {
            width: 30px;
            height: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            background: #ffffff;
            color: #475569;
            display: inline-grid;
            place-items: center;
            cursor: pointer;
            transition: .18s ease;
            padding: 0;
            flex-shrink: 0;
        }

        .kp-mini-action:hover {
            border-color: #FFD500;
            background: #FFF9E2;
            color: #8a6500;
        }

        .kp-mini-action svg {
            width: 14px;
            height: 14px;
        }

        .kp-subtask-form,
        .kp-attachment-form {
            margin-top: 8px;
        }

    </style>

    <div class="kp-shell">
        <section class="kp-command-bar" aria-label="KayaPa dashboard summary">
            <div class="kp-compact-brand">
                <div class="kp-compact-logo">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9 6.75V5.5A2.5 2.5 0 0 1 11.5 3h1A2.5 2.5 0 0 1 15 5.5v1.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M5.75 6.75h12.5A1.75 1.75 0 0 1 20 8.5v9.75A2.75 2.75 0 0 1 17.25 21H6.75A2.75 2.75 0 0 1 4 18.25V8.5a1.75 1.75 0 0 1 1.75-1.75Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <span class="kp-compact-kicker"><span class="kp-dot"></span>Workspace</span>
                    <h1 class="kp-compact-title">KayaPa</h1>
                </div>
            </div>

            <div class="kp-metrics-strip" aria-label="Task summary">
                <div class="kp-metric-pill"><small>Total</small><strong>{{ $totalTasks }}</strong><span>Tasks</span></div>
                <div class="kp-metric-pill"><small>To Do</small><strong>{{ $todoCount }}</strong><span>Queued</span></div>
                <div class="kp-metric-pill"><small>Active</small><strong>{{ $progressCount }}</strong><span>Moving</span></div>
                <div class="kp-metric-pill"><small>Review</small><strong>{{ $reviewCount }}</strong><span>Checking</span></div>
                <div class="kp-metric-pill"><small>Overdue</small><strong>{{ $overdueCount }}</strong><span>Attention</span></div>
            </div>

            <aside class="kp-board-compact-status">
                <div class="kp-mini-ring"><strong>{{ $completionRate }}%</strong></div>
                <div class="kp-status-text">
                    <small>Status</small>
                    <strong>{{ $doneCount }} / {{ $totalTasks }} done</strong>
                    <span>{{ $overdueCount }} overdue · {{ $progressCount }} active</span>
                </div>
            </aside>
        </section>
<div style="display:flex;justify-content:flex-end;align-items:center;margin:8px 0 14px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <label for="sortBy" style="font-size:12px;font-weight:900;color:#64748b;">Sort by</label>
                <select
                    id="sortBy"
                    wire:model.live="sortBy"
                    style="min-width:190px;border:1px solid #FFE761;background:white;border-radius:14px;padding:10px 12px;font-size:13px;font-weight:800;color:#111827;outline:none;"
                >
                    <option value="recent">Recently updated</option>
                    <option value="alpha">Alphabetical A-Z</option>
                    <option value="priority">Priority level</option>
                    <option value="deadline">Nearest deadline</option>
                </select>
            </div>
        </div>

        <div class="kp-board-scroll">
            <div class="kp-board" wire:loading.class="kp-loading" wire:target="addTask,editTask,updateTask,cancelEdit,updateTodoStatus,deleteTask">
                @foreach ($columns as $status => $column)
                    @php
                        $barClass = match ($status) { 'todo' => 'kp-yellow', 'in_progress' => 'kp-blue', 'review' => 'kp-purple', 'done' => 'kp-green', default => 'kp-yellow' };
                        $softClass = match ($status) { 'todo' => 'kp-soft-yellow', 'in_progress' => 'kp-soft-blue', 'review' => 'kp-soft-purple', 'done' => 'kp-soft-green', default => 'kp-soft-yellow' };
                    @endphp
                    <section
                        class="kp-column"
                        @dragenter.prevent="overStatus = '{{ $status }}'"
                        @dragover.prevent="overStatus = '{{ $status }}'; $event.dataTransfer.dropEffect = 'move';"
                        @dragleave="if ($event.currentTarget.contains($event.relatedTarget) === false) { overStatus = null; }"
                        @drop.prevent="dropOn('{{ $status }}', $event)"
                        :class="overStatus === '{{ $status }}' ? 'is-over' : ''"
                    >
                        <div class="kp-column-bar {{ $barClass }}"></div>
                        <div class="kp-column-head">
                            <div class="kp-column-title-wrap">
                                <div class="kp-icon-box {{ $softClass }}">
                                    @if ($status === 'todo')
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none"><path d="M8.75 8.75h6.5M8.75 12h6.5M8.75 15.25h3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M6.75 4.75h10.5A2.75 2.75 0 0 1 20 7.5v9A2.75 2.75 0 0 1 17.25 19.25H6.75A2.75 2.75 0 0 1 4 16.5v-9A2.75 2.75 0 0 1 6.75 4.75Z" stroke="currentColor" stroke-width="1.8"/></svg>
                                    @elseif ($status === 'in_progress')
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none"><path d="M12 5v3.5M12 15.5V19M5 12h3.5M15.5 12H19M7.05 7.05l2.47 2.47M14.48 14.48l2.47 2.47M16.95 7.05l-2.47 2.47M9.52 14.48l-2.47 2.47" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                    @elseif ($status === 'review')
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none"><path d="M9 11.75 11.25 14 15.5 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.75 4.75h10.5A2.75 2.75 0 0 1 20 7.5v9A2.75 2.75 0 0 1 17.25 19.25H6.75A2.75 2.75 0 0 1 4 16.5v-9A2.75 2.75 0 0 1 6.75 4.75Z" stroke="currentColor" stroke-width="1.8"/></svg>
                                    @else
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none"><path d="M20 7 9.5 17.5 4 12" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    @endif
                                </div>
                                <div><h3 class="kp-column-title">{{ $column['label'] }}</h3><p class="kp-column-desc">{{ $column['description'] }}</p></div>
                            </div>
                            <div class="kp-count">{{ $todosByStatus[$status]->count() }}</div>
                        </div>

                        <div class="kp-column-body">
                            @if ($status === 'todo')
                                <details>
                                    <summary class="kp-add-summary">Add a new task</summary>
                                    <form wire:submit="addTask" class="kp-form">
                                        <h3 class="kp-form-title">New task</h3>
                                        <div class="kp-field"><label for="title">Task title</label><input id="title" type="text" wire:model="title" placeholder="What needs to be done?" class="kp-input">@error('title')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                        <div class="kp-field"><label for="description">Description</label><textarea id="description" wire:model="description" placeholder="Add context, notes, or reminders" class="kp-textarea"></textarea>@error('description')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                        <div class="kp-form-grid">
                                            <div class="kp-field"><label for="priority">Priority</label><select id="priority" wire:model="priority" class="kp-select"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>@error('priority')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                            <div class="kp-field"><label for="deadline">Deadline</label><input id="deadline" type="date" wire:model="deadline" class="kp-input">@error('deadline')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                        </div>
                                        <button type="submit" class="kp-save-btn" wire:loading.attr="disabled" wire:target="addTask"><span wire:loading.remove wire:target="addTask">Save task</span><span wire:loading wire:target="addTask">Saving...</span></button>
                                    </form>
                                </details>
                            @endif

                            @forelse ($todosByStatus[$status] as $todo)
                                @php
                                    $priority = strtolower($todo->priority ?? 'medium');
                                    $deadlineDate = $todo->deadline ? \Illuminate\Support\Carbon::parse($todo->deadline) : null;
                                    $isOverdue = $deadlineDate && $deadlineDate->isBefore(now()->startOfDay()) && $todo->status !== 'done';
                                    $priorityClass = match ($priority) { 'low' => 'kp-pill-low', 'high' => 'kp-pill-high', default => 'kp-pill-medium' };
                                @endphp
                                <article wire:key="todo-card-{{ $todo->id }}" draggable="{{ $editingTodoId === $todo->id ? 'false' : 'true' }}" @dragstart="dragStart({{ $todo->id }}, $event)" @dragend="clearDrag()" :class="draggingId === {{ $todo->id }} ? 'is-dragging' : ''" class="kp-card">
                                    @if ($editingTodoId === $todo->id)
                                        <form wire:submit="updateTask" class="kp-edit-form" @click.stop>
                                            <h3 class="kp-form-title">Edit task</h3>
                                            <div class="kp-field"><label for="edit-title-{{ $todo->id }}">Task title</label><input id="edit-title-{{ $todo->id }}" type="text" wire:model="editTitle" class="kp-input">@error('editTitle')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                            <div class="kp-field"><label for="edit-description-{{ $todo->id }}">Description</label><textarea id="edit-description-{{ $todo->id }}" wire:model="editDescription" class="kp-textarea"></textarea>@error('editDescription')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                            <div class="kp-form-grid">
                                                <div class="kp-field"><label for="edit-priority-{{ $todo->id }}">Priority</label><select id="edit-priority-{{ $todo->id }}" wire:model="editPriority" class="kp-select"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>@error('editPriority')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                                <div class="kp-field"><label for="edit-deadline-{{ $todo->id }}">Deadline</label><input id="edit-deadline-{{ $todo->id }}" type="date" wire:model="editDeadline" class="kp-input">@error('editDeadline')<p class="kp-error">{{ $message }}</p>@enderror</div>
                                            </div>
                                            <div style="display:flex; gap:8px; margin-top:4px;"><button type="submit" class="kp-secondary-btn" wire:loading.attr="disabled" wire:target="updateTask">Save changes</button><button type="button" class="kp-ghost-btn" wire:click="cancelEdit">Cancel</button></div>
                                        </form>
                                    @else
                                        <div class="kp-card-top">
                                            <div><h3 class="kp-card-title">{{ $todo->title }}</h3><p class="kp-card-desc">{{ $todo->description ?: 'No description added yet.' }}</p></div>
                                            <div class="kp-card-menu">
                                                <button type="button" class="kp-icon-btn" title="Edit task" wire:click="editTask({{ $todo->id }})" @click.stop>
                                                    <svg class="kp-svg" viewBox="0 0 24 24" fill="none"><path d="M5 19h4.25L18.5 9.75a2.12 2.12 0 0 0-3-3L6.25 16H5v3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14.25 8 16.5 10.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                                </button>
                                                <button type="button" class="kp-delete-btn" title="Delete task" onclick="if (! confirm('Delete this task?')) { event.stopImmediatePropagation(); }" wire:click="deleteTask({{ $todo->id }})" @click.stop>
                                                    <svg class="kp-svg" viewBox="0 0 24 24" fill="none"><path d="M9.75 9.75v6.5M14.25 9.75v6.5M5.75 6.75h12.5M10 4h4a1.5 1.5 0 0 1 1.5 1.5v1.25h-7V5.5A1.5 1.5 0 0 1 10 4ZM7 6.75l.65 11.1A2.25 2.25 0 0 0 9.9 20h4.2a2.25 2.25 0 0 0 2.25-2.15L17 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="kp-card-meta">
                                            <span class="kp-pill {{ $priorityClass }}">{{ ucfirst($priority) }}</span>
                                            @if ($deadlineDate)
                                                <span class="kp-pill {{ $isOverdue ? 'kp-pill-overdue' : '' }}">{{ $deadlineDate->format('M d, Y') }}</span>
                                            @else
                                                <span class="kp-pill">No deadline</span>
                                            @endif
                                        </div>
                                    @endif

                                    @php
                                        $totalSubtasks = $todo->subtasks->count();
                                        $completedSubtasks = $todo->subtasks->where('is_completed', true)->count();
                                    @endphp

                                    <div class="kp-subtasks" @click.stop draggable="false">
                                        <div class="kp-subtask-head">
                                            <span>Checklist</span>

                                            <div class="kp-head-actions">
                                                <span>{{ $completedSubtasks }} / {{ $totalSubtasks }}</span>

                                                @if ($subtaskTodoId !== $todo->id)
                                                    <button
                                                        type="button"
                                                        class="kp-mini-action"
                                                        wire:click="startAddingSubtask({{ $todo->id }})"
                                                        title="Add subtask"
                                                        aria-label="Add subtask"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($totalSubtasks > 0)
                                            <div class="kp-subtask-list">
                                                @foreach ($todo->subtasks as $subtask)
                                                    <div class="kp-subtask-row" wire:key="subtask-{{ $subtask->id }}">
                                                        <input
                                                            type="checkbox"
                                                            @checked($subtask->is_completed)
                                                            wire:click="toggleSubtask({{ $subtask->id }})"
                                                        >

                                                        <span class="{{ $subtask->is_completed ? 'kp-subtask-done' : '' }}">
                                                            {{ $subtask->title }}
                                                        </span>

                                                        <button
                                                            type="button"
                                                            class="kp-subtask-delete"
                                                            title="Delete subtask"
                                                            wire:click="deleteSubtask({{ $subtask->id }})"
                                                        >
                                                            ×
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($subtaskTodoId === $todo->id)
                                            <form wire:submit.prevent="addSubtask({{ $todo->id }})" class="kp-subtask-form">
                                                <input
                                                    type="text"
                                                    class="kp-subtask-input"
                                                    wire:model="subtaskTitle"
                                                    placeholder="Add checklist item"
                                                >

                                                <button type="submit" class="kp-subtask-add">Add</button>

                                                <button type="button" class="kp-subtask-cancel" wire:click="cancelAddingSubtask">Cancel</button>
                                            </form>

                                            @error('subtaskTitle')
                                                <p class="kp-error">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>

                                

                                    @php
                                        $totalAttachments = $todo->attachments->count();
                                    @endphp

                                    <div class="kp-attachments" @click.stop draggable="false">
                                        <div class="kp-attachment-head">
                                            <span>Attachments</span>

                                            <div class="kp-head-actions">
                                                <span>{{ $totalAttachments }}</span>

                                                @if ($attachmentTodoId !== $todo->id)
                                                    <button
                                                        type="button"
                                                        class="kp-mini-action"
                                                        wire:click="startAddingAttachment({{ $todo->id }})"
                                                        title="Add attachment"
                                                        aria-label="Add attachment"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M21.44 11.05l-8.49 8.49a5 5 0 01-7.07-7.07l9.19-9.19a3.5 3.5 0 114.95 4.95l-9.19 9.19a2 2 0 01-2.83-2.83l8.48-8.49" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($totalAttachments > 0)
                                            <div class="kp-attachment-list">
                                                @foreach ($todo->attachments as $attachment)
                                                    <div class="kp-attachment-row" wire:key="attachment-{{ $attachment->id }}">
                                                        <span>📎</span>

                                                        <a
                                                            class="kp-attachment-name"
                                                            href="{{ '/storage/' . $attachment->path }}"
                                                            target="_blank"
                                                        >
                                                            {{ $attachment->original_name }}
                                                        </a>

                                                        <span class="kp-attachment-size">
                                                            {{ number_format(($attachment->size ?? 0) / 1024, 1) }} KB
                                                        </span>

                                                        <button
                                                            type="button"
                                                            class="kp-attachment-delete"
                                                            title="Delete attachment"
                                                            wire:click="deleteAttachment({{ $attachment->id }})"
                                                        >
                                                            ×
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($attachmentTodoId === $todo->id)
                                            <form wire:submit.prevent="uploadAttachment({{ $todo->id }})" class="kp-attachment-form">
                                                <input
                                                    type="file"
                                                    class="kp-attachment-input"
                                                    wire:model="attachmentFile"
                                                >

                                                <button type="submit" class="kp-attachment-add">Upload</button>

                                                <button type="button" class="kp-attachment-cancel" wire:click="cancelAddingAttachment">Cancel</button>
                                            </form>

                                            <div wire:loading wire:target="attachmentFile" style="margin-top:6px;font-size:11px;font-weight:800;color:#64748b;">
                                                Preparing file...
                                            </div>

                                            @error('attachmentFile')
                                                <p class="kp-error">{{ $message }}</p>
                                            @enderror
                                        @endif
                                    </div>

</article>
                            @empty
                                <div class="kp-empty"><strong>No tasks here yet</strong>Drop cards here or create a new task in To Do.</div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</div>

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
        @media (max-width: 1050px) { .kp-hero-grid { grid-template-columns: 1fr; } .kp-stats { grid-template-columns: repeat(2,minmax(0,1fr)); } }
        @media (max-width: 640px) { .kp-page { padding: 18px; } .kp-hero { border-radius: 26px; padding: 20px; } .kp-brand-row { align-items: flex-start; } .kp-logo { width: 58px; height: 58px; border-radius: 20px; } .kp-toolbar { align-items: flex-start; flex-direction: column; } }
    </style>

    <div class="kp-shell">
        <section class="kp-hero">
            <div class="kp-hero-grid">
                <div>
                    <div class="kp-brand-row">
                        <div class="kp-logo">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 6.75V5.5A2.5 2.5 0 0 1 11.5 3h1A2.5 2.5 0 0 1 15 5.5v1.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M5.75 6.75h12.5A1.75 1.75 0 0 1 20 8.5v9.75A2.75 2.75 0 0 1 17.25 21H6.75A2.75 2.75 0 0 1 4 18.25V8.5a1.75 1.75 0 0 1 1.75-1.75Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <span class="kp-eyebrow"><span class="kp-dot"></span>KayaPa Workspace</span>
                            <h1 class="kp-title">Make work feel <span>possible.</span></h1>
                        </div>
                    </div>
                    <p class="kp-subtitle">A focused Kanban board for organizing tasks, reviewing progress, and keeping priorities visible without making the workflow feel heavy.</p>
                    <div class="kp-hero-chips">
                        <span class="kp-chip">Add tasks in To Do</span>
                        <span class="kp-chip">Edit details anytime</span>
                        <span class="kp-chip">Drag cards across lanes</span>
                    </div>
                </div>
                <aside class="kp-pulse">
                    <p class="kp-pulse-label">Board pulse</p>
                    <div class="kp-pulse-grid">
                        <div class="kp-ring"><strong>{{ $completionRate }}%</strong></div>
                        <div>
                            <h2 class="kp-pulse-title">{{ $doneCount }} of {{ $totalTasks }} tasks done</h2>
                            <p class="kp-pulse-copy">Keep the board light: move what is active, review what needs feedback, and close what is already finished.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="kp-stats">
            <div class="kp-stat"><small>Total tasks</small><strong>{{ $totalTasks }}</strong><span>Overall workload</span></div>
            <div class="kp-stat"><small>To Do</small><strong>{{ $todoCount }}</strong><span>Waiting to start</span></div>
            <div class="kp-stat"><small>In Progress</small><strong>{{ $progressCount }}</strong><span>Currently moving</span></div>
            <div class="kp-stat"><small>For Review</small><strong>{{ $reviewCount }}</strong><span>Needs checking</span></div>
            <div class="kp-stat"><small>Overdue</small><strong>{{ $overdueCount }}</strong><span>Needs attention</span></div>
        </div>

        <div class="kp-toolbar">
            <h2 class="kp-section-title">Task board</h2>
            <div class="kp-hint">Drag to move status. Edit to update task details.</div>
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

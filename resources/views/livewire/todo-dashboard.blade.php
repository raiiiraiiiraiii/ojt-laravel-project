

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
    <style>
        .kp-page {
            min-height: 100vh;
            background: #FFF9E2;
            color: #1f2937;
            padding: 28px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .kp-shell {
            max-width: 1500px;
            margin: 0 auto;
        }

        .kp-brand {
    display: inline-flex;
    align-items: center;
    gap: 16px;
    background: white;
    border: 1px solid #FFE761;
    border-radius: 26px;
    padding: 18px 24px;
    font-size: 32px;
    font-weight: 950;
    letter-spacing: -0.04em;
    color: #111827;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
    margin-bottom: 26px;
    }

    .kp-brand svg {
        width: 42px;
        height: 42px;
        color: #FFD500;
    }

    .kp-brand-small {
        display: block;
        margin-top: 2px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0;
        color: #64748b;
    }

        .kp-hint {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            border: 1px solid #FFE761;
            border-radius: 18px;
            padding: 13px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .kp-board-scroll {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 12px;
        }

        .kp-board {
            display: grid;
            grid-template-columns: repeat(4, minmax(270px, 1fr));
            gap: 18px;
            min-width: 1180px;
            align-items: start;
        }

        .kp-column {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
            transition: 160ms ease;
        }

        .kp-column.is-over {
            outline: 4px solid rgba(255, 213, 0, 0.32);
            transform: translateY(-2px);
        }

        .kp-column-bar {
            height: 9px;
        }

        .kp-column-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .kp-column-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kp-icon-box {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            flex-shrink: 0;
        }

        .kp-column-title {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
            color: #111827;
        }

        .kp-column-desc {
            margin: 3px 0 0;
            font-size: 12px;
            line-height: 1.4;
            color: #64748b;
        }

        .kp-count {
            min-width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #f1f5f9;
            font-weight: 900;
            color: #475569;
        }

        .kp-column-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 14px;
            min-height: 260px;
        }

        .kp-add-summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            border: 1px dashed #FFD500;
            background: #FFF9E2;
            color: #9a6b00;
            border-radius: 18px;
            padding: 13px;
            font-size: 14px;
            font-weight: 900;
            transition: 160ms ease;
        }

        .kp-add-summary:hover {
            background: #FFF394;
        }

        .kp-add-summary::-webkit-details-marker {
            display: none;
        }

        .kp-form {
            margin-top: 12px;
            border: 1px solid #FFE761;
            background: #FFF9E2;
            border-radius: 20px;
            padding: 14px;
        }

        .kp-form-title {
            margin: 0 0 12px;
            font-size: 16px;
            font-weight: 900;
        }

        .kp-field {
            margin-bottom: 11px;
        }

        .kp-field label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            font-weight: 900;
            color: #374151;
        }

        .kp-input,
        .kp-textarea,
        .kp-select {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #facc15;
            background: white;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 14px;
            color: #111827;
            outline: none;
        }

        .kp-input:focus,
        .kp-textarea:focus,
        .kp-select:focus {
            border-color: #FFD500;
            box-shadow: 0 0 0 4px rgba(255, 213, 0, 0.2);
        }

        .kp-textarea {
            resize: vertical;
            min-height: 82px;
        }

        .kp-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .kp-error {
            margin: 5px 0 0;
            font-size: 12px;
            color: #dc2626;
            font-weight: 700;
        }

        .kp-save-btn {
            width: 100%;
            border: 0;
            border-radius: 15px;
            background: #111827;
            color: white;
            padding: 11px 14px;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .kp-save-btn:hover {
            background: #374151;
        }

        .kp-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 15px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            cursor: grab;
            transition: 160ms ease;
        }

        .kp-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.1);
        }

        .kp-card:active {
            cursor: grabbing;
        }

        .kp-card.is-dragging {
            opacity: 0.45;
            transform: scale(0.98);
        }

        .kp-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .kp-card-title {
            margin: 0;
            font-size: 15px;
            line-height: 1.35;
            font-weight: 900;
            color: #111827;
            word-break: break-word;
        }

        .kp-card-desc {
            margin: 6px 0 0;
            font-size: 13px;
            line-height: 1.45;
            color: #64748b;
            word-break: break-word;
        }

        .kp-delete-btn {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border: 1px solid #fecaca;
            border-radius: 13px;
            background: #fef2f2;
            color: #dc2626;
            cursor: pointer;
        }

        .kp-delete-btn:hover {
            background: #fee2e2;
        }

        .kp-card-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .kp-edit-btn {
            border: 1px solid #FFE761;
            border-radius: 13px;
            background: #FFF9E2;
            color: #9a6b00;
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
            padding: 8px 10px;
        }

        .kp-edit-btn:hover {
            background: #FFF394;
        }

        .kp-edit-form {
            display: grid;
            gap: 10px;
        }

        .kp-edit-actions {
            display: flex;
            gap: 8px;
        }

        .kp-edit-save-btn,
        .kp-edit-cancel-btn {
            border-radius: 999px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 900;
            padding: 9px 13px;
        }

        .kp-edit-save-btn {
            border: 0;
            background: #111827;
            color: white;
        }

        .kp-edit-save-btn:hover {
            background: #374151;
        }

        .kp-edit-cancel-btn {
            border: 1px solid #e5e7eb;
            background: white;
            color: #475569;
        }

        .kp-edit-cancel-btn:hover {
            background: #f8fafc;
        }


        .kp-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 13px;
        }

        .kp-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 9px;
            font-size: 11px;
            font-weight: 900;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #475569;
        }

        .kp-pill-low {
            background: #f8fafc;
            color: #475569;
            border-color: #e5e7eb;
        }

        .kp-pill-medium {
            background: #FFF9E2;
            color: #9a6b00;
            border-color: #FFE761;
        }

        .kp-pill-high {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .kp-pill-overdue {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .kp-empty {
            border: 1px dashed #d1d5db;
            border-radius: 20px;
            padding: 28px 14px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            background: #f8fafc;
        }

        .kp-yellow { background: #FFD500; }
        .kp-blue { background: #38bdf8; }
        .kp-purple { background: #a78bfa; }
        .kp-green { background: #34d399; }

        .kp-soft-yellow { background: #FFF9E2; color: #9a6b00; }
        .kp-soft-blue { background: #e0f2fe; color: #0369a1; }
        .kp-soft-purple { background: #f3e8ff; color: #7e22ce; }
        .kp-soft-green { background: #dcfce7; color: #15803d; }

        .kp-svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 900px) {
            .kp-page {
                padding: 18px;
            }

            .kp-title {
                font-size: 32px;
            }
        }
    </style>

    <div class="kp-brand">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M9 6.75V5.5A2.5 2.5 0 0 1 11.5 3h1A2.5 2.5 0 0 1 15 5.5v1.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M5.75 6.75h12.5A1.75 1.75 0 0 1 20 8.5v9.75A2.75 2.75 0 0 1 17.25 21H6.75A2.75 2.75 0 0 1 4 18.25V8.5a1.75 1.75 0 0 1 1.75-1.75Z" stroke="currentColor" stroke-width="1.8"/>
        <path d="M8 11h8M8 15h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>

    <span>
        KayaPa
        <span class="kp-brand-small">Organize tasks by dragging cards across columns.</span>
    </span>
</div>

        <div class="kp-hint">
            <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 3.75v2.5M17 3.75v2.5M4.75 8.75h14.5M7.5 5h9A2.75 2.75 0 0 1 19.25 7.75v8.75A2.75 2.75 0 0 1 16.5 19.25h-9A2.75 2.75 0 0 1 4.75 16.5V7.75A2.75 2.75 0 0 1 7.5 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Drag cards to update status.
        </div>

        <div class="kp-board-scroll">
            <div
                class="kp-board"
                wire:loading.class="kp-loading"
                wire:target="addTask,updateTodoStatus,deleteTask,editTask,updateTask,cancelEdit"
            >
                @foreach ($columns as $status => $column)
                    @php
                        $barClass = match ($status) {
                            'todo' => 'kp-yellow',
                            'in_progress' => 'kp-blue',
                            'review' => 'kp-purple',
                            'done' => 'kp-green',
                            default => 'kp-yellow',
                        };

                        $softClass = match ($status) {
                            'todo' => 'kp-soft-yellow',
                            'in_progress' => 'kp-soft-blue',
                            'review' => 'kp-soft-purple',
                            'done' => 'kp-soft-green',
                            default => 'kp-soft-yellow',
                        };
                    @endphp

                    <section
                        class="kp-column"
                        @dragenter.prevent="overStatus = '{{ $status }}'"
                        @dragover.prevent="
                            overStatus = '{{ $status }}';
                            $event.dataTransfer.dropEffect = 'move';
                        "
                        @dragleave="
                            if ($event.currentTarget.contains($event.relatedTarget) === false) {
                                overStatus = null;
                            }
                        "
                        @drop.prevent="dropOn('{{ $status }}', $event)"
                        :class="overStatus === '{{ $status }}' ? 'is-over' : ''"
                    >
                        <div class="kp-column-bar {{ $barClass }}"></div>

                        <div class="kp-column-head">
                            <div class="kp-column-title-wrap">
                                <div class="kp-icon-box {{ $softClass }}">
                                    @if ($status === 'todo')
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8.75 8.75h6.5M8.75 12h6.5M8.75 15.25h3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M6.75 4.75h10.5A2.75 2.75 0 0 1 20 7.5v9A2.75 2.75 0 0 1 17.25 19.25H6.75A2.75 2.75 0 0 1 4 16.5v-9A2.75 2.75 0 0 1 6.75 4.75Z" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                    @elseif ($status === 'in_progress')
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 5v3.5M12 15.5V19M5 12h3.5M15.5 12H19M7.05 7.05l2.47 2.47M14.48 14.48l2.47 2.47M16.95 7.05l-2.47 2.47M9.52 14.48l-2.47 2.47" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    @elseif ($status === 'review')
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 11.75 11.25 14 15.5 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6.75 4.75h10.5A2.75 2.75 0 0 1 20 7.5v9A2.75 2.75 0 0 1 17.25 19.25H6.75A2.75 2.75 0 0 1 4 16.5v-9A2.75 2.75 0 0 1 6.75 4.75Z" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                    @else
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M20 7 9.5 17.5 4 12" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <h2 class="kp-column-title">{{ $column['label'] }}</h2>
                                    <p class="kp-column-desc">{{ $column['description'] }}</p>
                                </div>
                            </div>

                            <div class="kp-count">
                                {{ $todosByStatus[$status]->count() }}
                            </div>
                        </div>

                        <div class="kp-column-body">
                            @if ($status === 'todo')
                                <details>
                                    <summary class="kp-add-summary">
                                        <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                        </svg>
                                        Add task
                                    </summary>

                                    <form wire:submit="addTask" class="kp-form">
                                        <h3 class="kp-form-title">New Task</h3>

                                        <div class="kp-field">
                                            <label for="title">Task title</label>
                                            <input
                                                id="title"
                                                type="text"
                                                wire:model="title"
                                                placeholder="Enter task title"
                                                class="kp-input"
                                            >
                                            @error('title')
                                                <p class="kp-error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="kp-field">
                                            <label for="description">Description</label>
                                            <textarea
                                                id="description"
                                                wire:model="description"
                                                placeholder="Add task details"
                                                class="kp-textarea"
                                            ></textarea>
                                            @error('description')
                                                <p class="kp-error">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="kp-form-grid">
                                            <div class="kp-field">
                                                <label for="priority">Priority</label>
                                                <select id="priority" wire:model="priority" class="kp-select">
                                                    <option value="low">Low</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="high">High</option>
                                                </select>
                                                @error('priority')
                                                    <p class="kp-error">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="kp-field">
                                                <label for="deadline">Deadline</label>
                                                <input
                                                    id="deadline"
                                                    type="date"
                                                    wire:model="deadline"
                                                    class="kp-input"
                                                >
                                                @error('deadline')
                                                    <p class="kp-error">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <button type="submit" class="kp-save-btn" wire:loading.attr="disabled" wire:target="addTask">
                                            <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                            </svg>
                                            <span wire:loading.remove wire:target="addTask">Save task</span>
                                            <span wire:loading wire:target="addTask">Saving...</span>
                                        </button>
                                    </form>
                                </details>
                            @endif

                            @forelse ($todosByStatus[$status] as $todo)
                                @php
                                    $priority = strtolower($todo->priority ?? 'medium');

                                    $deadlineDate = $todo->deadline
                                        ? \Illuminate\Support\Carbon::parse($todo->deadline)
                                        : null;

                                    $isOverdue = $deadlineDate
                                        && $deadlineDate->isBefore(now()->startOfDay())
                                        && $todo->status !== 'done';

                                    $priorityClass = match ($priority) {
                                        'low' => 'kp-pill-low',
                                        'high' => 'kp-pill-high',
                                        default => 'kp-pill-medium',
                                    };
                                @endphp

                                <article
                                    wire:key="todo-card-{{ $todo->id }}"
                                    draggable="{{ $editingTodoId === $todo->id ? 'false' : 'true' }}"
                                    @dragstart="dragStart({{ $todo->id }}, $event)"
                                    @dragend="clearDrag()"
                                    :class="draggingId === {{ $todo->id }} ? 'is-dragging' : ''"
                                    class="kp-card"
                                >
                                    @if ($editingTodoId === $todo->id)
                                        <form wire:submit.prevent="updateTask" class="kp-edit-form" @click.stop>
                                            <div class="kp-field">
                                                <label for="edit-title-{{ $todo->id }}">Task title</label>
                                                <input
                                                    id="edit-title-{{ $todo->id }}"
                                                    type="text"
                                                    wire:model="editTitle"
                                                    placeholder="Enter task title"
                                                    class="kp-input"
                                                >
                                                @error('editTitle')
                                                    <p class="kp-error">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="kp-field">
                                                <label for="edit-description-{{ $todo->id }}">Description</label>
                                                <textarea
                                                    id="edit-description-{{ $todo->id }}"
                                                    wire:model="editDescription"
                                                    placeholder="Add task details"
                                                    class="kp-textarea"
                                                ></textarea>
                                                @error('editDescription')
                                                    <p class="kp-error">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="kp-form-grid">
                                                <div class="kp-field">
                                                    <label for="edit-priority-{{ $todo->id }}">Priority</label>
                                                    <select id="edit-priority-{{ $todo->id }}" wire:model="editPriority" class="kp-select">
                                                        <option value="low">Low</option>
                                                        <option value="medium">Medium</option>
                                                        <option value="high">High</option>
                                                    </select>
                                                    @error('editPriority')
                                                        <p class="kp-error">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="kp-field">
                                                    <label for="edit-deadline-{{ $todo->id }}">Deadline</label>
                                                    <input
                                                        id="edit-deadline-{{ $todo->id }}"
                                                        type="date"
                                                        wire:model="editDeadline"
                                                        class="kp-input"
                                                    >
                                                    @error('editDeadline')
                                                        <p class="kp-error">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="kp-edit-actions">
                                                <button type="submit" class="kp-edit-save-btn" wire:loading.attr="disabled" wire:target="updateTask">
                                                    <span wire:loading.remove wire:target="updateTask">Save</span>
                                                    <span wire:loading wire:target="updateTask">Saving...</span>
                                                </button>

                                                <button type="button" class="kp-edit-cancel-btn" wire:click="cancelEdit" wire:loading.attr="disabled">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="kp-card-top">
                                            <div>
                                                <h3 class="kp-card-title">{{ $todo->title }}</h3>

                                                @if ($todo->description)
                                                    <p class="kp-card-desc">{{ $todo->description }}</p>
                                                @else
                                                    <p class="kp-card-desc">No description added.</p>
                                                @endif
                                            </div>

                                            <div class="kp-card-actions">
                                                <button
                                                    type="button"
                                                    class="kp-edit-btn"
                                                    title="Edit task"
                                                    wire:click="editTask({{ $todo->id }})"
                                                    @click.stop
                                                >
                                                    Edit
                                                </button>

                                                <button
                                                    type="button"
                                                    class="kp-delete-btn"
                                                    title="Delete task"
                                                    onclick="if (! confirm('Delete this task?')) { event.stopImmediatePropagation(); }"
                                                    wire:click="deleteTask({{ $todo->id }})"
                                                    @click.stop
                                                >
                                                    <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M9.75 9.75v6.5M14.25 9.75v6.5M5.75 6.75h12.5M10 4h4a1.5 1.5 0 0 1 1.5 1.5v1.25h-7V5.5A1.5 1.5 0 0 1 10 4ZM7 6.75l.65 11.1A2.25 2.25 0 0 0 9.9 20h4.2a2.25 2.25 0 0 0 2.25-2.15L17 6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="kp-card-meta">
                                            <span class="kp-pill {{ $priorityClass }}">
                                                <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M6 20V5.75A1.75 1.75 0 0 1 7.75 4h8.75l-1.75 4L16.5 12H7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                {{ ucfirst($priority) }}
                                            </span>

                                            @if ($deadlineDate)
                                                <span class="kp-pill {{ $isOverdue ? 'kp-pill-overdue' : '' }}">
                                                    <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M7 3.75v2.5M17 3.75v2.5M4.75 8.75h14.5M7.5 5h9A2.75 2.75 0 0 1 19.25 7.75v8.75A2.75 2.75 0 0 1 16.5 19.25h-9A2.75 2.75 0 0 1 4.75 16.5V7.75A2.75 2.75 0 0 1 7.5 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                    {{ $deadlineDate->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="kp-pill">
                                                    <svg class="kp-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M7 3.75v2.5M17 3.75v2.5M4.75 8.75h14.5M7.5 5h9A2.75 2.75 0 0 1 19.25 7.75v8.75A2.75 2.75 0 0 1 16.5 19.25h-9A2.75 2.75 0 0 1 4.75 16.5V7.75A2.75 2.75 0 0 1 7.5 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                    </svg>
                                                    No deadline
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="kp-empty">
                                    No tasks here yet.
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</div>
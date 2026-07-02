<?php

namespace App\Livewire;

use App\Models\Todo;
use App\Models\Subtask;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TodoDashboard extends Component
{
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_REVIEW = 'review';
    public const STATUS_DONE = 'done';

    public const STATUSES = [
        self::STATUS_TODO,
        self::STATUS_IN_PROGRESS,
        self::STATUS_REVIEW,
        self::STATUS_DONE,
    ];

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
    ];

    public const SORT_RECENT = 'recent';
    public const SORT_ALPHA = 'alpha';
    public const SORT_PRIORITY = 'priority';
    public const SORT_DEADLINE = 'deadline';

    public const SORT_OPTIONS = [
        self::SORT_RECENT,
        self::SORT_ALPHA,
        self::SORT_PRIORITY,
        self::SORT_DEADLINE,
    ];

    public string $title = '';

    public string $description = '';

    public string $priority = 'medium';

    public ?string $deadline = null;

    public string $sortBy = self::SORT_RECENT;

    public ?int $editingTodoId = null;

    public string $editTitle = '';

    public string $editDescription = '';

    public string $editPriority = 'medium';

    public ?string $editDeadline = null;

    public ?int $subtaskTodoId = null;

    public string $subtaskTitle = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', Rule::in(self::PRIORITIES)],
            'deadline' => ['nullable', 'date'],
        ];
    }

    public function addTask(): void
    {
        $this->authorizeTaskAction('create tasks');

        $validated = $this->validate();

        $todo = new Todo();

        $todo->title = trim($validated['title']);
        $todo->description = filled($validated['description'])
            ? trim($validated['description'])
            : null;
        $todo->priority = $validated['priority'];
        $todo->deadline = filled($validated['deadline'])
            ? Carbon::parse($validated['deadline'])->toDateString()
            : null;
        $todo->status = self::STATUS_TODO;

        $todo->save();

        $this->resetTaskForm();
    }

    public function editTask(int $todoId): void
    {
        $this->authorizeTaskAction('update tasks');

        $todo = Todo::query()->findOrFail($todoId);

        $this->editingTodoId = $todo->id;
        $this->editTitle = $todo->title;
        $this->editDescription = $todo->description ?? '';
        $this->editPriority = $todo->priority ?? 'medium';
        $this->editDeadline = $todo->deadline?->format('Y-m-d');

        $this->resetValidation();
    }

    public function updateTask(): void
    {
        $this->authorizeTaskAction('update tasks');

        if ($this->editingTodoId === null) {
            return;
        }

        $validated = $this->validate([
            'editTitle' => ['required', 'string', 'max:255'],
            'editDescription' => ['nullable', 'string', 'max:2000'],
            'editPriority' => ['required', Rule::in(self::PRIORITIES)],
            'editDeadline' => ['nullable', 'date'],
        ]);

        $todo = Todo::query()->findOrFail($this->editingTodoId);

        $todo->title = trim($validated['editTitle']);
        $todo->description = filled($validated['editDescription'])
            ? trim($validated['editDescription'])
            : null;
        $todo->priority = $validated['editPriority'];
        $todo->deadline = filled($validated['editDeadline'])
            ? Carbon::parse($validated['editDeadline'])->toDateString()
            : null;

        $todo->save();

        $this->cancelEdit();
    }

    public function cancelEdit(): void
    {
        $this->editingTodoId = null;
        $this->editTitle = '';
        $this->editDescription = '';
        $this->editPriority = 'medium';
        $this->editDeadline = null;

        $this->resetValidation();
    }

    public function startAddingSubtask(int $todoId): void
    {
        $this->authorizeTaskAction('update tasks');

        Todo::query()->findOrFail($todoId);

        $this->subtaskTodoId = $todoId;
        $this->subtaskTitle = '';

        $this->resetValidation('subtaskTitle');
    }

    public function addSubtask(int $todoId): void
    {
        $this->authorizeTaskAction('update tasks');

        $validated = $this->validate([
            'subtaskTitle' => ['required', 'string', 'max:255'],
        ]);

        $todo = Todo::query()->findOrFail($todoId);

        $todo->subtasks()->create([
            'title' => trim($validated['subtaskTitle']),
            'is_completed' => false,
        ]);

        $this->subtaskTodoId = $todoId;
        $this->subtaskTitle = '';

        $this->resetValidation('subtaskTitle');
    }

    public function cancelAddingSubtask(): void
    {
        $this->subtaskTodoId = null;
        $this->subtaskTitle = '';

        $this->resetValidation('subtaskTitle');
    }

    public function toggleSubtask(int $subtaskId): void
    {
        $this->authorizeTaskAction('update tasks');

        $subtask = Subtask::query()->findOrFail($subtaskId);

        $subtask->is_completed = ! $subtask->is_completed;
        $subtask->save();
    }

    public function deleteSubtask(int $subtaskId): void
    {
        $this->authorizeTaskAction('update tasks');

        Subtask::query()
            ->whereKey($subtaskId)
            ->delete();
    }

    public function updateTodoStatus(int $todoId, string $status): void
    {
        $this->authorizeTaskAction('move tasks');

        if (! in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Invalid task status.',
            ]);
        }

        $todo = Todo::query()->findOrFail($todoId);

        if ($todo->status === $status) {
            return;
        }

        $todo->status = $status;
        $todo->save();
    }

    public function deleteTask(int $todoId): void
    {
        $this->authorizeTaskAction('delete tasks');

        Todo::query()
            ->whereKey($todoId)
            ->delete();
    }

    private function resetTaskForm(): void
    {
        $this->reset([
            'title',
            'description',
            'priority',
            'deadline',
        ]);

        $this->priority = 'medium';

        $this->resetValidation();
    }

    private function authorizeTaskAction(string $permission): void
    {
        if (! auth()->check()) {
            return;
        }

        if (! auth()->user()->can($permission)) {
            abort(403, 'You do not have permission to perform this task action.');
        }
    }

    public function updatedSortBy(string $value): void
    {
        if (! in_array($value, self::SORT_OPTIONS, true)) {
            $this->sortBy = self::SORT_RECENT;
        }
    }

    private function applyTodoSorting($query)
    {
        return match ($this->sortBy) {
            self::SORT_ALPHA => $query
                ->orderBy('title')
                ->latest('updated_at'),

            self::SORT_PRIORITY => $query
                ->orderByRaw("
                    CASE priority
                        WHEN 'high' THEN 1
                        WHEN 'medium' THEN 2
                        WHEN 'low' THEN 3
                        ELSE 4
                    END
                ")
                ->latest('updated_at'),

            self::SORT_DEADLINE => $query
                ->orderByRaw("CASE WHEN deadline IS NULL THEN 1 ELSE 0 END")
                ->orderBy('deadline')
                ->latest('updated_at'),

            default => $query->latest('updated_at'),
        };
    }

    public function render(): View
    {
        $todos = $this->applyTodoSorting(
            Todo::query()->with('subtasks')
                ->orderByRaw("
                    CASE status
                        WHEN 'todo' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'review' THEN 3
                        WHEN 'done' THEN 4
                        ELSE 5
                    END
                ")
        )
            ->get()
            ->groupBy('status');

        return view('livewire.todo-dashboard', [
            'columns' => $this->columns(),
            'todosByStatus' => $this->todosByStatus($todos),
        ]);
    }

    private function columns(): array
    {
        return [
            self::STATUS_TODO => [
                'label' => 'To Do',
                'description' => 'Ideas, plans, and tasks waiting to start.',
                'bar' => 'bg-[#FFD500]',
                'soft' => 'bg-[#FFF9E2]',
                'border' => 'border-[#FFE761]',
                'text' => 'text-yellow-700',
            ],
            self::STATUS_IN_PROGRESS => [
                'label' => 'In Progress',
                'description' => 'Tasks currently being worked on.',
                'bar' => 'bg-sky-400',
                'soft' => 'bg-sky-50',
                'border' => 'border-sky-100',
                'text' => 'text-sky-700',
            ],
            self::STATUS_REVIEW => [
                'label' => 'Needs Review',
                'description' => 'Tasks ready for checking or feedback.',
                'bar' => 'bg-violet-400',
                'soft' => 'bg-violet-50',
                'border' => 'border-violet-100',
                'text' => 'text-violet-700',
            ],
            self::STATUS_DONE => [
                'label' => 'Done',
                'description' => 'Finished tasks and completed work.',
                'bar' => 'bg-emerald-400',
                'soft' => 'bg-emerald-50',
                'border' => 'border-emerald-100',
                'text' => 'text-emerald-700',
            ],
        ];
    }

    private function todosByStatus(Collection $todos): Collection
    {
        return collect(self::STATUSES)->mapWithKeys(function (string $status) use ($todos): array {
            return [
                $status => $todos->get($status, collect()),
            ];
        });
    }
}

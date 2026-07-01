<?php

namespace App\Livewire;

use App\Models\Todo;
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

    public string $title = '';

    public string $description = '';

    public string $priority = 'medium';

    public ?string $deadline = null;

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

    public function updateTodoStatus(int $todoId, string $status): void
    {
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

    public function render(): View
    {
        $todos = Todo::query()
            ->orderByRaw("
                CASE status
                    WHEN 'todo' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'review' THEN 3
                    WHEN 'done' THEN 4
                    ELSE 5
                END
            ")
            ->latest('updated_at')
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
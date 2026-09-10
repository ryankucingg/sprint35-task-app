<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MrCatz\DataTable\MrCatzComponent;

class DashboardPage extends MrCatzComponent
{
    public function mount()
    {
        $this->setTitle('Dashboard');
        session()->put('active', 'admin-dashboard');
        $this->breadcrumbs = [
            ['title' => 'Dashboard', 'url' => null]
        ];
    }

    private function scope()
    {
        return Auth::user()->isAdmin()
            ? Task::query()
            : Task::where('user_id', Auth::id());
    }

    private function stats(): array
    {
        $today = Carbon::today()->toDateString();
        $nextWeek = Carbon::today()->addDays(7)->toDateString();

        $base = $this->scope();

        return [
            'total' => (clone $base)->count(),
            'belum' => (clone $base)->where('status', Task::STATUS_BELUM)->count(),
            'dikerjakan' => (clone $base)->where('status', Task::STATUS_DIKERJAKAN)->count(),
            'selesai' => (clone $base)->where('status', Task::STATUS_SELESAI)->count(),
            'terlambat' => (clone $base)
                ->where('status', '!=', Task::STATUS_SELESAI)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->count(),
            'segera' => (clone $base)
                ->where('status', '!=', Task::STATUS_SELESAI)
                ->whereBetween('due_date', [$today, $nextWeek])
                ->count(),
        ];
    }

    private function upcomingTasks()
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7)->endOfDay();

        return $this->scope()
            ->with(['category', 'user'])
            ->where('status', '!=', Task::STATUS_SELESAI)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $nextWeek)
            ->orderBy('due_date')
            ->limit(8)
            ->get()
            ->map(function (Task $task) use ($today) {
                $due = Carbon::parse($task->due_date)->startOfDay();
                if ($due->lt($today)) {
                    $label = 'Terlambat ' . $due->diffInDays($today) . ' hari';
                    $tone = 'error';
                } elseif ($due->isSameDay($today)) {
                    $label = 'Jatuh tempo hari ini';
                    $tone = 'warning';
                } elseif ($due->isSameDay($today->copy()->addDay())) {
                    $label = 'Besok';
                    $tone = 'warning';
                } else {
                    $label = $due->diffInDays($today) . ' hari lagi';
                    $tone = 'info';
                }

                return (object) [
                    'id' => $task->id,
                    'title' => $task->title,
                    'owner' => Auth::user()->isAdmin() ? $task->user?->name : null,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'category' => $task->category?->name,
                    'due_label' => $due->locale('id')->translatedFormat('d M Y'),
                    'relative' => $label,
                    'tone' => $tone,
                ];
            });
    }

    private function perUserStats()
    {
        $today = Carbon::today()->toDateString();

        return DB::table('users')
            ->leftJoin('tasks', 'tasks.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.role')
            ->orderBy('users.name')
            ->select('users.id', 'users.name', 'users.role',
                DB::raw('COUNT(tasks.id) as total'),
                DB::raw("SUM(CASE WHEN tasks.status = '" . Task::STATUS_BELUM . "' THEN 1 ELSE 0 END) as belum"),
                DB::raw("SUM(CASE WHEN tasks.status = '" . Task::STATUS_DIKERJAKAN . "' THEN 1 ELSE 0 END) as dikerjakan"),
                DB::raw("SUM(CASE WHEN tasks.status = '" . Task::STATUS_SELESAI . "' THEN 1 ELSE 0 END) as selesai"),
                DB::raw("SUM(CASE WHEN tasks.due_date IS NOT NULL AND tasks.due_date < '" . $today . "' AND tasks.status != '" . Task::STATUS_SELESAI . "' THEN 1 ELSE 0 END) as terlambat")
            )
            ->get();
    }

    private function priorityStats(): array
    {
        $base = $this->scope();

        return [
            'labels' => array_values(Task::PRIORITIES),
            'series' => [
                (clone $base)->where('priority', Task::PRIORITY_RENDAH)->count(),
                (clone $base)->where('priority', Task::PRIORITY_SEDANG)->count(),
                (clone $base)->where('priority', Task::PRIORITY_TINGGI)->count(),
            ],
        ];
    }

    private function timelinessStats(): array
    {
        $today = Carbon::today()->toDateString();
        $base = $this->scope()->whereNotNull('due_date');

        $terlambat = (clone $base)
            ->whereDate('due_date', '<', $today)
            ->where('status', '!=', Task::STATUS_SELESAI)
            ->count();

        $tepatWaktu = (clone $base)->count() - $terlambat;

        return [
            'labels' => ['Tepat Waktu', 'Terlambat'],
            'series' => [$tepatWaktu, $terlambat],
        ];
    }

    public function render()
    {
        $stats = $this->stats();

        return view('livewire.admin.dashboard.dashboard-page', [
            'stats' => $stats,
            'upcoming' => $this->upcomingTasks(),
            'chart' => [
                'labels' => array_values(Task::STATUSES),
                'series' => [$stats['belum'], $stats['dikerjakan'], $stats['selesai']],
            ],
            'priorityChart' => $this->priorityStats(),
            'timelyChart' => $this->timelinessStats(),
            'perUser' => Auth::user()->isAdmin() ? $this->perUserStats() : null,
        ])->layout('components.layouts.admin_layout');
    }
}

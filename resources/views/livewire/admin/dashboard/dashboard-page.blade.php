@push('title')
    <title>Dashboard - {{ config('site.name') }}</title>
@endpush

@php
    $cards = [
        ['Total Tugas', $stats['total'], 'checklist', 'text-primary', null],
        ['Belum Dimulai', $stats['belum'], 'schedule', 'text-base-content/60', null],
        ['Dikerjakan', $stats['dikerjakan'], 'autorenew', 'text-warning', null],
        ['Selesai', $stats['selesai'], 'task_alt', 'text-success', null],
        ['Terlambat', $stats['terlambat'], 'event_busy', 'text-error', $stats['terlambat'] > 0 ? 'Lewat tenggat waktu' : null],
        ['Mendekati Tenggat', $stats['segera'], 'hourglass_top', 'text-info', $stats['segera'] > 0 ? '7 hari ke depan' : null],
    ];

    $toneMap = [
        'error' => ['bg' => 'bg-error/10', 'text' => 'text-error'],
        'warning' => ['bg' => 'bg-warning/10', 'text' => 'text-warning'],
        'info' => ['bg' => 'bg-info/10', 'text' => 'text-info'],
    ];
@endphp

<div class="grid-row p-4 sm:p-6">

    @include('mrcatz::components.ui.breadcrumbs')

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-3 mb-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Halo, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-base-content/50 mt-1">
                {{ (auth()->user()->isAdmin() ? 'Ringkasan seluruh tugas pengguna, ' : 'Ringkasan tugas Anda hari ini, ') . now()->locale('id')->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.all-tasks') }}" class="btn btn-primary btn-sm gap-1.5">
                    <span class="material-symbols-outlined text-base">assignment</span>
                    Kelola Semua Tugas
                </a>
            @else
                <a href="{{ route('admin.tasks') }}" class="btn btn-primary btn-sm gap-1.5">
                    <span class="material-symbols-outlined text-base">checklist</span>
                    Kelola Tugas Saya
                </a>
            @endif
        </div>
    </div>

    @if($stats['total'] === 0)
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body items-center text-center py-14">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary" style="font-size: 32px;">checklist</span>
                </div>
                <h3 class="text-lg font-bold text-base-content">Belum ada tugas</h3>
                <p class="text-sm text-base-content/50 mb-4 max-w-sm">
                    @if(auth()->user()->isAdmin())
                        Belum ada tugas yang tercatat untuk para pengguna. Buatkan tugas pertama untuk mulai memantau pekerjaan mereka.
                    @else
                        Mulai catat pekerjaan Anda — tetapkan prioritas dan tenggat waktu agar mudah dipantau.
                    @endif
                </p>
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.all-tasks' : 'admin.tasks') }}" class="btn btn-primary btn-sm gap-1.5">
                    <span class="material-symbols-outlined text-base">add_task</span>
                    {{ auth()->user()->isAdmin() ? 'Buatkan Tugas untuk Pengguna' : 'Tambah Tugas Pertama' }}
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
            @foreach($cards as [$label, $value, $icon, $color, $sub])
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body p-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-base-content/50 leading-snug">{{ $label }}</span>
                            <span class="material-symbols-outlined {{ $color }} shrink-0">{{ $icon }}</span>
                        </div>
                        <div class="text-3xl font-extrabold mt-1 text-base-content">{{ $value }}</div>
                        @if($sub)
                            <div class="text-[11px] text-base-content/50 leading-snug">{{ $sub }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-5">
            <div class="lg:col-span-2 card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-base-content/80 flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-primary text-base">donut_large</span>
                        Distribusi Status
                    </h3>
                    <div id="status-donut"></div>
                </div>
            </div>

            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body p-4 sm:p-5">
                        <h3 class="text-sm font-semibold text-base-content/80 flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-base">low_priority</span>
                            Prioritas Tugas
                        </h3>
                        <div id="priority-bar"></div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body p-4 sm:p-5">
                        <h3 class="text-sm font-semibold text-base-content/80 flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-base">schedule</span>
                            Ketepatan Tenggat
                        </h3>
                        <div id="timely-bar"></div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-base-content/80 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-base">hourglass_top</span>
                            Tugas Mendekati Tenggat
                        </h3>
                        <a href="{{ route('admin.tasks') }}" class="btn btn-ghost btn-xs text-base-content/50">
                            Lihat semua
                        </a>
                    </div>

                    @if($upcoming->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <span class="material-symbols-outlined text-success/60 mb-2" style="font-size: 36px;">celebration</span>
                            <p class="text-sm text-base-content/50">Tidak ada tugas mendekati tenggat. Kerja rapi!</p>
                        </div>
                    @else
                        <ul class="divide-y divide-base-content/5">
                            @foreach($upcoming as $task)
                                @php $tone = $toneMap[$task->tone]; @endphp
                                <li class="py-2.5 first:pt-0 last:pb-0 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg {{ $tone['bg'] }} {{ $tone['text'] }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-lg">{{ $task->tone === 'error' ? 'event_busy' : 'schedule' }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-base-content truncate">{{ $task->title }}</p>
                                        <p class="text-xs text-base-content/50 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                            <span class="{{ $tone['text'] }} font-semibold">{{ $task->relative }}</span>
                                            <span class="text-base-content/30">•</span>
                                            <span>{{ $task->due_label }}</span>
                                            @if($task->owner)
                                                <span class="text-base-content/30">•</span>
                                                <span class="font-medium text-base-content/70">{{ $task->owner }}</span>
                                            @endif
                                            @if($task->category)
                                                <span class="text-base-content/30">•</span>
                                                <span>{{ $task->category }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="badge {{ \App\Models\Task::priorityBadge($task->priority) }} badge-sm text-white whitespace-nowrap shrink-0">
                                        {{ \App\Models\Task::PRIORITIES[$task->priority] ?? $task->priority }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        @if($perUser !== null)
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-base-content/80 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-base">groups</span>
                            Statistik Tugas per Pengguna
                        </h3>
                        <a href="{{ route('admin.all-tasks') }}" class="btn btn-ghost btn-xs text-base-content/50">
                            Semua tugas
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="text-base-content/50 text-xs uppercase tracking-wide">
                                    <th>Pengguna</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Belum</th>
                                    <th class="text-center">Dikerjakan</th>
                                    <th class="text-center">Selesai</th>
                                    <th class="text-center">Terlambat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perUser as $row)
                                    <tr class="hover:bg-base-200/50">
                                        <td>
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($row->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-base-content truncate">{{ $row->name }}</p>
                                                    <span class="text-[10px] uppercase text-base-content/40">{{ str_replace('-', ' ', $row->role) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-semibold">{{ $row->total }}</td>
                                        <td class="text-center"><span class="badge badge-neutral badge-sm">{{ $row->belum }}</span></td>
                                        <td class="text-center"><span class="badge badge-warning badge-sm">{{ $row->dikerjakan }}</span></td>
                                        <td class="text-center"><span class="badge badge-success badge-sm">{{ $row->selesai }}</span></td>
                                        <td class="text-center">
                                            @if($row->terlambat > 0)
                                                <span class="badge badge-error badge-sm text-white">{{ $row->terlambat }}</span>
                                            @else
                                                <span class="text-base-content/30">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>

@push('scripts')
    @if($stats['total'] > 0)
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            (function () {
                var statusData = @json($chart);
                var priorityData = @json($priorityChart);
                var timelyData = @json($timelyChart);
                var instances = {};

                function isDark() {
                    return (document.documentElement.getAttribute('data-theme') || '').includes('dark');
                }

                function chrome() {
                    return {
                        theme: { mode: isDark() ? 'dark' : 'light' },
                        grid: { borderColor: isDark() ? '#243247' : '#eef1f6' },
                        foreColor: isDark() ? '#c2cee0' : '#5a6a84',
                    };
                }

                function base(height) {
                    var c = chrome();
                    return {
                        chart: {
                            type: 'bar',
                            height: height,
                            fontFamily: 'inherit',
                            background: 'transparent',
                            toolbar: { show: false },
                        },
                        theme: c.theme,
                        grid: c.grid,
                        foreColor: c.foreColor,
                        plotOptions: {
                            bar: { borderRadius: 6, barHeight: '60%', distributed: true },
                        },
                        dataLabels: { enabled: true },
                        xaxis: { axisTicks: { show: false } },
                        legend: { show: false },
                    };
                }

                function render() {
                    Object.values(instances).forEach(function (chart) { chart.destroy(); });

                    var donutEl = document.getElementById('status-donut');
                    if (donutEl) {
                        instances.donut = new ApexCharts(donutEl, {
                            chart: {
                                type: 'donut',
                                height: 320,
                                fontFamily: 'inherit',
                                background: 'transparent',
                                toolbar: { show: false },
                            },
                            theme: chrome().theme,
                            labels: statusData.labels,
                            series: statusData.series,
                            colors: ['#9aa8bd', '#d3b56a', '#7cb98c'],
                            legend: { position: 'bottom', fontSize: '12px' },
                            dataLabels: { enabled: true },
                            stroke: { width: 0 },
                            plotOptions: { pie: { donut: { size: '68%' } } },
                            noData: { text: 'Belum ada tugas' },
                        });
                        instances.donut.render();
                    }

                    var priorityEl = document.getElementById('priority-bar');
                    if (priorityEl) {
                        instances.priority = new ApexCharts(priorityEl, {
                            ...base(150),
                            series: [{ name: 'Tugas', data: priorityData.series }],
                            colors: ['#7ba0cc', '#d3b56a', '#cd7f89'],
                            xaxis: { categories: priorityData.labels, axisTicks: { show: false } },
                        });
                        instances.priority.render();
                    }

                    var timelyEl = document.getElementById('timely-bar');
                    if (timelyEl) {
                        instances.timely = new ApexCharts(timelyEl, {
                            ...base(150),
                            series: [{ name: 'Tugas', data: timelyData.series }],
                            colors: ['#7cb98c', '#cd7f89'],
                            xaxis: { categories: timelyData.labels, axisTicks: { show: false } },
                        });
                        instances.timely.render();
                    }
                }

                function boot() {
                    render();
                    new MutationObserver(render).observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['data-theme'],
                    });
                }

                if (window.ApexCharts) {
                    boot();
                } else {
                    var check = setInterval(function () {
                        if (window.ApexCharts) {
                            clearInterval(check);
                            boot();
                        }
                    }, 50);
                }
            })();
        </script>
    @endif
@endpush

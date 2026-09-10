@php($showOwner = $showOwner ?? false)

<div class="modal {{ $showDetailModal ? 'modal-open' : '' }}">
    <div class="modal-box max-w-lg">
        @if($detailTask)
            <div class="flex items-start justify-between gap-3 mb-4">
                <div class="min-w-0">
                <h3 class="text-lg font-bold text-base-content leading-snug break-words">{{ $detailTask->title }}</h3>
                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                        <span class="badge {{ \App\Models\Task::statusBadge($detailTask->status) }} badge-sm">
                            {{ \App\Models\Task::STATUSES[$detailTask->status] ?? $detailTask->status }}
                        </span>
                        <span class="badge {{ \App\Models\Task::priorityBadge($detailTask->priority) }} badge-sm text-white">
                            {{ \App\Models\Task::PRIORITIES[$detailTask->priority] ?? $detailTask->priority }}
                        </span>
                        @if($detailTask->isOverdue())
                            <span class="badge badge-error badge-sm text-white">Terlambat</span>
                        @endif
                    </div>
                </div>
                <button class="btn btn-ghost btn-sm btn-square shrink-0" wire:click="closeDetail">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-2.5 text-sm">
                @if($showOwner && $detailTask->user)
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-base-content/50 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">person</span>
                            Pemilik
                        </span>
                        <span class="font-medium text-base-content">{{ $detailTask->user->name }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between gap-4">
                    <span class="text-base-content/50 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">category</span>
                        Kategori
                    </span>
                    <span class="font-medium text-base-content">{{ $detailTask->category?->name ?? 'Tanpa Kategori' }}</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-base-content/50 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">event</span>
                        Tenggat
                    </span>
                    <span class="font-medium {{ $detailTask->isOverdue() ? 'text-error' : 'text-base-content' }}">
                        {{ $detailTask->due_date
                            ? $detailTask->due_date->locale('id')->translatedFormat('d F Y')
                            : 'Tidak ditentukan' }}
                    </span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-base-content/50 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">schedule</span>
                        Dibuat
                    </span>
                    <span class="font-medium text-base-content">{{ $detailTask->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span class="text-base-content/50 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">update</span>
                        Diperbarui
                    </span>
                    <span class="font-medium text-base-content">{{ $detailTask->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}</span>
                </div>
            </div>

            @if($detailTask->description)
                <div class="mt-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-base-content/50 mb-1.5 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">notes</span>
                        Deskripsi
                    </p>
                    <div class="rounded-xl bg-base-200/60 border border-base-content/10 p-3.5 text-sm text-base-content/80 leading-relaxed whitespace-pre-wrap">{{ $detailTask->description }}</div>
                </div>
            @endif

            <div class="modal-action">
                <button class="btn btn-ghost btn-sm" wire:click="closeDetail">Tutup</button>
                <button class="btn btn-primary btn-sm gap-1.5" wire:click="editFromDetail({{ $detailTask->id }})">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Edit Tugas
                </button>
            </div>
        @endif
    </div>
    <form method="dialog" class="modal-backdrop">
        <button wire:click="closeDetail">close</button>
    </form>
</div>

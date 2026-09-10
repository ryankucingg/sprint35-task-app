<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    public const STATUS_BELUM = 'belum-dimulai';

    public const STATUS_DIKERJAKAN = 'dikerjakan';

    public const STATUS_SELESAI = 'selesai';

    public const STATUSES = [
        self::STATUS_BELUM => 'Belum Dimulai',
        self::STATUS_DIKERJAKAN => 'Dikerjakan',
        self::STATUS_SELESAI => 'Selesai',
    ];

    public const PRIORITY_RENDAH = 'rendah';

    public const PRIORITY_SEDANG = 'sedang';

    public const PRIORITY_TINGGI = 'tinggi';

    public const PRIORITIES = [
        self::PRIORITY_RENDAH => 'Rendah',
        self::PRIORITY_SEDANG => 'Sedang',
        self::PRIORITY_TINGGI => 'Tinggi',
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->status !== self::STATUS_SELESAI
            && $this->due_date->isPast();
    }

    public function isDueSoon(int $days = 7): bool
    {
        return $this->due_date !== null
            && $this->status !== self::STATUS_SELESAI
            && $this->due_date->between(now()->startOfDay(), now()->addDays($days)->endOfDay());
    }

    public static function statusBadge(string $status): string
    {
        return match ($status) {
            self::STATUS_DIKERJAKAN => 'badge-warning',
            self::STATUS_SELESAI => 'badge-success',
            default => 'badge-neutral',
        };
    }

    public static function priorityBadge(string $priority): string
    {
        return match ($priority) {
            self::PRIORITY_TINGGI => 'badge-error',
            self::PRIORITY_SEDANG => 'badge-warning',
            default => 'badge-info',
        };
    }
}

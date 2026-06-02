<?php

namespace BastModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    use HasFactory;

    protected $table = 'user_reports';

    protected $fillable = [
        'user_id',
        'recipient_id',
        'giver_id',
        'recipient_snapshot',
        'giver_snapshot',
        'header_snapshot',
        'report_number',
        'assets_snapshot',
        'handover_date',
    ];

    protected $casts = [
        'recipient_snapshot' => 'array',
        'giver_snapshot' => 'array',
        'header_snapshot' => 'array',
        'assets_snapshot' => 'array',
        'handover_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recipient_id');
    }

    public function giver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'giver_id');
    }

    public function getRecipientDisplayNameAttribute(): string
    {
        $snapshotName = trim(collect([
            data_get($this->recipient_snapshot, 'first_name'),
            data_get($this->recipient_snapshot, 'last_name'),
        ])->filter()->implode(' '));

        if ($snapshotName !== '') {
            return $snapshotName;
        }

        if ($this->recipient) {
            return trim(collect([
                $this->recipient->first_name,
                $this->recipient->last_name,
            ])->filter()->implode(' '));
        }

        return 'User Tidak Ditemukan / Dihapus';
    }
}

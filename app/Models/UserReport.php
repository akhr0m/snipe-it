<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'recipient_id',
        'giver_id',
        'report_number',
        'assets_snapshot',
        'handover_date',
    ];

    public function recipient()
    {
        // Menghubungkan recipient_id ke model User Snipe-IT
        return $this->belongsTo(\App\Models\User::class, 'recipient_id');
    }
}

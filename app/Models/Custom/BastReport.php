<?php

namespace App\Models\Custom;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BastReport extends Model
{
    protected $table = 'bast_reports';

    protected $fillable = [
        'bast_number',
        'user_id',
        'username',
        'user_email',
        'user_nik',
        'user_jobtitle',
        'user_department',
        'user_location',
        'admin_name',
        'admin_nik',
        'admin_title',
        'admin_department',
        'admin_location',
        'date_printed',
        'perihal',
        'notes',
        'return_notes',
        'assets_data',
    ];

    protected $casts = [
        'assets_data' => 'array',
        'date_printed' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

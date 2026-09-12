<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLogs extends Model
{
    use HasFactory;

    protected $table = 'api_logs';

    protected $fillable = [
        'table',
        'user_id',
        'ip_address',
        'platform',
        'browser',
        'activity',
        'method_function',
        'browser_version',
        'is_mobile',
        'is_robot',
        'id_desktop',
        'referer',
        'agent_string',
    ];
}

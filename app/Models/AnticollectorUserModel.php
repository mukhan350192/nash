<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnticollectorUserModel extends Model
{
    use HasFactory;
    protected $table = 'anticollector_users';
    protected $fillable = [
        'fio',
        'email',
        'token',
        'password',
        'iin',
        'phone',
    ];
}

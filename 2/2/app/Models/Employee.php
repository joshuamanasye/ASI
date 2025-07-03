<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    public $timestamps = false;

    protected $fillable = [
        'nomor', 'nama', 'jabatan', 'talahir', 'photo_upload_path', 'created_on',
        'update_on', 'created_by', 'update_by', 'deleted_on'
    ];
}

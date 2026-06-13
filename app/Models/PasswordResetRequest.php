<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $table = 'password_reset_request';
    protected $primaryKey = 'reset_id';
    public $timestamps = false;
    protected $guarded = [];
}

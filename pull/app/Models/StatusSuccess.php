<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusSuccess extends Model
{
    protected $table = 'status_success';
    protected $fillable = ['pr_number', 'title', 'url'];
}

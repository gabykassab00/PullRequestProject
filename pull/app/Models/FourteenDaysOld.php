<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FourteenDaysOld extends Model
{
    protected $table = 'fourteen_days_old';
    protected $fillable = ['pr_number', 'title', 'url'];
}

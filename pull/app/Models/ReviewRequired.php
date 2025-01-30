<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewRequired extends Model
{
    protected $table = 'review_required';
    protected $fillable = ['pr_number', 'title', 'url'];
}

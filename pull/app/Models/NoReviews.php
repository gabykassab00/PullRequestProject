<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoReviews extends Model
{
    protected $table = 'no_reviews';
    protected $fillable = ['pr_number', 'title', 'url'];
}

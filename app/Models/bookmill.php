<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class bookmill extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table="novel_bookmill";
    protected $guarded = [];
}

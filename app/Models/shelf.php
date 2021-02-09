<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shelf extends Model
{
    use HasFactory;
    protected $table="novel_shelf";
    protected $guarded = [];
    public function book()
    {
        return $this->belongsTo(bookmill::class, 'book_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NovelDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "novel_details";
    protected $guarded = [];
    public function Novel()
    {
        return $this->belongsTo(bookmill::class, 'book_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class wxUser extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "novel_users";
    protected $guarded = [];
    public function Shelf()
    {
        return $this->hasMany(shelf::class, 'user_id');
    }
}

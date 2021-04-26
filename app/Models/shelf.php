<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\shelf
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $book_id
 * @property string|null $url
 * @property string|null $current_page_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\bookmill|null $book
 * @method static \Illuminate\Database\Eloquent\Builder|shelf newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|shelf newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|shelf query()
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereCurrentPageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|shelf whereUserId($value)
 * @mixin \Eloquent
 */
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\bookmill
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $url
 * @property string|null $author
 * @property string|null $newest
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill newQuery()
 * @method static \Illuminate\Database\Query\Builder|bookmill onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill query()
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereNewest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bookmill whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|bookmill withTrashed()
 * @method static \Illuminate\Database\Query\Builder|bookmill withoutTrashed()
 * @mixin \Eloquent
 */
class bookmill extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table="novel_bookmill";
    protected $guarded = [];
}

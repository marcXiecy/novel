<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\NovelDetail
 *
 * @property int $id
 * @property int $book_id
 * @property int $catalog_id
 * @property string $title
 * @property string $source_href
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\bookmill $Novel
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|NovelDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereCatalogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereSourceHref($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NovelDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NovelDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NovelDetail withoutTrashed()
 * @mixin \Eloquent
 */
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

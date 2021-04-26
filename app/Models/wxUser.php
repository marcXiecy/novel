<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\wxUser
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $avatar
 * @property string|null $dob
 * @property int|null $gender
 * @property string|null $phone
 * @property int|null $points
 * @property string|null $tips
 * @property string|null $wx_mini_openid
 * @property string|null $wx_offcial_openid
 * @property string|null $setting
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\shelf[] $Shelf
 * @property-read int|null $shelf_count
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|wxUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereWxMiniOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|wxUser whereWxOffcialOpenid($value)
 * @method static \Illuminate\Database\Query\Builder|wxUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|wxUser withoutTrashed()
 * @mixin \Eloquent
 */
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

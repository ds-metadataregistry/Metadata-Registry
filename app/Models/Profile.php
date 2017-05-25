<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Profile
 *
 * @property int                                                                         $id
 * @property \Carbon\Carbon                                                              $created_at
 * @property \Carbon\Carbon                                                              $updated_at
 * @property string                                                                      $deleted_at
 * @property int                                                                         $created_by
 * @property int                                                                         $updated_by
 * @property int                                                                         $deleted_by
 * @property string                                                                      $child_updated_at
 * @property int                                                                         $child_updated_by
 * @property string                                                                      $name
 * @property string                                                                      $note
 * @property string                                                                      $uri
 * @property string                                                                      $url
 * @property string                                                                      $base_domain
 * @property string                                                                      $token
 * @property string                                                                      $community
 * @property int                                                                         $last_uri_id
 * @property int                                                                         $status_id
 * @property string                                                                      $language
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ElementSet[]      $elementSets
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProfileProperty[] $profileProperties
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[]         $projects
 * @property-read \App\Models\Status                                                     $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vocabulary[]      $vocabularies
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereBaseDomain( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereChildUpdatedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereChildUpdatedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCommunity( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCreatedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereCreatedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereDeletedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereDeletedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereLanguage( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereLastUriId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereName( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereNote( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereStatusId( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereToken( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereUpdatedAt( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereUpdatedBy( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereUri( $value )
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Profile whereUrl( $value )
 * @mixin \Eloquent
 */
class Profile extends Model
{
    protected $table = self::TABLE;

    const TABLE = 'profile';

    use SoftDeletes;

    /*********************************
     * relationships
     **********************************/

    public function status()
    {
        return $this->belongsTo( \App\Models\Status::class, 'status_id', 'id' );
    }

    public function profileProperties()
    {
        return $this->hasMany( \App\Models\ProfileProperty::class, 'profile_id', 'id' );
    }

    public function elementSets()
    {
        return $this->hasMany( \App\Models\ElementSet::class, 'profile_id', 'id' );
    }

    public function vocabularies()
    {
        return $this->hasMany( \App\Models\Vocabulary::class, 'profile_id', 'id' );
    }

    public function projects()
    {
        return $this->belongsToMany( Project::class );
    }

    /*********************************
     * lookup functions
     **********************************/

    public function requiredProperties()
    {
        return $this->ProfileProperties()->whereIsRequired( true )->get();
    }
}

<?php namespace App\Models;

use App\Helpers\Macros\Traits\Languages;
use App\Models\Traits\BelongsToElement;
use App\Models\Traits\BelongsToElementset;
use App\Models\Traits\BelongsToImport;
use App\Models\Traits\BelongsToProfileProperty;
use App\Models\Traits\BelongsToRelatedElement;
use App\Models\Traits\HasStatus;
use Culpa\Traits\Blameable;
use Culpa\Traits\CreatedBy;
use Illuminate\Database\Eloquent\Model as Model;
use Laracasts\Matryoshka\Cacheable;

/**
 * App\Models\ElementAttributeHistory
 *
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_user_id
 * @property string $action
 * @property int $schema_property_element_id
 * @property int $schema_property_id
 * @property int $schema_id
 * @property int $profile_property_id
 * @property string $object
 * @property int $related_schema_property_id
 * @property string $language
 * @property int $status_id
 * @property string $change_note
 * @property int $import_id
 * @property-read \App\Models\Access\User\User $creator
 * @property-read \App\Models\Element $element
 * @property-read \App\Models\ElementAttribute $element_attribute
 * @property-read \App\Models\Elementset $elementset
 * @property-read mixed $current_language
 * @property-read mixed $default_language
 * @property-read \App\Models\Import $import
 * @property-read \App\Models\ProfileProperty $profile_property
 * @property-read \App\Models\Element $related_element
 * @property-read \App\Models\Status $status
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereChangeNote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereCreatedUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereImportId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereLanguage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereObject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereProfilePropertyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereRelatedSchemaPropertyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereSchemaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereSchemaPropertyElementId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereSchemaPropertyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereStatusId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ElementAttributeHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ElementAttributeHistory extends Model
{
    const TABLE = 'reg_schema_property_element_history';
    protected $table = self::TABLE;
    use HasStatus, Blameable, CreatedBy;
    use Cacheable;
    use Languages, BelongsToProfileProperty, BelongsToElementset, BelongsToElement, BelongsToImport, BelongsToRelatedElement;
    protected $blameable = [
        'created' => 'created_user_id',
    ];
    protected $guarded = [ 'id' ];
    protected $casts = [
        'id'                         => 'integer',
        'created_user_id'            => 'integer',
        'action'                     => 'string',
        'schema_property_element_id' => 'integer',
        'schema_property_id'         => 'integer',
        'schema_id'                  => 'integer',
        'profile_property_id'        => 'integer',
        'object'                     => 'string',
        'related_schema_property_id' => 'integer',
        'language'                   => 'string',
        'status_id'                  => 'integer',
        'change_note'                => 'string',
        'import_id'                  => 'integer',
    ];
    public static $rules = [
        'created_at'  => 'required|',
        'object'      => 'max:65535',
        'language'    => 'required|max:6',
        'change_note' => 'max:65535',
    ];

    public function element_attribute()
    {
        return $this->belongsTo( ElementAttribute::class,
            'schema_property_element_id',
            'id' );
    }

}

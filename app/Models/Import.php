<?php namespace App\Models;

use App\Models\Traits\BelongsToElementset;
use App\Models\Traits\BelongsToUser;
use App\Models\Traits\BelongsToVocabulary;
use Backpack\CRUD\CrudTrait;
use Culpa\Traits\Blameable;
use Culpa\Traits\CreatedBy;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Import
 *
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $source_file_name
 * @property string $source
 * @property string $map stores the serialized column map array
 * @property int $user_id
 * @property string $file_name
 * @property string $file_type
 * @property string $results stores the serialized results of the import
 * @property int $total_processed_count
 * @property int $error_count
 * @property int $success_count
 * @property int $batch_id
 * @property int $vocabulary_id
 * @property int $schema_id
 * @property int $token
 * @property string $instructions
 * @property-read \App\Models\Access\User\User $User
 * @property-read \App\Models\Batch $batch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ConceptAttributeHistory[] $concept_history
 * @property-read \App\Models\Access\User\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ElementAttributeHistory[] $element_history
 * @property-read \App\Models\Elementset $elementset
 * @property string $worksheet
 * @property-read \App\Models\Vocabulary $vocabulary
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereBatchId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereErrorCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereFileType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereInstructions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereMap($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereResults($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereSchemaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereSource($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereSourceFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereSuccessCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereTotalProcessedCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Import whereVocabularyId($value)
 * @mixin \Eloquent
 */
class Import extends Model
{
    const TABLE = 'reg_file_import_history';
    protected $table = self::TABLE;
    use Blameable, CreatedBy;
    use CrudTrait;
    use BelongsToVocabulary, BelongsToElementset, BelongsToUser;
    protected $blameable = [
        'created' => 'user_id',
    ];
    protected $guarded = [ 'id' ];
    protected $casts = [
        'id'                    => 'integer',
        'map'                   => 'string',
        'user_id'               => 'integer',
        'vocabulary_id'         => 'integer',
        'schema_id'             => 'integer',
        'file_name'             => 'string',
        'source_file_name'      => 'string',
        'file_type'             => 'string',
        'batch_id'              => 'integer',
        'results'               => 'string',
        'total_processed_count' => 'integer',
        'error_count'           => 'integer',
        'success_count'         => 'integer',
    ];
    public static $rules = [
        'map'              => 'max:65535',
        'file_name'        => 'max:255',
        'source_file_name' => 'max:255',
        'file_type'        => 'max:30',
        'results'          => 'max:65535',
    ];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo|null
     */
    public function batch(): ?BelongsTo
    {
        return $this->belongsTo( Batch::class, 'batch_id', 'id' );
    }

    /**
     * @return HasMany|null
     */
    public function concept_history(): ?HasMany
    {
        return $this->hasMany( ConceptAttributeHistory::class, 'import_id', 'id' );
    }

    /**
     * @return HasMany|null
     */
    public function element_history(): ?HasMany
    {
        return $this->hasMany(ElementAttributeHistory::class, 'import_id', 'id');
    }

    /**
     * @return BelongsToMany|null
     */
    public function projects(): ?BelongsToMany
    {
        return $this->morphedByMany(Project::class, 'importable')->withTimestamps();
    }

    /**
     * @return BelongsToMany|null
     */
    public function vocabularies(): ?BelongsToMany
    {
        return $this->morphedByMany(Vocabulary::class, 'importable')->withTimestamps();
    }

    /**
     * @return BelongsToMany|null
     */
    public function elementsets(): ?BelongsToMany
    {
        return $this->morphedByMany(Elementset::class, 'importable')->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * @param string $value
     *
     * @return string
     */
    public function getWorksheetAttribute($value): string
    {
        return $this->attributes['source_file_name'];
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * @param string $value
     *
     * @return string
     */
    public function setWorksheetAttribute($value): string
    {
        return $this->attributes['source_file_name'] = $value;
    }
}

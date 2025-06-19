<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'description',
        'user_id',
        'model_type',
        'model_id',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model.
     */
    public function subject()
    {
        return $this->morphTo('model');
    }

    /**
     * Scope a query to only include activities of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Create a new activity record.
     */
    public static function log($type, $description, $model = null, $userId = null)
    {
        return self::create([
            'type' => $type,
            'description' => $description,
            'user_id' => $userId,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
        ]);
    }
}

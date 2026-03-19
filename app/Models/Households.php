<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Households extends Model
{
    use SoftDeletes;

    protected $table = 'households';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'household_code',
        'purok_id',
        'address',
        'head_id',
        'created_by',
        'updated_by',
        'status',
        'archived',
    ];

    protected $casts = [
        'archived'    => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $year = now()->year;

            $last = self::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('household_code');

            $number = 1;

            if ($last && preg_match('/(\d+)$/', $last, $matches)) {
                $number = ((int)$matches[1]) + 1;
            }

            $model->household_code = 'HH-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
        });
    }

    public function residents()
    {
        return $this->hasMany(Residents::class, 'household_id');
    }

    public function head()
    {
        return $this->belongsTo(Residents::class, 'head_id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purok()
    {
        return $this->belongsTo(Puroks::class, 'purok_id');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}

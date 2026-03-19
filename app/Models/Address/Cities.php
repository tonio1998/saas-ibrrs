<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cities extends Model
{
    use SoftDeletes;

    protected $table = 'loc_cities';

    protected $fillable = [
        'code',
        'name',
        'province_code',
        'status',
        'archived',
        'created_by',
        'updated_by'
    ];

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_code', 'code');
    }

    public function barangays()
    {
        return $this->hasMany(Barangays::class, 'city_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('archived', 0);
    }
}

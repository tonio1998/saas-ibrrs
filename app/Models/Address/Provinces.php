<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provinces extends Model
{
    use SoftDeletes;

    protected $table = 'loc_provinces';

    protected $fillable = [
        'code',
        'name',
        'region_code',
        'status',
        'archived',
        'created_by',
        'updated_by'
    ];

    public function region()
    {
        return $this->belongsTo(Regions::class, 'region_code', 'code');
    }

    public function cities()
    {
        return $this->hasMany(Cities::class, 'province_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('archived', 0);
    }
}

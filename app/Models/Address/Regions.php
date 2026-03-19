<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regions extends Model
{
    use SoftDeletes;

    protected $table = 'loc_regions';

    protected $fillable = [
        'code',
        'name',
        'status',
        'archived',
        'created_by',
        'updated_by'
    ];

    public function provinces()
    {
        return $this->hasMany(Provinces::class, 'region_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('archived', 0);
    }
}

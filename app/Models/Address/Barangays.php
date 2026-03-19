<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barangays extends Model
{
    use SoftDeletes;

    protected $table = 'loc_barangays';

    protected $fillable = [
        'code',
        'name',
        'city_code',
        'status',
        'archived',
        'created_by',
        'updated_by'
    ];

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('archived', 0);
    }
}

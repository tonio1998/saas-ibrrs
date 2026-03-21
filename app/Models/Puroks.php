<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Puroks extends Model
{
    use SoftDeletes;

    protected $table = 'puroks';

    protected $fillable = [
        'PurokNo',
        'PurokName',
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

    public function households()
    {
        return $this->hasMany(Households::class, 'purok_id', 'PurokNo');
    }

}

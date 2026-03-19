<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificatesType extends Model
{
    use SoftDeletes;

    protected $table = 'certificate_types';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'template',
        'fee',
        'created_by',
        'updated_by',
        'status',
        'archived',
    ];

    protected $casts = [
        'fee'         => 'float',
        'archived'    => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function certificates()
    {
        return $this->hasMany(Certificates::class, 'certificate_type_id');
    }
}

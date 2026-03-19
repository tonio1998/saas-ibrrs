<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificates extends Model
{
    use SoftDeletes;

    protected $table = 'certificates';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'request_id',
        'issued_by',
        'control_no',
        'Fee',
        'issued_at',
        'Remark',
        'created_by',
        'updated_by',
        'status',
        'archived',
    ];

    protected $casts = [
        'issued_at'   => 'datetime',
        'archived'    => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $dates = [
        'issued_at',
        'deleted_at',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function type()
    {
        return $this->belongsTo(CertificatesType::class, 'certificate_type_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

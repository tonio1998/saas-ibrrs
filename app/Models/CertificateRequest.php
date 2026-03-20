<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateRequest extends Model
{
    use SoftDeletes;

    protected $table = 'certificate_requests';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'ControlNo',
        'resident_id',
        'certificate_type_id',
        'business_id',
        'purpose',
        'remark',
        'requested_at',
        'approved_at',
        'released_at',
        'created_by',
        'updated_by',
        'status',
        'archived'
    ];

    protected $casts = [
        'resident_id' => 'integer',
        'certificate_type_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'archived' => 'boolean',

        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'released_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'active',
        'archived' => false
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->ControlNo)) {
                do {
                    $model->ControlNo =
                        strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' .
                        strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' .
                        strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)) . '-' .
                        strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
                } while (self::where('ControlNo', $model->ControlNo)->exists());
            }
        });
    }

    public function certificateRecord()
    {
        return $this->hasOne(Certificates::class, 'request_id');
    }

    public function business()
    {
        return $this->belongsTo(BusinessInformation::class, 'business_id');
    }

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function certificateType()
    {
        return $this->belongsTo(CertificatesType::class, 'certificate_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('archived', 0);
    }

    public function scopeArchived($query)
    {
        return $query->where('archived', 1);
    }
}

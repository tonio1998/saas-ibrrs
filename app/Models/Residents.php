<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class Residents extends Model implements AuditableContract
{
    use SoftDeletes;
    use Auditable;

    protected $table = 'residents';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'household_id',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'gender',
        'BirthDate',
        'CivilStatus',
        'Occupation',
        'is_head',
        'is_voter',
        'created_by',
        'updated_by',
        'status',
        'archived',
    ];

    protected $casts = [
        'BirthDate'   => 'date',
        'is_head'     => 'boolean',
        'is_voter'    => 'boolean',
        'archived'    => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $dates = [
        'BirthDate',
        'deleted_at',
    ];

    public function getFullNameAttribute()
    {
        return collect([
            $this->FirstName,
            $this->MiddleName,
            $this->LastName,
            $this->Suffix
        ])->filter()->implode(' ');
    }

    public function info()
    {
        return $this->hasOne(ResidentInfo::class, 'resident_id')
            ->where('archived', 0)
            ->whereNull('deleted_at');
    }

    public function businesses()
    {
        return $this->hasMany(BusinessInformation::class, 'resident_id')
            ->where('archived', 0)
            ->whereNull('deleted_at');
    }

    public function activeBusinesses()
    {
        return $this->hasMany(BusinessInformation::class, 'resident_id')
            ->where('status', 'active')
            ->where('archived', 0)
            ->whereNull('deleted_at');
    }

    public function household()
    {
        return $this->belongsTo(Households::class, 'household_id');
    }
    public function householdHead()
    {
        return $this->hasOne(Households::class, 'head_id');
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

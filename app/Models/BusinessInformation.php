<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInformation extends Model
{
    protected $table = 'business_information';

    protected $fillable = [
        'resident_id',
        'business_name',
        'operator_id',
        'unit',
        'street',
        'purok',
        'barangay',
        'city',
        'province',
        'region',
        'zip',
        'status',
        'archived',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function getFullAddressAttribute()
    {
        return collect([
            $this->unit,
            $this->street,
            $this->purok ? 'Purok '.$this->purok : null,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip,
        ])->filter()->implode(', ');
    }
}

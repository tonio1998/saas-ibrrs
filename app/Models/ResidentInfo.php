<?php

namespace App\Models;

use App\Models\Address\Barangays;
use App\Models\Address\Cities;
use App\Models\Address\Provinces;
use App\Models\Address\Regions;
use Illuminate\Database\Eloquent\Model;

class ResidentInfo extends Model
{
    protected $table = 'resident_info';

    protected $fillable = [
        'resident_id',
        'unit',
        'street',
        'purok',
        'barangay',
        'city',
        'province',
        'region',
        'zip',
        'full_address',
        'status',
        'archived',
    ];

    public function resident()
    {
        return $this->belongsTo(Residents::class, 'resident_id');
    }

    public function barangayRel()
    {
        return $this->belongsTo(Barangays::class, 'barangay', 'code');
    }

    public function cityRel()
    {
        return $this->belongsTo(Cities::class, 'city', 'code');
    }

    public function provinceRel()
    {
        return $this->belongsTo(Provinces::class, 'province', 'code');
    }

    public function regionRel()
    {
        return $this->belongsTo(Regions::class, 'region', 'code');
    }

    private function formatText($value)
    {
        return $value ? ucwords(strtolower($value)) : null;
    }

    public function getFullAddressAttribute()
    {
        return collect([
            $this->formatText($this->unit),
            $this->formatText($this->street),
            $this->purok ? 'Purok '.$this->formatText($this->purok) : null,
            $this->formatText(optional($this->barangayRel)->name ?? $this->barangay),
            $this->formatText(optional($this->cityRel)->name ?? $this->city),
            $this->formatText(optional($this->provinceRel)->name ?? $this->province),
            $this->formatText(optional($this->regionRel)->name ?? $this->region),
            $this->zip,
        ])->filter()->implode(', ');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blotter extends Model
{
    use SoftDeletes;

    protected $table = 'blotters';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'complainant_id',
        'respondent_id',
        'incident_date',
        'location',
        'description',
        'Remark',
        'created_by',
        'updated_by',
        'status',
        'archived',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'archived'      => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected $dates = [
        'incident_date',
        'deleted_at',
    ];

    public function complainant()
    {
        return $this->belongsTo(Residents::class, 'complainant_id');
    }

    public function respondent()
    {
        return $this->belongsTo(Residents::class, 'respondent_id');
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

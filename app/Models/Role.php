<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;
class Role extends SpatieRole
{
    use HasFactory;

    public $table = 'roles';
    public $primaryKey = 'id';
    public $filterable = [
        'id',
        'name',
        'details',
        'guard_name',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public $table = 'permissions';
    public $primaryKey = 'id';

    protected $fillable = [
        'name',
        'details',
        'guard_name',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }

}

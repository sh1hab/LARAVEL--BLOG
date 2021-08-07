<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [];

    protected $table = 'roles';
    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
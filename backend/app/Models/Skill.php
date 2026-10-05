<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $primaryKey = 'skill_id';

    protected $fillable = [
        'skill_name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_skills',
            'skill_id',
            'user_id'
        )->withTimestamps();
    }
}
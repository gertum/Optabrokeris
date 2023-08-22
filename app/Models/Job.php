<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'user_id',
        'solver_id',
        'status',
        'result',
        'type',
        'name'
    ];

    public function scopeUser($builder, $userId) {
        return $builder->where('user_id', '=', $userId);
    }

    public function getType() {
        return $this->getAttribute('type');
    }

    public function getResult() {
        return $this->getAttribute('result');
    }

    public function getData() {
        return $this->getAttribute('data');
    }
}

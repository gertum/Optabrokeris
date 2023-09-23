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
        'name',
        'flag_uploaded',
        'flag_solving',
        'flag_solved',
    ];

    public function scopeUser($builder, $userId)
    {
        return $builder->where('user_id', '=', $userId);
    }

    public function getType()
    {
        return $this->getAttribute('type');
    }

    public function getResult()
    {
        return $this->getAttribute('result');
    }

    public function getData()
    {
        return $this->getAttribute('data');
    }

    public function getFlagSolving()
    {
        return $this->getAttribute('flag_solving');
    }

    public function getFlagSolved()
    {
        return $this->getAttribute('flag_solved');
    }

    public function getFlagUploaded()
    {
        return $this->getAttribute('flag_uploaded');
    }

    public function setFlagSolving(bool $flag)
    {
        return $this->setAttribute('flag_solving', $flag);
    }

    public function setFlagSolved(bool $flag)
    {
        return $this->setAttribute('flag_solved', $flag);
    }

    public function setFlagUploaded(bool $flag)
    {
        return $this->setAttribute('flag_uploaded', $flag);
    }

}

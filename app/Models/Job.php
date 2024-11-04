<?php

namespace App\Models;

use App\Data\Profile;
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

    protected $hidden = ['original_file_content'];

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

    public function setData($data): self
    {
        return $this->setAttribute('data', $data);
    }

    public function setResult($result): self
    {
        return $this->setAttribute('result', $result);
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

    public function setOriginalFileContent(string $content)
    {
        return $this->setAttribute('original_file_content', base64_encode($content));
    }

    public function getOriginalFileContent(): string
    {
        return base64_decode($this->getAttribute('original_file_content'));
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getProfile(): string
    {
        return $this->getAttribute('profile');
    }

    public function setProfile(string $profile): string
    {
        return $this->setAttribute('profile', $profile);
    }

    public function getProfileObj(): Profile {
        return new Profile($this->getProfile());
    }

    public function setProfileObj(Profile $profile) {
        $this->setProfile(json_encode($profile));
    }
}

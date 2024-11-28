<?php

namespace App\Models;

use App\Domain\Roster\Profile;
use App\Domain\Roster\Schedule;
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

    protected $appends = [
        'result_obj',
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

    public function getProfile(): ?string
    {
        return $this->getAttribute('profile');
    }

    public function setProfile(?string $profile): string
    {
        return $this->setAttribute('profile', $profile);
    }

    public function getProfileObj(): Profile
    {
        $data = json_decode($this->getProfile(), true);
        return new Profile($data);
    }

    public function setProfileObj(Profile $profile)
    {
        $this->setProfile(json_encode($profile));
    }

    public function getSolverId() {
        return $this->getAttribute('solver_id');
    }

    public function setSolverId($solverId) : self {
        $this->setAttribute('solver_id', $solverId);
        return $this;
    }

    public function getUserId() {
        return $this->getAttribute('user_id');
    }

    public function setUserId($userId) : self {
        $this->setAttribute('user_id', $userId);

        return $this;
    }
    public function getErrorMessage() {
        return $this->getAttribute('error_message');
    }

    public function setErrorMessage($message) : self {
        $this->setAttribute('error_message', $message);

        return $this;
    }

    /**
     * Laravel magic attribute using  $appends ( look above at the top of this class body )
     * @return Schedule|mixed we leave possibility to solver other problems.
     */
    public function getResultObjAttribute() {
        // TODO use constant instead of 'roster'
        if ( $this->getType() == 'roster') {
            $resultArray = json_decode( $this->getResult(), true );
            $schedule = new Schedule($resultArray);

            return $schedule;
        }

        // other tasks transformations, may be schedules, may be transport assignments, may be auditories assignments etc.
        return null;
    }

    public function getStatus() {
        return $this->getAttribute('status');
    }

    public function setStatus(string $status) : self {
        $this->setAttribute('status', $status);

        return $this;
    }
}

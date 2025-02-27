<?php

namespace App\Models;

use App\Domain\Roster\Profile;
use App\Domain\Roster\Schedule;
use GuzzleHttp\Utils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    public const TYPE_ROSTER = 'roster';
    public const TYPE_SCHOOL = 'school';

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

    public function setFlagSolving(bool $flag): self
    {
        return $this->setAttribute('flag_solving', $flag);
    }

    public function setFlagSolved(bool $flag): self
    {
        return $this->setAttribute('flag_solved', $flag);
    }

    public function setFlagUploaded(bool $flag): self
    {
        return $this->setAttribute('flag_uploaded', $flag);
    }

    public function setOriginalFileContent(string $content): self
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

    public function setProfileObj(Profile $profile): self
    {
        $this->setProfile(json_encode($profile));

        return $this;
    }

    public function getSolverId()
    {
        return $this->getAttribute('solver_id');
    }

    public function setSolverId($solverId): self
    {
        $this->setAttribute('solver_id', $solverId);
        return $this;
    }

    public function getUserId()
    {
        return $this->getAttribute('user_id');
    }

    public function setUserId($userId): self
    {
        $this->setAttribute('user_id', $userId);

        return $this;
    }

    public function getErrorMessage()
    {
        return $this->getAttribute('error_message');
    }

    public function setErrorMessage($message): self
    {
        $this->setAttribute('error_message', $message);

        return $this;
    }

    /**
     * Laravel magic attribute using  $appends ( look above at the top of this class body )
     * @return Schedule|mixed we leave possibility to solver other problems.
     */
    public function getResultObjAttribute()
    {
        if ($this->getType() ==  self::TYPE_ROSTER) {
            $resultArray = json_decode($this->getResult(), true);
            $schedule = new Schedule($resultArray);

            // clear json_last_error()
            if ( json_last_error() != 0 ) {
                json_decode('{}');
            }

            return $schedule;
        }

        // other tasks transformations, may be schedules, may be transport assignments, may be auditories assignments etc.
        return null;
    }

    public function getStatus()
    {
        return $this->getAttribute('status');
    }

    public function setStatus(string $status): self
    {
        $this->setAttribute('status', $status);

        return $this;
    }

    public function handleResultFromSolver(string $result, string $errorMessage): void
    {
        if ( $errorMessage == '' ) {
            // hacky
            if ( str_starts_with( $this->getErrorMessage(), '-' )) {
                $this->setErrorMessage($errorMessage);
            }
            elseif ($this->getErrorMessage() != '' )  {
                $this->setErrorMessage( '-'.$this->getErrorMessage());
            }
        }
        else {
            $this->setErrorMessage($errorMessage);
        }

        $this->setResult($result);

        $resultDataArray = json_decode($result, true);
        $status = $resultDataArray['solverStatus'] ?? '';

        if ($errorMessage != '' || !is_array($resultDataArray)) {
            $this->setFlagSolving(false);
        }


        $flagSolved = false;
        if ($this->getFlagSolving() && $status == 'NOT_SOLVING') {
            $flagSolved = true;
        }
        $this->setFlagSolved($flagSolved);
        $this->setStatus($status);
    }

    public function setId(int $id) : self {
        $this->setAttribute('id', $id);

        return $this;
    }
}

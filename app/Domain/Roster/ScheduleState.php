<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class ScheduleState extends DataTransferObject
{
    public $tenantId;
    public $publishLength;
    public $draftLength;
    public $firstDraftDate;
    public $lastHistoricDate;
    public $firstUnplannedDate;

    /**
     * @param mixed $tenantId
     * @return ScheduleState
     */
    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;
        return $this;
    }

    /**
     * @param mixed $publishLength
     * @return ScheduleState
     */
    public function setPublishLength($publishLength)
    {
        $this->publishLength = $publishLength;
        return $this;
    }

    /**
     * @param mixed $draftLength
     * @return ScheduleState
     */
    public function setDraftLength($draftLength)
    {
        $this->draftLength = $draftLength;
        return $this;
    }

    /**
     * @param mixed $firstDraftDate
     * @return ScheduleState
     */
    public function setFirstDraftDate($firstDraftDate)
    {
        $this->firstDraftDate = $firstDraftDate;
        return $this;
    }

    /**
     * @param mixed $lastHistoricDate
     * @return ScheduleState
     */
    public function setLastHistoricDate($lastHistoricDate)
    {
        $this->lastHistoricDate = $lastHistoricDate;
        return $this;
    }

    /**
     * @param mixed $firstUnplannedDate
     * @return ScheduleState
     */
    public function setFirstUnplannedDate($firstUnplannedDate)
    {
        $this->firstUnplannedDate = $firstUnplannedDate;
        return $this;
    }
}
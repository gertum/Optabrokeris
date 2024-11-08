<?php

namespace App\Models;

use App\Domain\Roster\SubjectDataInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model implements SubjectDataInterface
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position_amount',
        'hours_in_month',
    ];

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function setName(?string $name): self
    {
        return $this->setAttribute('name', $name);
    }

    /**
     * Etatas : 1, 0.5, 0.25, 1.5
     * @return float
     */
    public function getPositionAmount(): float
    {
        return $this->getAttribute('position_amount');
    }

    public function setPositionAmount(?float $positionAmount): self
    {
        return $this->setAttribute('position_amount', $positionAmount);
    }

    public function getHoursInMonth(): float
    {
        return $this->getAttribute('hours_in_month');
    }

    public function setHoursInMonth(?float $hoursInMonth): self
    {
        return $this->setAttribute('hours_in_month', $hoursInMonth);
    }

    public function setHoursInDay(?float $hoursInDay): self
    {
        $this->setAttribute('hours_in_day', $hoursInDay);

        return $this;
    }

    public function getHoursInDay(): ?float
    {
        return $this->getAttribute('hours_in_day');
    }

}
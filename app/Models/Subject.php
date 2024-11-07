<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function setName(string $name): self
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

    public function setPositionAmount(float $amount): self
    {
        return $this->setAttribute('position_amount', $amount);
    }

    public function getHoursInMonth(): float {
        return $this->getAttribute('hours_in_month');
    }

    public function setHoursInMonth(float $hours): self {
        return $this->setAttribute('hours_in_month', $hours);
    }
}
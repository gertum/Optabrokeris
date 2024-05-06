<?php

namespace App\Domain\Roster\Hospital;

use Spatie\DataTransferObject\DataTransferObject;

class Cell extends DataTransferObject
{
    public $type;
    public $name;
    public $value;
    public $href;
    public $f;
    public $format;
    public $s;
    public $css;
    public $r;
    public $hidden;
    public $width;
    public $height;
    public $comment;


}
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


    public static function parseCss(string $css): array
    {
        $parts = preg_split("/;/", $css);

        $result = [];

        foreach ($parts as $part) {
            $keyValues = preg_split("/:/", $part);
            if (!$keyValues) {
                continue;
            }
            if (count($keyValues) != 2) {
                continue;
            }

            list($key, $value) = $keyValues;

            $result[trim($key)] = trim($value);
        }

        return $result;
    }


    /**
     * In memory cache.
     * @var array|null
     */
    private ?array $parsedCss = null;

    public function getParsedCss() : array
    {
        if ($this->parsedCss == null) {
            $this->parsedCss = self::parseCss($this->css);
        }

        return $this->parsedCss;
    }

    public function getBackgroundColor () : string {
        return $this->getParsedCss()['background-color'];
    }
}
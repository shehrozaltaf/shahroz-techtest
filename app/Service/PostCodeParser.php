<?php

declare(strict_types=1);

namespace App\Service;

final class PostCodeParser
{
    private string $postcode;

    public function __construct(string $postcode)
    {
        $this->postcode = $postcode;
    }

    public function getArea(): string
    {
        if (ctype_digit(substr($this->postcode, 1, 1))) {
            return substr($this->postcode, 0, 1);
        } else {
            return substr($this->postcode, 0, 2);
        }
    }
}

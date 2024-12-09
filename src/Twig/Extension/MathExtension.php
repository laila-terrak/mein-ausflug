<?php namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MathExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('floor', [$this, 'floor']),
        ];
    }

    public function floor($value)
    {
        return floor($value);
    }
}

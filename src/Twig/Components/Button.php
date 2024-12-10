<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
    public string $icon="";
    public string $text;
    public string $href="";
    public string $class="";
}
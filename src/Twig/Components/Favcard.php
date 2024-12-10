<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Favcard
{
    public string $src="";
    public string $alt="";
    public string $title="";
    public string $href="";
    public string $class="";
}
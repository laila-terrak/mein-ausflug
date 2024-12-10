<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Input {
       public string $placeholder;
       public string $type;
       public string $name;
}
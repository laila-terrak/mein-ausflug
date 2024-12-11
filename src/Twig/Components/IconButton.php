<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class IconButton
{
    public string $icon = "";
    public ?string $itemId = null;
    public ?string $itemType = null;
    public bool $isFavorite = false;
}
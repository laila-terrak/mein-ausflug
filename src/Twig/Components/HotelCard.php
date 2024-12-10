<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class HotelCard {
  public string $title="";
  public string $description="";
  public string $image="";
  public string $rooms="";
  public string $rating="";
  public string $href="";
}


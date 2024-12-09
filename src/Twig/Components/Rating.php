<?php
namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Rating
{
  public string $reviews="";
  public string $rating="";
    
}
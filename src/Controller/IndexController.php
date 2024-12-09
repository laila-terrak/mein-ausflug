<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController {

  #[Route('/')]
  public function index(Connection $connection): Response {
    // Fetch all destinations from the database
    $destinations = $connection->fetchAllAssociative('SELECT * FROM destinations');
    
    // Pass the destinations data to the Twig template
    return $this->render('views/index.html.twig', [
        'destinations' => $destinations
    ]);
  }
}

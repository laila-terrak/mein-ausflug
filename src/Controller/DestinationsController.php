<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DestinationsController extends AbstractController {

    #[Route('/destinations')]
    public function index(Connection $connection): Response {
        // Fetch all destinations with their first image
        $destinations = $connection->fetchAllAssociative('
            SELECT d.*,
                   i.url as image_url
            FROM destinations d
            LEFT JOIN images i ON i.destination_id = d.id
            GROUP BY d.id
        ');
        
        return $this->render('views/destinations.html.twig', [
            'destinations' => $destinations
        ]);
    }
} 
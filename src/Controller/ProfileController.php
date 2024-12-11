<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController {

    #[Route('/profile')]
    public function index(Request $request, Connection $connection): Response {
        $session = $request->getSession();
        $user = $session->get('user');
        
        if (!$user) {
            return $this->redirectToRoute('app_signin');
        }
        
        // Fetch favorite hotels
        $favoriteHotels = $connection->fetchAllAssociative('
            SELECT h.*, i.url as image_url, d.name as destination_name
            FROM favorites f
            JOIN hotels h ON f.hotel_id = h.id
            JOIN destinations d ON h.destination_id = d.id
            LEFT JOIN images i ON i.hotel_id = h.id
            WHERE f.user_id = ?
            GROUP BY h.id
        ', [$user['id']]);
        
        // Fetch favorite destinations
        $favoriteDestinations = $connection->fetchAllAssociative('
            SELECT d.*, i.url as image_url
            FROM favorites f
            JOIN destinations d ON f.destination_id = d.id
            LEFT JOIN images i ON i.destination_id = d.id
            WHERE f.user_id = ?
            GROUP BY d.id
        ', [$user['id']]);
        
        return $this->render('views/profile.html.twig', [
            'favoriteHotels' => $favoriteHotels,
            'favoriteDestinations' => $favoriteDestinations
        ]);
    }
}
<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HotelsController extends AbstractController {

    #[Route('/hotels')]
    public function index(Connection $connection): Response {
        // Fetch all hotels from the database with their first image
        $hotels = $connection->fetchAllAssociative('
            SELECT h.*, 
                   d.name as destination_name,
                   i.url as image_url
            FROM hotels h
            JOIN destinations d ON h.destination_id = d.id
            LEFT JOIN (
                SELECT hotel_id, url
                FROM images
                GROUP BY hotel_id
            ) i ON i.hotel_id = h.id
        ');
        
        return $this->render('views/hotels.html.twig', [
            'hotels' => $hotels
        ]);
    }

    #[Route('/hotel/{id}')]
    public function show(Request $request, Connection $connection, string $id): Response {
      $session = $request->getSession();
      $user = $session->get('user');
      
      // Check if hotel is favorited
      $isFavorited = false;
      if ($user) {
          $favorite = $connection->fetchOne(
              'SELECT id FROM favorites WHERE user_id = ? AND hotel_id = ?',
              [$user['id'], $id]
          );
          $isFavorited = (bool)$favorite;
      }

        // Fetch hotel with all related data
        $results = $connection->fetchAllAssociative('
            SELECT h.*, 
                   d.name as destination_name,
                   d.description as destination_description,
                   i.url as image_url,
                   i.id as image_id
            FROM hotels h
            JOIN destinations d ON h.destination_id = d.id
            LEFT JOIN images i ON i.hotel_id = h.id
            WHERE h.id = ?
        ', [$id]);

        if (!$results) {
            throw $this->createNotFoundException('Hotel not found');
        }

        // Process results
        $hotel = [
            'id' => $results[0]['id'],
            'name' => $results[0]['name'],
            'description' => $results[0]['description'],
            'text' => $results[0]['text'],
            'available_rooms' => $results[0]['available_rooms'],
            'room_number' => $results[0]['room_number'],
            'images' => [],
            'destination_name' => $results[0]['destination_name'],
            'destination_description' => $results[0]['destination_description']
        ];

        // Collect all images
        foreach ($results as $row) {
            if ($row['image_url']) {
                $hotel['images'][] = $row['image_url'];
            }
        }

        return $this->render('views/hotel.html.twig', [
            'hotel' => $hotel,
            'isFavorited' => $isFavorited
        ]);
    }
} 
<?php
// src/Controller/DestinationController.php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DestinationController extends AbstractController
{
    #[Route('/destination/{id}', name: 'destination_show')]
    public function destination(Request $request, Connection $connection, string $id): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');
        
        // Check if destination is favorited
        $isFavorited = false;
        if ($user) {
            $favorite = $connection->fetchOne(
                'SELECT id FROM favorites WHERE user_id = ? AND destination_id = ?',
                [$user['id'], $id]
            );
            $isFavorited = (bool)$favorite;
        }
        
        $query = '
            SELECT 
                d.id AS destination_id, 
                d.name AS destination_name, 
                d.description AS destination_description, 
                d.text AS destination_text,
                h.id AS hotel_id, 
                h.name AS hotel_name, 
                h.description AS hotel_description, 
                h.text AS hotel_text, 
                h.available_rooms,
                h.room_number,
                i.id AS image_id,
                i.url AS image_url,
                i.hotel_id AS hotel_image_id, 
                i.destination_id AS destination_image_id
            FROM destinations d
            LEFT JOIN hotels h ON h.destination_id = d.id
            LEFT JOIN images i ON (i.destination_id = d.id OR i.hotel_id = h.id)
            WHERE d.id = :destination_id
            ORDER BY h.id, i.id
        ';

        // Execute the query and fetch the results
        $results = $connection->fetchAllAssociative($query, ['destination_id' => $id]);

        // Group the data by destination and hotels
        $destination = null;
        $hotels = [];
        foreach ($results as $row) {
            // Check if it's the first row for the destination
            if (!$destination) {
                $destination = [
                    'id' => $row['destination_id'],
                    'name' => $row['destination_name'],
                    'description' => $row['destination_description'],
                    'text' => $row['destination_text'],
                ];
            }

            // Collect hotels and images
            if ($row['hotel_id']) {
                $hotelId = $row['hotel_id'];
                if (!isset($hotels[$hotelId])) {
                    $hotels[$hotelId] = [
                        'id' => $row['hotel_id'],
                        'name' => $row['hotel_name'],
                        'description' => $row['hotel_description'],
                        'text' => $row['hotel_text'],
                        'available_rooms' => $row['available_rooms'],
                        'room_number' => $row['room_number'],
                        'images' => []
                    ];
                }

                // Add images only if they belong to this hotel
                if ($row['image_url'] && $row['hotel_image_id'] == $hotelId) {
                    $hotels[$hotelId]['images'][] = $row['image_url'];
                }
            }

            // Add destination-level images
            if ($row['image_url'] && $row['destination_image_id'] == $destination['id']) {
                if (!isset($destination['images'])) {
                    $destination['images'] = [];
                }
                if (!in_array($row['image_url'], $destination['images'])) {
                    $destination['images'][] = $row['image_url'];
                }
            }
        }

        // Pass the data to the Twig template
        return $this->render('views/destination.html.twig', [
            'destination' => $destination,
            'hotels' => $hotels,
            'isFavorited' => $isFavorited
        ]);
    }
}
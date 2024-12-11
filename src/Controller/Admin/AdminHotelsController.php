<?php
namespace App\Controller\Admin;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminHotelsController extends AdminBaseController {
  #[Route('/admin/hotels', name: 'app_admin_hotels')]
  public function hotels(Request $request, Connection $connection): Response {
      $this->checkAdminAccess($request);
      
      $hotels = $connection->fetchAllAssociative('
          SELECT h.*, d.name as destination_name, GROUP_CONCAT(i.url) as images
          FROM hotels h
          JOIN destinations d ON h.destination_id = d.id
          LEFT JOIN images i ON i.hotel_id = h.id
          GROUP BY h.id
      ');
      
      return $this->render('admin/hotels.html.twig', [
          'hotels' => $hotels
      ]);
  }

  #[Route('/admin/hotels/add', name: 'app_admin_hotels_add', methods: ['GET'])]
  public function addHotelForm(Request $request, Connection $connection): Response {
      $this->checkAdminAccess($request);
      
      $destinations = $connection->fetchAllAssociative('SELECT id, name FROM destinations');
      
      return $this->render('admin/hotel-form.html.twig', [
          'destinations' => $destinations
      ]);
  }

  #[Route('/admin/hotels/add', methods: ['POST'])]
  public function addHotel(Request $request, Connection $connection): Response {
      $this->checkAdminAccess($request);
      
      $name = $request->request->get('name');
      $description = $request->request->get('description');
      $text = $request->request->get('text');
      $destinationId = $request->request->get('destination_id');
      $availableRooms = $request->request->get('available_rooms');
      $roomNumber = $request->request->get('room_number');
      $imageUrls = $request->request->all('image_urls');

      try {
          $connection->beginTransaction();
          
          // Generate UUID for the new hotel
          $hotelId = $connection->fetchOne('SELECT UUID()');
          
          // Insert hotel
          $connection->executeStatement(
              'INSERT INTO hotels (id, name, description, text, destination_id, available_rooms, room_number) 
               VALUES (?, ?, ?, ?, ?, ?, ?)',
              [$hotelId, $name, $description, $text, $destinationId, $availableRooms, $roomNumber]
          );
          
          // Insert images
          foreach ($imageUrls as $imageUrl) {
              if (!empty($imageUrl)) {
                  $connection->executeStatement(
                      'INSERT INTO images (url, hotel_id) VALUES (?, ?)',
                      [$imageUrl, $hotelId]
                  );
              }
          }
          
          $connection->commit();
          return $this->redirectToRoute('app_admin_hotels');
      } catch (\Exception $e) {
          $connection->rollBack();
          return $this->redirectToRoute('app_admin_hotels_add', [
              'error' => 'Failed to add hotel: ' . $e->getMessage()
          ]);
      }
  }

  #[Route('/admin/hotels/{id}/edit', name: 'app_admin_hotels_edit', methods: ['GET'])]
  public function editHotelForm(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      $hotel = $connection->fetchAssociative('
          SELECT h.*, GROUP_CONCAT(i.url) as images
          FROM hotels h
          LEFT JOIN images i ON i.hotel_id = h.id
          WHERE h.id = ?
          GROUP BY h.id',
          [$id]
      );
      
      $destinations = $connection->fetchAllAssociative('SELECT id, name FROM destinations');
      
      return $this->render('admin/hotel-form.html.twig', [
          'hotel' => $hotel,
          'destinations' => $destinations
      ]);
  }

  #[Route('/admin/hotels/{id}/edit', methods: ['POST'])]
  public function editHotel(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      $name = $request->request->get('name');
      $description = $request->request->get('description');
      $text = $request->request->get('text');
      $destinationId = $request->request->get('destination_id');
      $availableRooms = $request->request->get('available_rooms');
      $roomNumber = $request->request->get('room_number');
      $imageUrls = $request->request->all('image_urls');
      
      try {
          $connection->beginTransaction();
          
          $connection->executeStatement(
              'UPDATE hotels SET name = ?, description = ?, text = ?, destination_id = ?, 
               available_rooms = ?, room_number = ? WHERE id = ?',
              [$name, $description, $text, $destinationId, $availableRooms, $roomNumber, $id]
          );
          
          // Update images
          $connection->executeStatement('DELETE FROM images WHERE hotel_id = ?', [$id]);
          
          foreach ($imageUrls as $imageUrl) {
              if (!empty($imageUrl)) {
                  $connection->executeStatement(
                      'INSERT INTO images (url, hotel_id) VALUES (?, ?)',
                      [$imageUrl, $id]
                  );
              }
          }
          
          $connection->commit();
          return $this->redirectToRoute('app_admin_hotels');
      } catch (\Exception $e) {
          $connection->rollBack();
          return $this->redirectToRoute('app_admin_hotels_edit', [
              'id' => $id,
              'error' => 'Failed to update hotel: ' . $e->getMessage()
          ]);
      }
  }

  #[Route('/admin/hotels/{id}/delete', methods: ['POST'])]
  public function deleteHotel(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      try {
          $connection->beginTransaction();
          $connection->executeStatement('DELETE FROM images WHERE hotel_id = ?', [$id]);
          $connection->executeStatement('DELETE FROM hotels WHERE id = ?', [$id]);
          $connection->commit();
          
          return $this->redirectToRoute('app_admin_hotels');
      } catch (\Exception $e) {
          $connection->rollBack();
          return $this->redirectToRoute('app_admin_hotels', [
              'error' => 'Failed to delete hotel: ' . $e->getMessage()
          ]);
      }
  }
} 
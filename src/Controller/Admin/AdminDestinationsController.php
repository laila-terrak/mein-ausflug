<?php
namespace App\Controller\Admin;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminDestinationsController extends AdminBaseController {
  #[Route('/admin/destinations', name: 'app_admin_destinations')]
  public function destinations(Request $request, Connection $connection): Response {
      $this->checkAdminAccess($request);
      
      $destinations = $connection->fetchAllAssociative('
          SELECT d.*, GROUP_CONCAT(i.url) as images
          FROM destinations d
          LEFT JOIN images i ON i.destination_id = d.id
          GROUP BY d.id
      ');
      
      return $this->render('admin/destinations.html.twig', [
          'destinations' => $destinations
      ]);
  }

  #[Route('/admin/destinations/add', name: 'app_admin_destinations_add', methods: ['GET'])]
  public function addDestinationForm(Request $request): Response {
      $this->checkAdminAccess($request);
      return $this->render('admin/destination-form.html.twig');
  }

  #[Route('/admin/destinations/add', methods: ['POST'])]
  public function addDestination(Request $request, Connection $connection): Response {
      $this->checkAdminAccess($request);
      
      $name = $request->request->get('name');
      $description = $request->request->get('description');
      $text = $request->request->get('text');
      $imageUrls = $request->request->all('image_urls');

      try {
          $connection->beginTransaction();
          
          // Generate UUID for the new destination
          $destinationId = $connection->fetchOne('SELECT UUID()');
          
          // Insert destination with the generated UUID
          $connection->executeStatement(
              'INSERT INTO destinations (id, name, description, text) VALUES (?, ?, ?, ?)',
              [$destinationId, $name, $description, $text]
          );
          
          // Insert images if provided
          foreach ($imageUrls as $imageUrl) {
              if (!empty($imageUrl)) {
                  $connection->executeStatement(
                      'INSERT INTO images (url, destination_id) VALUES (?, ?)',
                      [$imageUrl, $destinationId]
                  );
              }
          }
          
          $connection->commit();
          return $this->redirectToRoute('app_admin_destinations');
      } catch (\Exception $e) {
          $connection->rollBack();
          return $this->redirectToRoute('app_admin_destinations_add', [
              'error' => 'Failed to add destination: ' . $e->getMessage()
          ]);
      }
  }

  #[Route('/admin/destinations/{id}/edit', name: 'app_admin_destinations_edit', methods: ['GET'])]
  public function editDestinationForm(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      $destination = $connection->fetchAssociative(
          'SELECT d.*, GROUP_CONCAT(i.url) as images
          FROM destinations d
          LEFT JOIN images i ON i.destination_id = d.id
          WHERE d.id = ?
          GROUP BY d.id',
          [$id]
      );
      
      return $this->render('admin/destination-form.html.twig', [
          'destination' => $destination
      ]);
  }

  #[Route('/admin/destinations/{id}/edit', methods: ['POST'])]
  public function editDestination(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      $name = $request->request->get('name');
      $description = $request->request->get('description');
      $text = $request->request->get('text');
      $imageUrls = $request->request->all('image_urls');
      
      try {
          $connection->beginTransaction();
          
          // Update destination
          $connection->executeStatement(
              'UPDATE destinations SET name = ?, description = ?, text = ? WHERE id = ?',
              [$name, $description, $text, $id]
          );
          
          // Delete all existing images
          $connection->executeStatement(
              'DELETE FROM images WHERE destination_id = ?',
              [$id]
          );
          
          // Insert new images
          foreach ($imageUrls as $imageUrl) {
              if (!empty($imageUrl)) {
                  $connection->executeStatement(
                      'INSERT INTO images (url, destination_id) VALUES (?, ?)',
                      [$imageUrl, $id]
                  );
              }
          }
          
          $connection->commit();
          return $this->redirectToRoute('app_admin_destinations');
      } catch (\Exception $e) {
          $connection->rollBack();
          return $this->redirectToRoute('app_admin_destinations_edit', [
              'id' => $id,
              'error' => 'Failed to update destination: ' . $e->getMessage()
          ]);
      }
  }

  #[Route('/admin/destinations/{id}/delete', methods: ['POST'])]
  public function deleteDestination(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      try {
          $connection->beginTransaction();
          
          // Delete associated images first
          $connection->executeStatement('DELETE FROM images WHERE destination_id = ?', [$id]);
          
          // Then delete the destination
          $connection->executeStatement('DELETE FROM destinations WHERE id = ?', [$id]);
          
          $connection->commit();
          return $this->redirectToRoute('app_admin_destinations');
      } catch (\Exception $e) {
          $connection->rollBack();
          return $this->redirectToRoute('app_admin_destinations', [
              'error' => 'Failed to delete destination: ' . $e->getMessage()
          ]);
      }
  }
} 
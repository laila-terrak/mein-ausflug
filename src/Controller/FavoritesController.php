<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoritesController extends AbstractController
{
    #[Route('/favorites/toggle', name: 'app_favorites_toggle', methods: ['POST'])]
    public function toggle(Request $request, Connection $connection): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');
        
        if (!$user) {
            return $this->redirectToRoute('app_signin');
        }

        $hotelId = $request->request->get('hotel_id');
        $destinationId = $request->request->get('destination_id');
        $referer = $request->headers->get('referer');

        try {
            $connection->beginTransaction();

            // Check if favorite exists
            $existingFavorite = null;
            if ($hotelId) {
                $existingFavorite = $connection->fetchAssociative(
                    'SELECT id FROM favorites WHERE user_id = ? AND hotel_id = ?',
                    [$user['id'], $hotelId]
                );
            } else {
                $existingFavorite = $connection->fetchAssociative(
                    'SELECT id FROM favorites WHERE user_id = ? AND destination_id = ?',
                    [$user['id'], $destinationId]
                );
            }

            if ($existingFavorite) {
                // Remove favorite
                $connection->executeStatement(
                    'DELETE FROM favorites WHERE id = ?',
                    [$existingFavorite['id']]
                );
            } else {
                // Add favorite
                $connection->executeStatement(
                    'INSERT INTO favorites (user_id, hotel_id, destination_id) VALUES (?, ?, ?)',
                    [$user['id'], $hotelId, $destinationId]
                );
            }

            $connection->commit();
            return $this->redirect($referer);
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
} 
<?php
namespace App\Controller\Admin;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminUsersController extends AdminBaseController {
  #[Route('/admin/users', name: 'app_admin_users')]
  public function users(Request $request, Connection $connection): Response {
      $this->checkAdminAccess($request);
      
      $users = $connection->fetchAllAssociative('
          SELECT id, email, first_name, last_name, role, created_at
          FROM users
          ORDER BY created_at DESC
      ');
      
      return $this->render('admin/users.html.twig', [
          'users' => $users
      ]);
  }

  #[Route('/admin/users/{id}/edit', name: 'app_admin_users_edit', methods: ['GET'])]
  public function editUserForm(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      $user = $connection->fetchAssociative('
          SELECT id, email, first_name, last_name, role
          FROM users
          WHERE id = ?',
          [$id]
      );
      
      return $this->render('admin/user-form.html.twig', [
          'user' => $user
      ]);
  }

  #[Route('/admin/users/{id}/edit', methods: ['POST'])]
  public function editUser(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      $firstName = $request->request->get('first_name');
      $lastName = $request->request->get('last_name');
      $email = $request->request->get('email');
      $role = $request->request->get('role');
      
      try {
          $connection->executeStatement(
              'UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ? WHERE id = ?',
              [$firstName, $lastName, $email, $role, $id]
          );
          
          return $this->redirectToRoute('app_admin_users');
      } catch (\Exception $e) {
          return $this->redirectToRoute('app_admin_users_edit', [
              'id' => $id,
              'error' => 'Failed to update user: ' . $e->getMessage()
          ]);
      }
  }

  #[Route('/admin/users/{id}/delete', methods: ['POST'])]
  public function deleteUser(Request $request, Connection $connection, string $id): Response {
      $this->checkAdminAccess($request);
      
      // Prevent self-deletion
      if ($id === $request->getSession()->get('user')['id']) {
          return $this->redirectToRoute('app_admin_users', [
              'error' => 'You cannot delete your own account'
          ]);
      }
      
      try {
          $connection->executeStatement('DELETE FROM users WHERE id = ?', [$id]);
          return $this->redirectToRoute('app_admin_users');
      } catch (\Exception $e) {
          return $this->redirectToRoute('app_admin_users', [
              'error' => 'Failed to delete user: ' . $e->getMessage()
          ]);
      }
  }
} 
<?php
namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AdminBaseController extends AbstractController {
    protected function checkAdminAccess(Request $request): void {
        $user = $request->getSession()->get('user');
        if (!$user || $user['role'] !== 'admin') {
            throw $this->createAccessDeniedException('Access denied');
        }
    }
} 
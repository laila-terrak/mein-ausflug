<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SigninController extends AbstractController {

    #[Route('/signin', name: 'app_signin', methods: ['GET'])]
    public function index(Request $request): Response {
        // If already logged in, redirect to home
        if ($request->getSession()->get('user')) {
            return $this->redirectToRoute('app_index');
        }
        return $this->render('views/signin.html.twig');
    }

    #[Route('/signin', name: 'app_signin_post', methods: ['POST'])]
    public function signin(Request $request, Connection $connection): Response {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $connection->fetchAssociative(
            'SELECT * FROM users WHERE email = ? AND password = ?',
            [$email, $password]
        );

        if ($user) {
            $session = $request->getSession();
            $session->set('user', $user);
            return $this->redirectToRoute('app_index');
        }

        // Always redirect, even on error
        $referer = $request->headers->get('referer');
        return $this->redirect($referer . '?error=Invalid email or password');
    }

    #[Route('/signout', name: 'app_signout')]
    public function signout(Request $request): Response {
        $session = $request->getSession();
        $session->remove('user');
        return $this->redirectToRoute('app_index');
    }
}
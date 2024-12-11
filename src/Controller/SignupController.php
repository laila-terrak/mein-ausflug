<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SignupController extends AbstractController {

    #[Route('/signup', name: 'app_signup', methods: ['GET'])]
    public function index(Request $request): Response {
        // If already logged in, redirect to home
        if ($request->getSession()->get('user')) {
            return $this->redirectToRoute('app_index');
        }
        return $this->render('views/signup.html.twig');
    }

    #[Route('/signup', name: 'app_signup_post', methods: ['POST'])]
    public function signup(Request $request, Connection $connection): Response {
        $firstName = $request->request->get('fname');
        $lastName = $request->request->get('lname');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $confirmPassword = $request->request->get('confirm-password');
        $phoneNumber = $request->request->get('phone-number');
        $birthDate = $request->request->get('birth-date');

        // Basic validation
        if ($password !== $confirmPassword) {
            return $this->redirect('/signup?error=Passwords do not match');
        }

        try {
            $connection->executeStatement(
                'INSERT INTO users (first_name, last_name, email, password, phone_number, birth_date) VALUES (?, ?, ?, ?, ?, ?)',
                [$firstName, $lastName, $email, $password, $phoneNumber, $birthDate]
            );

            return $this->redirectToRoute('app_signin');
        } catch (\Exception $e) {
            return $this->redirect('/signup?error=Registration failed. Email might already exist.');
        }
    }
}

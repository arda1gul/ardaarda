<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    private array $validUsers = [
        'Arda' => '123456',
        'Irem' => '123456',
    ];

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, SessionInterface $session): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {
            $username = trim($request->request->get('username', ''));
            $password = $request->request->get('password', '');

            if (isset($this->validUsers[$username]) && $this->validUsers[$username] === $password) {
                $session->set('user', $username);
                return $this->redirectToRoute('app_home');
            } else {
                $error = 'Onjuiste gebruikersnaam of wachtwoord!';
            }
        }

        return $this->render('login/index.html.twig', [
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('user');
        return $this->redirectToRoute('app_login');
    }
}
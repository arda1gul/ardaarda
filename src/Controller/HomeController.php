<?php

namespace App\Controller;

use App\Entity\Score;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SessionInterface $session, EntityManagerInterface$entityManager): Response
    {
        $username =$session->get('user');

        if (!$username) {
            return $this->redirectToRoute('app_login');
        }

        $scoreRepository =$entityManager->getRepository(Score::class);

        // 1. Jouw eigen totale punten
        $myScores =$scoreRepository->findBy(['username' => $username]);$totalPoints = 0;
        foreach ($myScores as$score) {
            $totalPoints +=$score->getPoints();
        }

        // 2. Scores berekenen voor het Live Tussenstand Leaderboard (Arda vs Irem)
        $ardaScores =$scoreRepository->findBy(['username' => 'Arda']);
        $ardaTotal = array_sum(array_map(fn($s) => $s->getPoints(),$ardaScores));

        $iremScores =$scoreRepository->findBy(['username' => 'Irem']);
        $iremTotal = array_sum(array_map(fn($s) => $s->getPoints(),$iremScores));

        return $this->render('home/index.html.twig', [
            'username' => $username,
            'totalPoints' => $totalPoints,
            'ardaTotal' => $ardaTotal,
            'iremTotal' => $iremTotal,
        ]);
    }
}
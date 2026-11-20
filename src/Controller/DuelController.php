<?php

namespace App\Controller;

use App\Entity\DuelAnswer;
use App\Entity\Score;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DuelController extends AbstractController
{
    // Uitgebreide vragenbank verdeeld per ronde (max 10 per keer)
    private array $roundsQuestions = [
        1 => [
            ['id' => 1, 'text' => 'Wat is jouw grootste ergernis in het dagelijks leven?'],
            ['id' => 2, 'text' => 'Welk land of welke stad wil jij absoluut als eerst bezoeken?'],
            ['id' => 3, 'text' => 'Ben jij een uitgesproken ochtendmens of een echte nachtbraker?'],
            ['id' => 4, 'text' => 'Wat is jouw ultieme comfort food op een luie dag?'],
            ['id' => 5, 'text' => 'Als je één superkracht zou mogen kiezen, welke kies je dan?'],
            ['id' => 6, 'text' => 'Hét type vakantie: hele dag relaxen op het strand of de hele dag op pad om te ontdekken?'],
            ['id' => 7, 'text' => 'Wie van jullie twee is het slordigst in het huishouden?'],
            ['id' => 8, 'text' => 'Wat is een ietwat gekke of onredelijke angst die jij hebt?'],
            ['id' => 9, 'text' => 'Wat is een guilty pleasure muzieknummer of artiest waar je stiekem van houdt?'],
            ['id' => 10, 'text' => 'Hoe ziet jouw ideale luie zondagochtend eruit?']
        ]
        // Je kunt hier eenvoudig ronde 2 t/m 10 aan toevoegen!
    ];

    #[Route('/duel', name: 'app_duel')]
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $username = $session->get('user');
        if (!$username) {
            return $this->redirectToRoute('app_login');
        }

        $opponent = ($username === 'Arda') ? 'Irem' : 'Arda';
        $round = 1; // Start in ronde 1

        $answerRepo = $entityManager->getRepository(DuelAnswer::class);

        // Check of de huidige speler al heeft ingediend voor ronde 1
        $myAnswers = $answerRepo->findBy([
            'username' => $username,
            'roundNumber' => $round
        ]);

        // Check of de tegenstander al heeft ingediend
        $opponentAnswers = $answerRepo->findBy([
            'username' => $opponent,
            'roundNumber' => $round
        ]);

        $questions = $this->roundsQuestions[$round];

        if ($request->isMethod('POST') && count($myAnswers) === 0) {
            foreach ($questions as $q) {
                $own = $request->request->get('own_' . $q['id']);
                $guess = $request->request->get('guess_' . $q['id']);

                if ($own && $guess) {
                    $answer = new DuelAnswer();
                    $answer->setUsername($username);
                    $answer->setRoundNumber($round);
                    $answer->setQuestionIndex($q['id']);
                    $answer->setOwnAnswer($own);
                    $answer->setGuessAnswer($guess);
                    
                    // Bereken direct punten als de tegenstander ook al klaar is
                    // (Simpele vergelijking: exacte match = 10 pts, anders 0 of 5)
                    $entityManager->persist($answer);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_duel');
        }

        // Als beide spelers klaar zijn, berekenen we de scores en tonen we de vergelijking
        $readyToCompare = (count($myAnswers) > 0 && count($opponentAnswers) > 0);

        return $this->render('duel/index.html.twig', [
            'username' => $username,
            'opponent' => $opponent,
            'round' => $round,
            'questions' => $questions,
            'hasSubmitted' => (count($myAnswers) > 0),
            'readyToCompare' => $readyToCompare,
            'myAnswers' => $myAnswers,
            'opponentAnswers' => $opponentAnswers
        ]);
    }
}
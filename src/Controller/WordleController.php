<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\WordleWord;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WordleController extends AbstractController
{
    #[Route('/wordle', name: 'app_wordle')]
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $username = $session->get('user');
        if (!$username) {
            return $this->redirectToRoute('app_login');
        }

        // Bepaal wie de tegenstander is (Arda <-> Irem)
        $opponent = ($username === 'Arda') ? 'Irem' : 'Arda';
        $today = new \DateTime('today'); // Reset elke dag na 00:00

        $wordRepo = $entityManager->getRepository(WordleWord::class);

        // 1. Zoek of de tegenstander al een woord heeft ingesteld voor HUIDIGE speler voor vandaag
        $incomingWord = $wordRepo->findOneBy([
            'setterUsername' => $opponent,
            'guesserUsername' => $username,
            'targetDate' => $today
        ]);

        // 2. Zoek of HUIDIGE speler al een woord heeft klaargezet voor de tegenstander voor vandaag
        $outgoingWord = $wordRepo->findOneBy([
            'setterUsername' => $username,
            'guesserUsername' => $opponent,
            'targetDate' => $today
        ]);

        // Verwerk het instellen van een woord voor de tegenstander
        if ($request->isMethod('POST') && $request->request->has('set_word')) {
            $newWord = $request->request->get('secret_word');
            if (strlen($newWord) >= 3) {
                if (!$outgoingWord) {
                    $outgoingWord = new WordleWord();
                    $outgoingWord->setSetterUsername($username);
                    $outgoingWord->setGuesserUsername($opponent);
                    $outgoingWord->setTargetDate($today);
                    $entityManager->persist($outgoingWord);
                }
                $outgoingWord->setWord($newWord);
                $outgoingWord->setIsCompleted(false); // Moet nog geraden worden door opponent
                $entityManager->flush();

                return $this->redirectToRoute('app_wordle');
            }
        }

        // Verwerk een raadpoging van het woord dat de tegenstander voor jou heeft ingesteld
        $guessResult = null;
        if ($request->isMethod('POST') && $request->request->has('guess_word') && $incomingWord && !$incomingWord->isCompleted()) {
            $guess = strtolower(trim($request->request->get('guess')));
            $secret = strtolower($incomingWord->getWord());

            if ($guess === $secret) {
                // Geraden! 50 punten
                $incomingWord->setIsGuessed(true);
                $incomingWord->setIsCompleted(true);
                
                // Sla op in Score tabel voor de live tussenstand
                $score = new Score();
                $score->setUsername($username);
                $score->setGameName('Wordle (Dagelijks Duel)');
                $score->setPoints(50);
                $score->setPlayedAt(new \DateTime());
                $entityManager->persist($score);
                
                $entityManager->flush();
                $guessResult = 'correct';
            } else {
                // Fout geraden (voor nu simpel gehouden op 1 poging of meerdere, we maken het een 1-poging of direct resultaat per submit)
                // Je wilde 5 pogingen: we kunnen dit uitbreiden of per submit controleren.
                $incomingWord->setIsCompleted(true); // Sluit af na poging
                $entityManager->flush();
                $guessResult = 'wrong';
            }

            return $this->redirectToRoute('app_wordle');
        }

        return $this->render('wordle/index.html.twig', [
            'username' => $username,
            'opponent' => $opponent,
            'incomingWord' => $incomingWord,
            'outgoingWord' => $outgoingWord,
            'guessResult' => $guessResult
        ]);
    }
}
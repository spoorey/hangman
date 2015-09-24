<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 09.09.2015
 * Time: 11:00
 */
namespace Application\Controller;


use Application\Entity\Word;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Application\Entity\User;
use Application\Entity\Game;

class GameController extends AbstractActionController implements ObjectManagerAwareInterface {

    use ProvidesObjectManager;

    public function indexAction() {
        $user = $this->getUser();
        $gameRepo = $this->getObjectManager()->getRepository(Game::class);
        $container = new Container('game');
        if (isset($container->gameId) && null != $container->gameId) {
            $game = $gameRepo->find($container->gameId);
            if ($game instanceof Game && $game->getFinishedAt() == null && $game->getUser()->getId() === $user->getId()) {
                return $this->redirect()->toRoute(
                    'application/default',
                    ['controller' => 'game', 'action' => 'play']
                );
            }
        }

        $oldGames = $gameRepo->findBy(
            [
              'user'        => $user->getId(),
              'finishedAt'  => null,
            ],
            [
                'startedAt' => 'asc',
            ],
            10
        );

        return new ViewModel([
            'oldGames'  => $oldGames,
            'gameCount' => count($oldGames)
        ]);
    }

    public function startAction() {
        $id = (int) $this->params()->fromRoute('id');
        $gameRepo = $this->getObjectManager()->getRepository(Game::class);

        $user = $this->getUser();
        $startedGames = $gameRepo->findBy([
            'user' => $user->getId(),
            'finishedAt' => null,
        ]);


        $game = null;
        if (null != $id) {
            $game = $gameRepo->findOneBy([
                'id'     => $id,
                'user' => $user->getId(),
                'finishedAt' => null,
            ]);
        }

        if (!$game instanceof Game) {
            $game = new Game();
            $game->setUser($user);


            $word = Word::getRandomWord($this->getObjectManager());
            $game->setWord($word->getWord());
        }

        $game->setLastActionAt(new DateTime());
        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->persist($game);
        $this->getObjectManager()->flush();

        $container = new Container('game');
        $container->gameId = $game->getId();

        return $this->redirect()->toRoute(
            'application/default',
            ['controller' => 'game', 'action' => 'play']
        );
    }

    public function playAction()
    {
        $container = new Container('game');
        if (!(isset($container->gameId) && null != $container->gameId)) {
            // no game stored in Session
            return $this->redirect()->toRoute(
                'application/default',
                ['controller' => 'game', 'action' => 'index']
            );
        }

        $gameRepo = $this->getObjectManager()->getRepository(Game::class);
        $user = $this->getUser();
        $game = $gameRepo->findOneBy([
            'id'   => $container->gameId,
            'user' => $user->getId(),
        ]);

        if (!$game instanceof Game) {
            // no game found
            return $this->redirect()->toRoute(
                'application/default',
                ['controller' => 'game', 'action' => 'index']
            );
        }

        return new ViewModel();
    }

    public function updateAction(){
        $container = new Container('game');
        if (!(isset($container->gameId) && null != $container->gameId)) {
            // no game stored in Session
            return $this->getResponse()->setStatusCode(404);
        }

        $gameRepo = $this->getObjectManager()->getRepository(Game::class);
        $user = $this->getUser();
        $game = $gameRepo->findOneBy([
            'id'   => $container->gameId,
            'user' => $user->getId(),
        ]);

        if (!$game instanceof Game) {
            return $this->getResponse()->setStatusCode(404);
        }
        $correctGuesses = 0;
        $guessesAndPositions = $game->getGuessesAndPositions();

        foreach ($guessesAndPositions as $guess) {
            $correctGuesses += count($guess['positions']);
        }
        $gameWon =  ($correctGuesses >= $game->getWordLength());
        $config = $this->serviceLocator->get('Config');

        $invalidLetter = false;
        // if this game is won, do not update it
        if (!$gameWon) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getPost();
                if (isset($data['letter'])) {
                    $letter = Word::getStringUpper((string) $data['letter']);
                    if (in_array($letter, $config['game']['allowedLetters'])) {
                        if(mb_strlen($letter, 'utf-8') == 1) {
                            $game->addGuessedLetter($letter);
                        }
                    } else {
                        $invalidLetter = true;
                    }
                }
            }
        }

        $allowedLetters = $config['game']['allowedLetters'];
        $correctGuesses = 0;
        $guessesAndPositions = $game->getGuessesAndPositions();

        foreach ($guessesAndPositions as $guess) {
            $correctGuesses += count($guess['positions']);
        }

        $gameWon =  ($correctGuesses >= $game->getWordLength());
        if ($gameWon) {
            $container = new Container('game');
            $container->gameId = null;
            $game->setWon(true);
            $game->setFinishedAt(new DateTime());
        }

        $wrongGuesses = $game->getWrongGuessesAmount();
        $leftGuesses = $config['game']['gameOverAfter'] - $wrongGuesses;

        $gameLost = $leftGuesses <= 0;
        if ($gameLost) {
            $container = new Container('game');
            $container->gameId = null;
            $game->setWon(false);
            $game->setFinishedAt(new DateTime());

            // reveal the word
            $guessesAndPositions = $game->getLettersAndPositon();
        }

        $this->getObjectManager()->persist($game);
        $this->getObjectManager()->flush();

        $variables = [
            'letterCount'           => $game->getWordLength(),
            'guessedLetters'        => $guessesAndPositions,
            'allowedLetters'        => $allowedLetters,
            'gameWon'               => $gameWon,
            'leftGuesses'           => $leftGuesses,
            'gameLost'              => $gameLost,
            'invalidLetter'         => $invalidLetter,
        ];

        return new JsonModel($variables);
    }

    /**
     * @return \Zend\Http\Response
     */
    public function forfeitAction() {
        $container = new Container('game');
        if (!(isset($container->gameId) && null != $container->gameId)) {
            // no game stored in Session
            return $this->getResponse()->setStatusCode(404);
        }

        $gameRepo = $this->getObjectManager()->getRepository(Game::class);
        $user = $this->getUser();
        $game = $gameRepo->findOneBy([
            'id'   => $container->gameId,
            'user' => $user->getId(),
        ]);

        if (!$game instanceof Game) {
            return $this->getResponse()->setStatusCode(404);
        }

        $game->setFinishedAt(new DateTime());
        $game->setWon(false);
        $this->getObjectManager()->persist($game);
        $this->getObjectManager()->flush();
        $container->gameId = null;

        return $this->redirect()->toRoute('application/default', ['controller' => 'game']);
    }

    public function pauseAction(){
        $container = new Container('game');
        if (!(isset($container->gameId) && null != $container->gameId)) {

            return $this->redirect()->toRoute('application/default', ['controller' => 'game']);
        }

        $container->gameId = null;
        return $this->redirect()->toRoute('application/default', ['controller' => 'game']);
    }
    /**
     * @return User
     */
    private function getUser() {
        $user = $this->serviceLocator->get('auth')->getIdentity();

        if (!$user instanceof User) {
            return false;
        }

        return $this->getObjectManager()->getRepository(User::class)->find($user->getId());
    }
}

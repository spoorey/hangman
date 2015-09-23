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
use Doctrine\ORM\QueryBuilder;
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
        $gameRepo = $this->getObjectManager()->getRepository(Game::class);
        $container = new Container('game');
        if (isset($container->gameId) && null != $container->gameId) {
            $game = $gameRepo->find($container->gameId);
            if ($game instanceof Game && $game->getFinishedAt() == null && $game->getUser()->getId() === $this->getUser()->getId()) {
                return $this->redirect()->toRoute(
                    'application/default',
                    ['controller' => 'game', 'action' => 'play']
                );
            }
        }

        $userRepo = $this->getObjectManager()->getRepository(User::class);

        $user = $userRepo->find(1);
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

        /*$game = new Game();
        $game->setUser($user);
        $game->setWord($word);
        $this->getObjectManager()->persist($game);
        $this->getObjectManager()->flush();*/

        return new ViewModel([
            'oldGames'  => $oldGames,
            'gameCount' => count($oldGames)
        ]);
    }

    public function startAction() {
        $id = (int) $this->params()->fromRoute('id');
        $gameRepo = $this->getObjectManager()->getRepository(Game::class);

        $game = null;
        if (null != $id) {
            $game = $gameRepo->findOneBy([
                'id'     => $id,
                'user' => $this->getUser()->getId(),
            ]);
        }

        if (!$game instanceof Game) {
            $game = new Game();
            $game->setUser($this->getUser());


            $word = Word::getRandomWord($this->getObjectManager());
            $game->setWord($word->getWord());
        }

        $game->setLastActionAt(new DateTime());
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
        $game = $gameRepo->findOneBy([
            'id'   => $container->gameId,
            'user' => $this->getUser()->getId(),
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
        $game = $gameRepo->findOneBy([
            'id'   => $container->gameId,
            'user' => $this->getUser()->getId(),
        ]);

        if (!$game instanceof Game) {
            return $this->getResponse()->setStatusCode(404);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data['letter'])) {
                $letter = (string) $data['letter'];
                if(Word::getStringLength($letter) == 1) {
                    $game->addGuessedLetter($letter);
                    $this->getObjectManager()->persist($game);
                    $this->getObjectManager()->flush();
                }
            }
        }

        $guessesAndPositions = $game->getGuessesAndPositions();
        sort($guessesAndPositions);
        $variables = [
            'letterCount'           => $game->getWordLength(),
            'guessedLetters'        => $guessesAndPositions,
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
        $game = $gameRepo->findOneBy([
            'id'   => $container->gameId,
            'user' => $this->getUser()->getId(),
        ]);

        if (!$game instanceof Game) {
            return $this->getResponse()->setStatusCode(404);
        }

        $game->setFinishedAt(new DateTime());
        $this->getObjectManager()->persist($game);
        $this->getObjectManager()->flush();
        $container->getManager()->destroy();

        return $this->redirect()->toRoute('application/default', ['controller' => 'game']);
    }

    public function pauseAction(){
        $container = new Container('game');
        if (!(isset($container->gameId) && null != $container->gameId)) {

            return $this->getResponse()->setStatusCode(404);
        }

        $container->getManager()->destroy();
        return $this->redirect()->toRoute('application/default', ['controller' => 'game']);
    }
    /**
     * @return User
     */
    private function getUser(){
        $userRepo = $this->getObjectManager()->getRepository(User::class);

        return $userRepo->find(1);
    }
}

<?php


/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 09.09.2015
 * Time: 11:00
 */
namespace Application\Controller;


use Application\Entity\Word;
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
        $userRepo = $this->getObjectManager()->getRepository(User::class);

        $user = $userRepo->find(1);
        $gameRepo = $this->getObjectManager()->getRepository(Game::class);
        $oldGames = $gameRepo->findBy(
            [
              'user'        => $user->getId(),
              'finishedAt'  => null,
            ],
            [
                'finishedAt' => 'desc',
            ],
            10
        );

        /** @var QueryBuilder $qb */
        $qb = $this->getObjectManager()->createQueryBuilder();
        $qb->select('count(game.id)');
        $qb->where('game.user = ?1')->andWhere('game.finishedAt IS NULL')->setParameter(1, $user->getId());
        $qb->from(Game::class,'game');
        $count = $qb->getQuery()->getSingleScalarResult();

        /*$game = new Game();
        $game->setUser($user);
        $game->setWord($word);
        $this->getObjectManager()->persist($game);
        $this->getObjectManager()->flush();*/

        return new ViewModel([
            'oldGames'  => $oldGames,
            'gameCount' => $count
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

        $game->setLastActionAt(new \DateTime());
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

        $variables = [
            'letterCount'           => mb_strlen($game->getWord()),
            'guessedLetters'        => $game->getGuessesAndPositions(),
        ];

        return new JsonModel($variables);
    }

    /**
     * @return User
     */
    private function getUser(){
        $userRepo = $this->getObjectManager()->getRepository(User::class);

        return $userRepo->find(1);
    }
}

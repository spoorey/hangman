<?php


/**
 * Created by PhpStorm
 * User: David Spörri
 * Date: 13082015
 * Time: 09:14
 */

namespace Application\Controller;


use Application\Form\WordForm;
use Application\Form\WordSearchForm;
use Application\InputFilter\WordInputFilter;
use Application\InputFilter\WordSearchInputFilter;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Entity\Word;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

class WordController extends AbstractActionController implements ObjectManagerAwareInterface
{

    use ProvidesObjectManager;

    public function indexAction()
    {

        /** @var QueryBuilder $qb */
        $qb = $this->getObjectManager()->createQueryBuilder();
        $qb->select('w')
            ->from(Word::class, 'w');

        $form = new WordSearchForm();
        $form->setData($this->params()->fromQuery());
        $form->setInputFilter(new WordSearchInputFilter());
        $form->isValid();

        $direction = strtolower($form->getData()['d']);
        $direction = ($direction == 'desc' ? 'desc' : 'asc');

        $orderBy = strtolower($form->getData()['o']);
        switch ($orderBy) {
            case 'created':
                $order = 'createdAt';
                break;
            case 'updated':
                $order = 'updatedAt';
                break;
            default:
                $orderBy = $order = 'word';
                break;
        }

        $qb->orderBy('w.' . $order, $direction);
        // no need to avoid SQL injection, as doctrine does this for us
        $query = $form->getData()['q'];
        $qb->where('w.word LIKE :query')
        ->setParameter('query', '%' . $query . '%');

        $itemsPerPage = 50;

        $adapter = new DoctrineAdapter(new ORMPaginator($qb->getQuery()));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(50);
        $page = (int) $this->params()->fromQuery('p');

        if ($page != null) {
            $paginator->setCurrentPageNumber($page);
        } else {
            $paginator->setCurrentPageNumber(1);
        }

        $pages = $paginator->getTotalItemCount() / $itemsPerPage;

        $paginator->setPageRange($pages);

        $viewModel = new ViewModel();

        $viewModel->setVariables([
            'words'       => $paginator->getCurrentItems(),
            'pageCount'   => $paginator->getPageRange() + 1,
            'currentPage' => $paginator->getCurrentPageNumber(),
            'orderBy'     => $orderBy,
            'direction'   => $direction,
            'query'       => $query,
            'form'        => $form,
        ]);

        return $viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $objectManager = $this->getObjectManager();
        $wordRepo = $objectManager->getRepository(Word::class);
        $word = $wordRepo->find((int) $id);
        if (!$word instanceof Word) {
            $viewModel = new ViewModel();
            $viewModel->setTemplate('error/404');
            $viewModel->setVariable('reason', 'Dieses Wort konnte nicht gefunden werden.');

            return $viewModel;
        }


        $form = new WordForm();
        $form->setData(['word' => $word->getWord()]);

        $conflict = false;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            $config = $this->serviceLocator->get('Config');
            $form->setInputFilter(new WordInputFilter($config['game']['allowedLetters']));
            if ($form->isValid()) {
                $data = $form->getData();
                $form->setData($data);
                $existingWord = $this->getObjectManager()->getRepository(Word::class)->findOneBy(['word' => $data['word']]);
                if ($existingWord instanceof Word && $existingWord->getId() != $word->getId())  {
                    $conflict = true;
                } else {
                    $word->setWord($data['word']);
                    $this->getObjectManager()->persist($word);
                    $this->getObjectManager()->flush();
                }
            }
        }


        $viewModel = new ViewModel([
            'form' => $form,
            'conflict' => $conflict,
        ]);

        return $viewModel;
    }

    public function addAction(){
        $form = new WordForm();

        $request = $this->getRequest();
        $conflict = false;
        if ($request->isPost()) {
            $form->setData($request->getPost());
            $config = $this->serviceLocator->get('Config');
            $form->setInputFilter(new WordInputFilter($config['game']['allowedLetters']));
            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->getObjectManager()->getRepository(Word::class)->findOneBy(['word' => $data['word']]))  {
                    $conflict = true;
                } else {
                    $word = new Word();
                    $word->setWord($data['word']);
                    $this->getObjectManager()->persist($word);
                    $this->getObjectManager()->flush();
                    $this->redirect()->toRoute('application/words');
                }
            }
        }

        $viewModel = new ViewModel([
            'form' => $form,
            'conflict' => $conflict,

        ]);

        return $viewModel;
    }

    static function getEditForm(Word $word) {
        $form = new WordForm();
        $form->setData(['word' => $word->getWord()]);
        return $form;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 16.06.2015
 * Time: 13:32
 */

namespace Application\Controller;


use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;

class DemoController extends AbstractActionController implements ObjectManagerAwareInterface {

    use ProvidesObjectManager;

    public function indexAction(){

        die('I am the demo man');
    }

    public function fooAction(){
        die('two programmers walk into a foo ');
    }
} 
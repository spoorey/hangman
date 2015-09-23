<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 19.08.2015
 * Time: 15:10
 */

namespace Application\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct('login', $options);

        $this->add([
            'name'       => 'username',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Benutzername',
            ],
            'attributes' => [
                'id' => 'username',
            ],
        ]);
        $this->add([
            'name'       => 'password',
            'type'       => 'Password',
            'options'    => [
                'label' => 'Passwort',
            ],
            'attributes' => [
                'id' => 'password',
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Anmelden',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary'
            ],
        ]);
    }
}

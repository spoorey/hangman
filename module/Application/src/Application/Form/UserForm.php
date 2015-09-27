<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 23.09.2015
 * Time: 11:05
 */

namespace Application\Form;


use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct('user', $options);

        $this->add([
            'name'       => 'username',
            'type'       => 'Text',
            'options'    => [
                'label'    => 'Benutzername',
            ],
            'attributes' => [
                'id'       => 'user-name',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'name'       => 'email',
            'type'       => 'Email',
            'options'    => [
                'label'    => 'E-Mail (freiwillig)',
            ],
            'attributes' => [
                'id'       => 'email',
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
            'name'       => 'confirmPassword',
            'type'       => 'Password',
            'options'    => [
                'label' => 'Passwort bestätigen',
            ],
            'attributes' => [
                'id' => 'confirm-password',
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
 
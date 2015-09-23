<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 19.08.2015
 * Time: 15:10
 */

namespace Application\Form;

use Zend\Form\Form;

class WordForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct('word', $options);

        $this->add([
            'name'       => 'word',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Wort',
                'required' => true,
            ],
            'attributes' => [
                'id'       => 'word',
                'required' => 'required',

            ],
        ]);


        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Speichern',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary'
            ],
        ]);
    }
}

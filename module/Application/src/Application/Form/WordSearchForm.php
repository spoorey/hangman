<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 24.09.2015
 * Time: 18:42
 */

namespace Application\Form;


use Zend\Form\Form;

class WordSearchForm extends Form
{

    public function __construct()
    {
        parent::__construct('word', []);

        $this->setAttribute('class', 'form-inline word-search-form');
        $this->setAttribute('method', 'get');

        $this->add([
            'name'       => 'q',
            'type'       => 'Text',
            'options'    => [
                'label' => 'Suche',
            ],
            'attributes' => [
                'id' => 'q',

            ],
        ]);

        $this->add([
            'name'    => 'o',
            'type'    => 'Select',
            'options' => [
                'label'         => 'Sortieren nach',
                'value_options' => [
                    'word'    => 'Wort ',
                    'created' => 'Erstellt am',
                    'updated' => 'Bearbeitet am',
                ],
            ],
            'attributes' => [
                'id' => 'o',

            ],
        ]);


        $this->add([
            'name'    => 'd',
            'type'    => 'Select',
            'options' => [
                'label'         => 'Richtung',
                'value_options' => [
                    'asc'  => 'Aufsteigend',
                    'desc' => 'Absteigend',
                ],
            ],
            'attributes' => [
                'id' => 'd',

            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => 'Submit',
            'attributes' => [
                'value' => 'Aktualisieren',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary'
            ],
        ]);
    }
}

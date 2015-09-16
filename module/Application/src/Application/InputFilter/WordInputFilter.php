<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 19.08.2015
 * Time: 16:14
 */

namespace Application\InputFilter;


use Zend\InputFilter\InputFilter;

class WordInputFilter extends InputFilter
{
    function __construct()
    {
        $this->add([
            'name'       => 'word',
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 6,
                        'max'      => 100,
                    ],
                ],
            ],
        ]);
    }
}

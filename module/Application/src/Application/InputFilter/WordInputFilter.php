<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 19.08.2015
 * Time: 16:14
 */

namespace Application\InputFilter;


use Zend\Filter\StringToUpper;
use Zend\InputFilter\InputFilter;

class WordInputFilter extends InputFilter
{
    function __construct(array $allowedLetters)
    {
        $this->add([
            'name'       => 'word',
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
                [
                    'name' => StringToUpper::class,
                    'options' => [
                        'encoding' => 'utf-8',
                    ]
                ],
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

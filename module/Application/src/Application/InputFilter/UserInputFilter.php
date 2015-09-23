<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 23.09.2015
 * Time: 15:48
 */

namespace Application\InputFilter;


use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;

class UserInputFilter extends InputFilter {
    function __construct()
    {
        $this->add([
            'name'       => 'username',
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
                        'min'      => 3,
                        'max'      => 50,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'email',
            'required'   => false,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => EmailAddress::class,
                ],
            ],
        ]);

        $this->add([
            'name'       => 'password',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 6,
                    ],
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'confirmPassword',
                    ],
                ]
            ],
        ]);

        // must be same as password anyways, no further validation needed
        $this->add([
            'name'       => 'confirmPassword',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'password',
                    ],
                ]
            ],
        ]);
    }
}
 
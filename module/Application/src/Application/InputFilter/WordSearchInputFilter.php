<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 24.09.2015
 * Time: 19:11
 */

namespace Application\InputFilter;


use Zend\InputFilter\InputFilter;

class WordSearchInputFilter extends InputFilter {
    /**
     * We need almost no validation, as invalid values would just trigger default search behaviour
     */
    function __construct()
    {
        $this->add([
            'name'       => 'q',
            'required'   => false,
        ]);
        $this->add([
            'name'       => 'o',
            'required'   => false,
        ]);
        $this->add([
            'name'       => 'd',
            'required'   => false,
        ]);

    }
}
 
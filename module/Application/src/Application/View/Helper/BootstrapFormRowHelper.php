<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 26.08.2015
 * Time: 10:40
 */

namespace Application\View\Helper;


use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormRow;

class BootstrapFormRowHelper extends FormRow{


    public function render(ElementInterface $element, $labelPosition = null)
    {

        if ($element->getAttribute('type') != 'submit') {
            $elementAttributes = $element->getAttributes();
            if (isset($elementAttributes['class']) && $elementAttributes['class'] != null) {
                $elementAttributes['class'] .= ' form-control';
            } else {
                $elementAttributes['class'] = 'form-control';
            }
            $element->setAttributes($elementAttributes);
        }


        $row = parent::render($element, $labelPosition);
        return '<div class="form-group">' . $row . '</div>';
    }

}
 
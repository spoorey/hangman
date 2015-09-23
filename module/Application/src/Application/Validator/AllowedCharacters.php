<?php
namespace Application\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 23.09.2015
 * Time: 09:42
 */

class AllowedCharacters extends AbstractValidator {

    protected $messageTemplates = [
        'invalidCharacters' => 'Die Zeichen %characters% dürfen nicht verwendet werden.',
        'invalidCharacter' => 'Das Zeichen "%character%" darf nicht verwendet werden.',
    ];

    protected $messageVariables = [
        'characters'  => 'characters',
        'character'  => 'character',
    ];

    protected $characters;

    protected $character;

    /**
     * @var array
     */
    private $allowedCharacters;

    /**
     * Sets validator options
     *
     * @param  int|array|\Traversable $options
     */
    public function __construct($options = [])
    {
        if (!isset($options['allowedCharacters'])) {

        }

        parent::__construct($options);
    }

    /**
     * Checks if a value contains only the allowed characters
     *
     * @param mixed $value
     * @return bool|void
     */
    public function isValid($value)
    {
        $letters = str_split(utf8_decode($value));
        $isValid = true;
        if (count($letters) == 0) {
            return true;
        }

        $invalidLetters = [];
        foreach ($letters as $letter) {
            if (!in_array(utf8_encode($letter), $this->allowedCharacters)) {
                $invalidLetters[$letter] = utf8_encode($letter);
                $isValid = false;
            }
        }


        if (count($invalidLetters) > 1) {
            $lastLetter = array_pop($invalidLetters);
            $this->characters = '"' . implode('", "', $invalidLetters) . '"';
            $this->characters .= 'und "' . $lastLetter . '"';
            $this->error('invalidCharacters');
        } elseif (count($invalidLetters) > 0) {
            $this->character = array_values($invalidLetters)[0];
            $this->error('invalidCharacter');
        }


        return $isValid;
    }

    /**
     * @return array
     */
    public function getAllowedCharacters()
    {
        return $this->allowedCharacters;
    }

    /**
     * @param array $allowedCharacters
     */
    public function setAllowedCharacters($allowedCharacters)
    {
        $this->allowedCharacters = $allowedCharacters;
    }
}
 
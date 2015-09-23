<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 30.06.2015
 * Time: 09:56
 */

namespace Application\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Application\Entity\Word;

/**
 * This represents a game
 *
 * @HasLifecycleCallbacks
 * @ORM\Entity
 * @ORM\Table(name="hm_game")
 */
class Game {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false, name="started_at");
     */
    protected $startedAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true, name="finished_at");
     */
    protected $finishedAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false, name="last_action_at");
     */
    protected $lastActionAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false);
     */
    protected $word;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=false, name="guessed_letters");
     */
    protected $guessedLetters = [];

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false);
     */
    protected $won = false;

    /**
     * @PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        if (null == $this->id && null == $this->startedAt) {
            $this->startedAt = new DateTime();
        }

        $this->lastActionAt = new DateTime();
    }

    public function getWordLength() {
        return mb_strlen($this->word, 'utf-8');
    }

    public function getGuessesAndPositions($includeWrongGuesses = true) {
        $word = utf8_decode($this->word);
        $letters = [];

        if (null != $this->guessedLetters && is_array($this->guessedLetters)) {
            foreach ($this->guessedLetters as $guess) {
                $guess = utf8_decode($guess);
                $strpos = strpos($word, $guess);
                if (false !== $strpos) {
                    $positions = [];
                    $offset = 0;
                    do {
                        $position = strpos($word, $guess, $offset);
                        $offset = $position + 1;
                        if (false !== $position) {
                            $positions[] = $position;
                        }
                    } while ($position !== false);
                    $letters[] = [
                        'letter'    => utf8_encode($guess),
                        'positions' => $positions,
                    ];
                } elseif ($includeWrongGuesses) {
                    $letters[] = [
                        'letter'    => utf8_encode($guess),
                        'positions' => [],
                    ];
                }
            }
        }

        return $letters;
    }

    /**
     * @return DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @param DateTime $finishedAt
     */
    public function setFinishedAt(DateTime $finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getLastActionAt()
    {
        return $this->lastActionAt;
    }

    /**
     * @param DateTime $lastActionAt
     */
    public function setLastActionAt(DateTime $lastActionAt)
    {
        $this->lastActionAt = $lastActionAt;
    }

    /**
     * @return DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param DateTime $startedAt
     */
    public function setStartedAt(DateTime $startedAt)
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getGuessedLetters()
    {
        return $this->guessedLetters;
    }

    /**
     * @param array $guessedLetters
     */
    public function setGuessedLetters(array $guessedLetters)
    {
        foreach ($guessedLetters as $i => $letter) {
            $guessedLetters[$i] = Word::getStringUpper($letter);
        }
        $this->guessedLetters = $guessedLetters;
    }

    /**
     * @param string $letter
     */
    public function addGuessedLetter($letter)
    {
        if (!$this->letterWasGuessed($letter)) {
            $this->guessedLetters[] = Word::getStringUpper($letter);
        }
    }

    /**
     * @param $letter
     * @return bool
     */
    public function letterWasGuessed($letter)
    {
        return in_array(Word::getStringUpper($letter), $this->guessedLetters);
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string  $word
     */
    public function setWord($word)
    {
        $this->word = Word::getStringUpper($word);
    }

    /**
     * @return boolean
     */
    public function isWon()
    {
        return $this->won;
    }

    /**
     * @param boolean $won
     */
    public function setWon($won)
    {
        $this->won = $won;
    }
}
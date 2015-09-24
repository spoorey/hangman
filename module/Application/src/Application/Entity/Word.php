<?php
namespace Application\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Rand;

/**
 * This represents a word
 *
 * @HasLifecycleCallbacks
 * @ORM\Entity
 * @ORM\Table(name="hm_word")
 */
class Word {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true);
     */
    protected $word;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false, name="created_at");
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false, name="updated_at");
     */
    protected $updatedAt;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\User", mappedBy="word")
     */
    protected $games;

    /**
     * @PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        if (null == $this->id && null == $this->createdAt) {
            $this->createdAt = new DateTime();
        }

        $this->updatedAt = new DateTime();
    }

    public function getFirstLetter() {
        return mb_substr($this->word, 0, 1, 'utf-8');
    }

    /**
     * @param EntityManager $objectManager
     * @return Word
     * @throws \Doctrine\ORM\ORMException
     */
    static function getRandomWord(EntityManager $objectManager) {
        $config = $objectManager->getConfiguration();
        $config->addCustomStringFunction('RAND', Rand::class);
        /** @var QueryBuilder $qb */

        do {
            $qb = $objectManager->createQueryBuilder();
            $qb->select('word.id');
            $qb->orderBy('RAND()');
            $qb->setMaxResults(1);
            $qb->from(Word::class, 'word');
            $results = array_values($qb->getQuery()->getResult());
            $id = $results[0]['id'];

            $word = $objectManager->getRepository(self::class)->find($id);
            if ($word instanceof self) {
                return $word;
            }
        } while (!$word instanceof self);
    }

    function __toString()
    {
        return $this->word;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string $word
     */
    public function setWord($word)
    {
        $this->word = self::getStringUpper($word);
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * @param string $string
     * @return string
     */
    static function getStringUpper($string) {
        return mb_strtoupper($string, 'utf-8');
    }
}
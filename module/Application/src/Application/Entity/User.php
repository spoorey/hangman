<?php
/**
 * Created by PhpStorm.
 * User: David Spörri
 * Date: 30.06.2015
 * Time: 09:56
 */

namespace Application\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Zend\Crypt\Password\Bcrypt;

/**
 * This represents a user
 *
 * @HasLifecycleCallbacks
 * @ORM\Entity
 * @ORM\Table(name="hm_user")
 */
class User {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, name="user_name", unique=true);
     */
    protected $userName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="email");
     */
    protected $email;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\User", mappedBy="user")
     */
    protected $games;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, name="password", unique=true);
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, name="role");
     */
    protected $role = 'guest';


    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false, name="registered_at");
     */
    protected $registeredAt;

    public function verifyPassword($password) {
        $bcrypt = new Bcrypt();

        return $bcrypt->verify($password, $this->password);
    }

    /**
     * @PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        if (null == $this->id && null == $this->registeredAt) {
            $this->registeredAt = new DateTime();
        }
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return Collection
     */
    public function getGames()
    {
        return $this->games;
    }

    public function addGame(Game $game) {
        $game->setUser($this);
        $this->games->add($game);
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}

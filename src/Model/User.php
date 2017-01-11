<?php
namespace Boxspaced\CmsAccountModule\Model;

use DateTime;
use Boxspaced\EntityManager\Entity\AbstractEntity;

class User extends AbstractEntity
{

    const TYPE_PUBLIC = 'public';
    const TYPE_ADMIN = 'admin';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @param string $type
     * @return User
     */
    public function setType($type)
    {
        $this->set('type', $type);
		return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->get('username');
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->set('username', $username);
		return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->set('email', $email);
		return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->get('password');
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->set('password', $password);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastLogin()
    {
        return $this->get('last_login');
    }

    /**
     * @param DateTime $lastLogin
     * @return User
     */
    public function setLastLogin(DateTime $lastLogin = null)
    {
        $this->set('last_login', $lastLogin);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getThisLogin()
    {
        return $this->get('this_login');
    }

    /**
     * @param DateTime $thisLogin
     * @return User
     */
    public function setThisLogin(DateTime $thisLogin = null)
    {
        $this->set('this_login', $thisLogin);
		return $this;
    }

    /**
     * @return bool
     */
    public function getActivated()
    {
        return $this->get('activated');
    }

    /**
     * @param bool $activated
     * @return User
     */
    public function setActivated($activated)
    {
        $this->set('activated', $activated);
		return $this;
    }

    /**
     * @return bool
     */
    public function getEverBeenActivated()
    {
        return $this->get('ever_been_activated');
    }

    /**
     * @param bool $everBeenActivated
     * @return User
     */
    public function setEverBeenActivated($everBeenActivated)
    {
        $this->set('ever_been_activated', $everBeenActivated);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegisteredTime()
    {
        return $this->get('registered_time');
    }

    /**
     * @param DateTime $registeredTime
     * @return User
     */
    public function setRegisteredTime(DateTime $registeredTime = null)
    {
        $this->set('registered_time', $registeredTime);
		return $this;
    }

}

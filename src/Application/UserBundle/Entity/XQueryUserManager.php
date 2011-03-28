<?php

namespace Application\UserBundle\Entity;

use Application\UserBundle\Entity\XQueryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use existdb\eXist;

class XQueryUserManager implements UserProviderInterface {
    protected $db;

    public function __construct($exist) {
        $this->db = $exist;
    }

    public function getOneById($id) {
        $this->db->connect() or die ($this->db->getError());
        die('KIKOOOO XQueryUserManager.getOneById');

        $query = 'for $i in document("/db/orga/users.xml")//user[@id ="'.$id.'"]
                  return $i';

        return XQueryUser::fromXML($result['XML']);
    }

    public function addUser(XQueryUser $user) {
        $this->db->connect() or die ($this->db->getError());

        $query ='update insert
            <user id="U{count(document("/db/orga/users.xml")//user)+1}">
                <login>'.$user->getUsername().'</login>
                <password>'.hash('sha512',$user->getPassword()).'</password>
                <firstname>'.$user->getFirstName().'</firstname>
                <surname>'.$user->getSurName().'</surname>
                <mail>'.$user->getMail().'</mail>
                <phone>'.$user->getPhone().'</phone>
                <avatar>'.$user->getAvatar().'</avatar>
            </user>
            into document("/db/orga/users.xml")//users';

        $result = $this->db->xquery($query)
             or (preg_match('/No data found/', $this->db->getError())
             or die($this->db->getError()));
    }

    public function editUser($user) {
        $this->db->connect() or die ($this->db->getError());

        $query ='update replace document("/db/orga/users.xml")//user[@id = "'.$user->getId().'"]
                 with
            <user id="'.$user->getId().'">
                <login>'.$user->getUsername().'</login>
                <password>'.hash('sha512',$user->getPassword()).'</password>
                <firstname>'.$user->getFirstName().'</firstname>
                <surname>'.$user->getSurName().'</surname>
                <mail>'.$user->getMail().'</mail>
                <phone>'.$user->getPhone().'</phone>
                <avatar>'.$user->getAvatar().'</avatar>
            </user>';

        $result = $this->db->xquery($query) or
            (preg_match('/No data found/', $this->db->getError()) or
             die($this->db->getError()));
    }

    public function findOneBy($array) {
        if (isset($array['username']))
            return $this->loadUserByUsername($array['username']);
        throw new UnsupportedException("Not implemented");
    }

    public function loadUserByUsername($username) {
        $this->db->connect() or die ($this->db->getError());

        $query = '<results>
                  {for $i in document("/db/orga/users.xml")//user[
                                 login ="'.$username.'"]
                  return $i
                  }
                  </results>';

        $result = $this->db->xquery($query) or die ($this->db->getError());

        return XQueryUser::fromXML($result['XML']);
    }

    public function loadUser(UserInterface $user) {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
             sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    public function supportsClass($class) {
        return $class === 'Application\UserBundle\Entity\XQueryUser';
    }
}

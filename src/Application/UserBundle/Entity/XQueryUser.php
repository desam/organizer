<?php
namespace Application\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Imagine;

/**
/* @orm:Entity(repositoryClass="Application\UserBundle\Entity\XQueryUserManager")
 */
class XQueryUser implements UserInterface {

    /**
     * @orm:Id
     * @orm:Column(type="string")
     */
    protected $id;

    /**
     * @validation:NotBlank
     */
    protected $login;

    /**
     * @validation:NotBlank
     */
    protected $pass;

    /**
     * @validation:NotBlank
     */
    protected  $firstname;

    /**
     * @validation:NotBlank
     */
    protected $surname;

    /**
     * @validation:Email
     * @validation:NotBlank
     */
    protected $mail;

    /**
     * @validation:NotBlank
     * @validation:Regex("/^\d{2}\d{2}\d{2}\d{2}\d{2}$/")
     */
    protected $phone;

    protected $avatar;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->login;
    }

    public function setUsername($login) {
        $this->login = $login;
    }

    public function setPassword($pass) {
        $this->pass = $pass;
    }

    public function getPassword() {
        return $this->pass;
    }

    public function getRoles() {
        return array();
    }

    public function getFirstName() {
        return $this->firstname;
    }

    public function setFirstName($firstname) {
        $this->firstname = $firstname;
    }

    public function getSurName() {
        return $this->surname;
    }

    public function setSurName($surname) {
        $this->surname = $surname;
    }

    public function getMail() {
        return $this->mail;
    }

    public function setMail( $mail) {
        $this->mail = $mail;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone( $phone) {
        $this->phone = $phone;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function equals(UserInterface $other) {
        return $this->getId() === $other->getId()
           and $this->getMail() === $other->getMail();
    }

    public function eraseCredentials() {
        //remove sensitive data from the user..?
    }

    // bacon ?
    public function getSalt() {
        return '';
        /* return 'kirby' . $this->getPhone(); */
    }

    public function setAvatar($image) {
        $dir      = realpath(__DIR__ . '/../../../../web/uploads/avatar');
        $filename = uniqid() . '.png';
        $imagine  = new Imagine\Gd\Imagine();

        $image = $imagine->open($image);
        $image->thumbnail(new Imagine\Image\Box(240, $image->getSize()->getHeight()), Imagine\ImageInterface::THUMBNAIL_INSET)
            ->crop(new Imagine\Image\Point(0, 0), new Imagine\Image\Box(240, 198))
            ->save($dir . '/' . $filename);

        $this->avatar = $filename;
    }

    static public function fromXML($data) {
        $xml = simplexml_load_string($data);
        $user = new XQueryUser();

        $user->setId((string)$xml->user['id']);
        $user->setUsername((string)$xml->user->login);
        $user->setFirstName((string)$xml->user->firstname);
        $user->setSurName((string)$xml->user->surname);
        $user->setMail((string)$xml->user->mail);
        $user->setPhone((string)$xml->user->phone);
        $user->setPassword((string)$xml->user->password);

        return $user;
    }

}

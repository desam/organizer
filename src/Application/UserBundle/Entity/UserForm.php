<?php
namespace Application\UserBundle\Entity;

use Symfony\Component\Form\Form;

use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\FileField;


class UserForm extends Form
{
    protected function configure()	
    {	
        $this->add(new TextField('login', array('max_length' => 30,)));
		$this->add(new PasswordField('pass', array('max_length' => 30,)));
        $this->add(new TextField('firstname', array('max_length' => 30,))); 
        $this->add(new TextField('surname', array('max_length' => 30,)));
        $this->add(new TextField('mail', array('max_length' => 30,)));
        $this->add(new TextField('phone', array('max_length' => 10,)));        
        $this->add(new FileField('avatar',array('secret' => 10,)));        
    }  
    
}



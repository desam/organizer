<?php
namespace Application\UserBundle\Entity;

use Symfony\Component\Form\Form;

use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;

class LoginForm extends Form
{
    protected function configure()	
    {	 
	    $this->add(new TextField('login', array('max_length' => 30,)));
		$this->add(new PasswordField('pass', array('max_length' => 30,)));        
    }
}

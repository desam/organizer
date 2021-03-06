<?php
namespace Application\UserBundle\Entity;

use Symfony\Component\Form\Form;

use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;

class GroupForm extends Form
{
    protected function configure()	
    {	 
	    $this->add(new TextField('groupname', array('max_length' => 30,)));
        $this->add(new TextareaField('groupdescription'));
        $this->add(new FileField('avatar',array('secret' => 10,)));                
    }
}
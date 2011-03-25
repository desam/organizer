<?php
namespace Application\UserBundle\Entity;

use Symfony\Component\Form\Form;

use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;

class GroupForm extends Form
{
    protected function configure()	
    {	 
	    $this->add(new TextField('name', array('max_length' => 30,)));
        $this->add(new TextField('description', array('max_length' => 300,)));
        $form->add(new CollectionField('members_id', array('m_id' => new TextField(),)));		   
    }
}
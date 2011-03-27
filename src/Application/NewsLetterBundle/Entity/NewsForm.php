<?php
namespace Application\NewsLetterBundle\Entity;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;


class NewsForm extends Form
{
    protected function configure()	
    {	 
        $this->add(new TextField('mail', array('max_length' => 30,)));
                
    }
}
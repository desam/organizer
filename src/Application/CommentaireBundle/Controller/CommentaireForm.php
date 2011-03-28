<?php 
namespace Application\CommentaireBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Symfony\Component\Form\EmailField;
use Symfony\Component\Form\CheckboxField;
use Symfony\Component\Form\ChoiceField;
use Application\ConnexionBundle\Controller\ConnexionController;

class CommentaireForm extends Form
{
    protected function configure()
    {
        $this->add(new TextField('titre', array(
            'max_length' => 100)));
        $this->add(new TextareaField('message',array(
            'required' => false,)));
    }
}
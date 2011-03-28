<?php 
namespace Application\ArticlesBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Symfony\Component\Form\EmailField;
use Symfony\Component\Form\CheckboxField;
use Symfony\Component\Form\ChoiceField;
use Application\ConnexionBundle\Controller\ConnexionController;
use Application\ArticlesBundle\Controller\AddArticleForm;

class EditArticleForm extends Form
{
    protected function configure()
    {
		//TODO côté serveur récupérer la date de création de l'article 
        $this->add(new TextField('titre', array(
            'max_length' => 100
        )));
        
        $this->add(new TextareaField('description',array(
            'required' => false,)));
            
		$this->add(new TextareaField('corps',array(
            'required' => false,)));
        
        // Récupère l'objet xml
        $arrayxml = AddArticleForm::getCategories();
        $liste = array();
        
        // popule le tableau à une dimension à partir de l'objet XML
        for ($i=0; $i<count($arrayxml); $i++) {
          $chaine = $arrayxml[0]->categorie[$i];
          $liste[$i] = "".$chaine;
        }
        
        $this->add(new ChoiceField('categories', array("choices"=> $liste))); 
        
    }

}
<?php 
namespace Application\ArticlesBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Symfony\Component\Form\EmailField;
use Symfony\Component\Form\CheckboxField;
use Symfony\Component\Form\ChoiceField;
use Application\ConnexionBundle\Controller\ConnexionController;

class AddArticleForm extends Form
{
    protected function configure()
    {
		//TODO côté serveur récupérer la date de création de l'article 
        $this->add(new TextField('Titre', array(
            'max_length' => 100 )));
        $this->add(new TextareaField('description'));
		$this->add(new TextareaField('corps'));
        
        // Récupère l'objet xml
        $arrayxml = $this->getCategories();
        $liste = array();
        
        // popule le tableau à une dimension à partir de l'objet XML
        for ($i=0; $i<count($arrayxml); $i++) {
          $chaine = $arrayxml[0]->categorie[$i];
          $liste[$i] = "".$chaine;
        }
        
        $this->add(new ChoiceField('categories', array("choices"=> $liste))); 
    }

    public static function getCategories()
    {
        $query = 'for $Y in document("/db/Organizer/categories.xml")/categories return $Y';
        $connexion = new ConnexionController();
        $result = $connexion->simpleexecuteAction($query);
        $xml = simplexml_load_string($result["XML"]);         
        return $xml;
    }
}
<?php 
namespace Application\ArticlesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\ConnexionBundle\Controller\eXist;
use Application\ConnexionBundle\Controller\ConnexionController;

class ArticlesController extends Controller
{
    public function indexAction()
    {
    
    }

    public function getArticlesAction()
    {    
        $resultat = null;
        $query ='<articles>
                 {
                 for $a in document("/db/Organizer/articles.xml")//article
                 let $cat:= $a/@refc
                 let $user:= $a/@refu
                 let $c:= document("/db/Organizer/categories.xml")//categorie[@idc=$cat]
                 let $u:= document("/db/Organizer/users.xml")//user[@id=$user]
                 return 
                 <article>
                    <idarticle>{string($a/@id)}</idarticle>
                    <titre>{string($a/titre)}</titre>
                    <description>{string($a/description)}</description>
                    <datepublication>{string($a/datepublication)}</datepublication>   
                    <user>{string($u/surname)}
                          &#160;
                          {string($u/firstname)}</user>                    
                    <categorie>{string($c)}</categorie>
                 </article>
                 }
                 </articles>';
        $connexion = new ConnexionController();
        $resultat = $connexion->simpleexecuteAction($query);
        $arrayxml = simplexml_load_string($resultat["XML"]);
        return $this->render('ArticlesBundle:Articles:articles.twig.html', array('articles' =>$arrayxml));
    }
    
    public function editArticleAction($ida)
    {
        $articleRequest = new ArticleRequest();
		$form = EditArticleForm::create($this->get('form.context'),'EditArticle');
        
        if('POST' === $this->get('request')->getMethod()) {
            $form->bind($this->get('request'), $articleRequest);

            if ($form->isValid()) {
                $articleRequest->send();
                $node = $this->getArticleFromForm($ida);
                $this->updateArticleAction($ida,$node);
                return $this->redirect('../index');
            }
       }
       else { // GET
            $hash = $this->getArticleFromDBAction($ida);
            $art = $articleRequest->toFillArticle($hash);
            $form->bind($this->get('request'), $art);
       }
		return $this->render('ArticlesBundle:Articles:editArticle.twig.html', array(
			'form' => $form));
    }
    
    public function getArticleFromDBAction($ida)
    {   
        $query ='for $a in document("/db/Organizer/articles.xml")//article[@id='.$ida.']
                  return
                  <article>
                      <titre>{string($a/titre)}</titre>
                      <description>{string($a/description)} </description>
                      <corps>{string($a/corps)}</corps>
                      <datepublication>{string($a/datepublication)}</datepublication>
                      <categorie>{string($a/@refc)}</categorie>
                      <user>{string($a/@refu)}</user>
                      <idarticle>{string($a/@id)}</idarticle>
                  </article>';
      
        $conn = new ConnexionController();
        $resultat = $conn->simpleexecuteAction($query);
        $xml = simplexml_load_string($resultat["XML"]); 
       
        return $xml;
    }
    
    public function getArticleFromForm($ida)
    {
        $article = $_POST['EditArticle'];
        
        if(!empty($_POST['EditArticle'])&&!empty($article['Titre'])
            &&!empty($article['description'])&&!empty($article['corps']))
        {
            $datep = date("Y-m-d : H:i:s", time());
            $titre = $article['Titre'];
            $description = htmlentities($article['description']);
            $corps = htmlentities($article['corps']);
            $idcategorie = $article['categories'];
            
            $nodearticle='<article id="'.$ida.'" refc="'.$idcategorie.'" refu="1">
                          <datepublication>'.$datep.'</datepublication>
                          <titre>'.$titre.'</titre>
                          <description>'.$description.'</description>
                          <corps>'.$corps.'</corps>
                          </article>';
                          
            return $nodearticle;
         }
    }
        
    public function updateArticleAction($ida,$node)
    {
        $query='update replace document("/db/Organizer/articles.xml")//article[@id = "'. $ida.'"]
                with '. $node .'';
        
        $conn = new ConnexionController();
        $result = $conn->simpleexecuteAction($query);
    }
    
    public function deleteAction($ida)
    {
        $query = 'update delete document("/db/Organizer/articles.xml")//article[@id="'.$ida.'"]';  
        
        $conn = new ConnexionController();
        $result = $conn->simpleexecuteAction($query);
        return $this->redirect('../index');
    }
    
    public function viewArticleAction($ida)
    {
        $connexion = new ConnexionController();
        
        $query ='for $a in document("/db/Organizer/articles.xml")//article[@id='.$ida.'] 
                 let $cat:= $a/@refc
                 let $user:= $a/@refu
                 let $c:= document("/db/Organizer/categories.xml")//categorie[@idc=$cat]
                 let $u:= document("/db/Organizer/users.xml")//user[@id=$user]
                 return
                 <article>
                      <idarticle>{string($a/@id)}</idarticle>
                      <titre>{string($a/titre)}</titre>
                      <description>{string($a/description)}</description>
                      <corps>{string($a/corps)}</corps>
                      <categorie>{string($c)}</categorie>
                      <user>
                          {string($u/surname)}
                          &#160;
                          {string($u/firstname)}
                      </user>
                  </article>';
         
        $query2 ='<commentaires>
                  {
                    for $a in document("/db/Organizer/commentaires.xml")//commentaire[@refa='.$ida.']
                    let $idu:=$a/@refu
                    let $u:= document("/db/Organizer/users.xml")//user[@id=$idu]
                    return
                    <commentaire>
                        <id>{string($a/@id)}</id>
                        <titre>{string($a/titre)}</titre>
                        <message>{string($a/message)}</message>
                        <datepublication>{string($a/datepublication)}</datepublication>
                        <auteur>{string($u/surname)} &#160; {string($u/firstname)} </auteur>
                    </commentaire>
                  }
                  </commentaires>';
                  
        $resultat = $connexion->simpleexecuteAction($query);
        $resultat2 = $connexion->simpleexecuteAction($query2);
        $arrayxml = simplexml_load_string($resultat["XML"]);         
        $arrayxml2 = simplexml_load_string($resultat2["XML"]);

        return $this->render('ArticlesBundle:Articles:viewarticle.twig.html', array('article' =>$arrayxml,
                                                                                    'commentaires' =>$arrayxml2));           
    }
    
    
     public function addArticleAction()
    {
		$articleRequest = new ArticleRequest();
		$form = AddArticleForm::create($this->get('form.context'),'AddArticle');
        
        if('POST' === $this->get('request')->getMethod()) 
        {
            $form->bind($this->get('request'), $articleRequest);
            
            if ($form->isValid())
            {
                $articleRequest->send();
                $this->createArticleAction();
                return $this->redirect('index');
            }
        }
		// Display the form with the values in $contactRequest
		return $this->render('ArticlesBundle:Articles:addArticle.twig.html', array(
			'form' => $form));
    }
    
    public function getIdMaxArticle()
    {
        $query = 'for $a in document("/db/Organizer/articles.xml")/articles
                  return
                  <max>
                    {max($a/article/@id)}
                  </max>';
        $connexion = new ConnexionController();
        $result = $connexion->simpleexecuteAction($query);          

        $xml = simplexml_load_string($result["XML"]); 

        return $xml;
    }
    
    public function createArticleAction()
    {
        if(!empty($_POST['AddArticle'])) {
            $news = $_POST['AddArticle'];
            if(!empty($news['Titre'])&&!empty($news['description'])&&!empty($news['corps'])){
                $datep = date("Y-m-d : H:i:s", time());
                $titre = htmlentities($news['Titre']);
                $description = htmlentities($news['description']);
                $corps = htmlentities($news['corps']) ;
                $idcategorie = $news['categories'];
                $idarticle = $this->getIdMaxArticle() + 1;
                
                $query ='update insert 
                <article id="'.$idarticle.'" refc="'.$idcategorie.'" refu="1">
                <datepublication>'.$datep.'</datepublication>
                <titre>'.$titre.'</titre>
                <description>'.$description.'</description>
                <corps>'.$corps.'</corps>
                </article> into document("/db/Organizer/articles.xml")/articles';

                $connexion = new ConnexionController();
                $connexion->simpleexecuteAction($query);
            }
        }
        else 
            echo "L'un des champs fournis est vide";
    }
    
    public function getInfoArticle($ida)
    {
        $query = 'for $a in document("/db/Organizer/articles.xml")//article[@id=1] 
                  let $cat:= $a/@refc
                  let $user:= $a/@refu
                  let $c:= document("/db/Organizer/categories.xml")//categorie[@idc=$cat]
                  let $u:= document("/db/Organizer/users.xml")//user[@id=$user]
                  return
                  <article>
                      <idarticle>{string($a/@id)}</idarticle>
                      <titre>{string($a/titre)}</titre>
                      <description>{string($a/description)}</description>
                      <corps>{string($a/corps)}</corps>
                      <categorie>{string($c)}</categorie>
                      <user>
                          {string($u/surname)}
                          &#160;
                          {string($u/firstname)}
                      </user>
                  </article>';
                  
        $result = $connexion->simpleexecuteAction($query);
        $arrayxml = simplexml_load_string($resultat["XML"]);         
        
        $titre = $arrayxml[0]->titre;
        $categories = $arrayxml[0]->categorie;
        $ida = $arrayxml[0]->idarticle;
        $user = $arrayxml[0]->user;
        
        $article = array("ida" => $ida,
                         "user" => $idu,
                         "titre" =>$titre,
                         "categories" => $categories);
        
    }

        
    
}
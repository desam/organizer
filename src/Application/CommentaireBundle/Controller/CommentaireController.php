<?php 
namespace Application\CommentaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\ConnexionBundle\Controller\ConnexionController;

class CommentaireController extends Controller
{
    public function indexAction($ida)
    {
		$commentaireRequest = new CommentaireRequest();
		$form = CommentaireForm::create($this->get('form.context'),'AddCommentaire');
        
        if('POST' === $this->get('request')->getMethod()) 
        {
            $form->bind($this->get('request'), $commentaireRequest);
            
            if ($form->isValid())
            {
                if(!empty($_POST['AddCommentaire'])) {
                    $com = $_POST['AddCommentaire'];
                    $com['iduser']= 1; // r�cup�rer l'iduser depuis la session
                    $com['idarticle'] = $ida;
                    if(!empty($com['titre'])&&!empty($com['message'])&&!empty($com['idarticle'])
                    &&!empty($com['iduser'])) { 
                        $commentaireRequest->send();
                        $this->createCommentaireAction($com, $ida);
                        return $this->redirect('../../articles/index');
                    }
                    else
                        echo '<script>alert("Veuillez remplir tous les champs !")</script>';
                }
            }
        }
		// Display the form with the values in $commentaireRequest
		return $this->render('CommentaireBundle:Commentaires:ajouterCommentaire.twig.html', array(
        'form' => $form));
    }
    
    public function getIdMaxCommentaire()
    {
        $query = 'for $a in document("/db/Organizer/commentaires.xml")/commentaires
                  return
                  <max>
                    {max($a/commentaire/@id)}
                  </max>';
        $connexion = new ConnexionController();
        $result = $connexion->simpleexecuteAction($query);          

        $xml = simplexml_load_string($result["XML"]); 

        return $xml;
    }
    
    public function createCommentaireAction($com,$ida)
    {
        $datep = date("Y-m-d : H:i:s", time());
        $titre = htmlentities($com['titre']);
        $message = htmlentities($com['message']);
        $idc = $this->getIdMaxCommentaire() + 1;
        $idu = $com['iduser'];
        $ida = $com['idarticle'];
        
        $query ='update insert 
                <commentaire id="'.$idc.'" refa="'.$ida.'" refu="'.$idu.'">
                    <datepublication>'.$datep.'</datepublication>
                    <titre>'.$titre.'</titre>
                    <message>'.$message.'</message>
                </commentaire> into document("/db/Organizer/commentaires.xml")/commentaires';
 
        $connexion = new ConnexionController();
        $connexion->simpleexecuteAction($query);
    }
    
    public function getCommentairesAction($idu)
    {
        $query='<commentaires>
                {
                for $a in document("/db/Organizer/commentaires.xml")//commentaire[@refu='.$idu.']
                let $idu:=$a/@refu
                let $ida:=$a/@refa
                let $u:= document("/db/Organizer/users.xml")//user[@id=$idu]
                let $n:= document("/db/Organizer/articles.xml")//article[@id=$ida]
                return
                <commentaire>
                <id>{string($a/@id)}</id>
                <article>
                    <id>{string($n/@id)}</id>
                    <titre>{string($n/titre)}</titre>
                </article>
                <titre>{string($a/titre)}</titre>
                <message>{string($a/message)}</message>
                <datepublication>{string($a/datepublication)}</datepublication>
                <auteur>
                    <name>{string($u/surname)} &#160; {string($u/firstname)}</name>
                    <idauteur>{string($u/@id)}</idauteur>
                </auteur>
                </commentaire>
                }
                </commentaires>';
                
        $conn = new ConnexionController();
        $resultat = $conn->simpleexecuteAction($query);
        $commentaires = simplexml_load_string($resultat["XML"]); 
       
        return $this->render('CommentaireBundle:Commentaires:Commentaires.twig.html', array(
        'commentaires' => $commentaires));
    }
    
    public function deleteCommentaireAction($id,$idu)
    {
        $query = 'update delete document("/db/Organizer/commentaires.xml")//commentaire[@id="'.$id.'"]';  
        
        $conn = new ConnexionController();
        $result = $conn->simpleexecuteAction($query);
        return $this->redirect('../../view/'.$idu);
    }

  
}
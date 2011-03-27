<?php 
namespace Application\ConnexionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\ConnexionBundle\Controller\eXist;
use XSLTProcessor;
use domDocument;

class ConnexionController extends Controller
{
    public function executeQueryAction($query, $xslurl)
    {
    	$resultat = null;
        $db = new eXist();

		// Connexion
		$db->connect() or die ($db->getError());

		// Definition et Execution de la requete 
		$result = $db->xquery($query) or die ($db->getError());

        // Transformation XSLT 
        $xslt = new XSLTProcessor();

        // Chargement du fichier XML
        $xml = new DOMDocument();
        $chaine = $result["XML"];
        $xml -> loadXML($chaine);

        // Chargement du fichier XSL
        $xsl = new domDocument();
        $xsl -> load($xslurl);
      
        // Import de la feuille XSL
        $xslt -> importStylesheet($xsl);

        // Transformation et affichage du résultat
        $resultat = $xslt->transformToXml($xml);

        return $resultat;
    }
    
    public function simpleexecuteAction($query)
    {
        $db = new eXist();

		// Connexion
		$db->connect() or die ($db->getError());

		// Définition et Exécution de la requête 
		$result = $db->xquery($query);
        
        return $result;
    }

}
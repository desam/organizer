<?php 
 if(isset($_POST['searchterm']))
 { 
    $searchterm = addslashes($_POST['searchterm']);

    $query="for $a in document('/db/Organizer/articles.xml')//article
            WHERE contains($a/description,".$searchterm.")
            OR contains($a/corps,".$searchterm.")
            OR contains($a/titre,".$searchterm.")
            return
            $a";
    
    $connexion = new ConnexionController();
    $resultat = $connexion->simpleexecuteAction($query);
    
    $arrayxml = simplexml_load_string($resultat["XML"]);
    
       echo '<ul>
                <li id="1">ACME Inc</li>
                <li id="2">Scriptaculous</li>
             </ul>';
    }
    else
    {
       echo '<ul>
                <li>Eeeee</li>
                <li>dddd</li>
             </ul>';
    }

?>
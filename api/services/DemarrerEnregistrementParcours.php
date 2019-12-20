<?php
$dao = new DAO();
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

//http://localhost/ws-php-miszczuk/tracegps/api/DemarrerEnregistrementParcours?pseudo=callisto&mdp=13e3668bbee30b004380052b086457b014504b3e&lang=xml
// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else
{
    // Les paramètres doivent être présents
    if ( $pseudo == "" || $mdpSha1 == "" )
    {	$msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {	
        if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 ) 
        {
    		$msg = "Erreur : authentification incorrecte.";
    		$code_reponse = 401;
        }
    	else 
    	{
    	    
    	}
    }
}
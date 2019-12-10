<?php
http://localhost/ws-php-miszczuk/tracegps/api/GetTousLesUtilisateurs?pseudo=callisto&lang=xml
// connexion du serveur web à la base MySQL
$dao = new DAO();
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// initialisation du nombre de réponses

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
$code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "")
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
    }
}
// ferme la connexion à MySQL :
unset($dao);
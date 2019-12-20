<?php
global $ADR_MAIL_EMETTEUR, $ADR_SERVICE_WEB;
$dao = new DAO();
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
// initialisation du nombre de réponses

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	
    $msg = "Erreur : méthode HTTP incorrecte.";
    $code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "")
    {	
        $msg = "Erreur : données incomplètes.";
        $code_reponse = 400;
    }
    else
    {
        $voyelles = "aeiouy";
        $consonnnes = "zrtpqsdfghjklmwxcvbn";
        $mdp = "";
        for($i = 0;$i<4;$i++)
        {
            $mdp = $mdp.substr($voyelles,random_int(0,5),1);
            $mdp = $mdp.substr($consonnnes,random_int(0,strlen($consonnnes)-1),1);
            //$mdp = $mdp.substr($caractere,random_int(0,strlen($caractere)-1),1);
        }
        $demandeur = $dao->getUnUtilisateur($pseudo);
        $adrMailDemandeur = $demandeur->getAdrMail();
        $sujetMail = "Demande de réinitialisation de mot de passe";
        $contenuMail = "Cher ou chère " . $pseudo . "\n\n";
        $contenuMail .= "Vous avez demandé une réinitialisation de votre mot de passe.\n";
        $contenuMail .= "Votre nouveau mot de passe est : ".$mdp.".\n\n";
        $contenuMail .= "Cordialement.\n";
        $contenuMail .= "L'administrateur du système TraceGPS";
        $ok = Outils::envoyerMail($adrMailDemandeur, $sujetMail, $contenuMail, $ADR_MAIL_EMETTEUR);
        if ( ! $ok )
        {
            $message = "Erreur : l'envoi du courriel au demandeur a rencontré un problème.";
            $code_reponse = 500;
            echo $message;
        }
        else
        {
            $message = "Vous allez recevoir un courriel avec votre nouveau mot de passe";
            $code_reponse = 200;
            echo $message;
        }
    }
    
}
// ferme la connexion à MySQL :
unset($dao);

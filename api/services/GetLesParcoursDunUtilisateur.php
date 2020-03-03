<?php
// Service Web qui retourne les traces d'un utilisateur
// Service non terminé car il ne prend pas en compte le paramètre pseudoConsulte
//      http://localhost/ws-php-miszczuk/tracegps/api/GetLesParcoursDunUtilisateur?pseudo=callisto&mdp=13e3668bbee30b004380052b086457b014504b3e&pseudoConsulte=oxygen&lang=xml
// connexion du serveur web à la base MySQL
$dao = new DAO();
// Récupération des données transmises
$pseudo = ( empty($this->request['pseudo'])) ? "" : $this->request['pseudo'];
$mdpSha1 = ( empty($this->request['mdp'])) ? "" : $this->request['mdp'];
$lang = ( empty($this->request['lang'])) ? "" : $this->request['lang'];
$pseudoConsulte = ( empty($this->request['pseudoConsulte'])) ? "" : $this->request['pseudoConsulte'];

// "xml" par défaut si le paramètre lang est absent ou incorrect
if ($lang != "json") $lang = "xml";

// initialisation du nombre de réponses
$nbReponses = 0;
$lesTraces = array();

// La méthode HTTP utilisée doit être GET
if ($this->getMethodeRequete() != "GET")
{	$msg = "Erreur : méthode HTTP incorrecte.";
$code_reponse = 406;
}
else {
    // Les paramètres doivent être présents
    if ( $pseudo == "" || $mdpSha1 == "" )
    {	$msg = "Erreur : données incomplètes.";
    $code_reponse = 400;
    }
    else
    {	if ( $dao->getNiveauConnexion($pseudo, $mdpSha1) == 0 ) {
        $msg = "Erreur : authentification incorrecte.";
        $code_reponse = 401;
    }
    else
    {

      $utilisateurConsulte = $dao->getUnUtilisateur($pseudoConsulte);
      $utilisateurConsultant = $dao->getUnUtilisateur($pseudo);
      $lesUtilisateursConsultes = $dao->getLesUtilisateursAutorises($utilisateurConsultant->getId());
      foreach ($lesUtilisateursConsultes as $unUtilisateur){
        echo $unUtilisateur->getId();
      }

      // récupération de la liste des utilisateurs à l'aide de la méthode getTousLesUtilisateurs de la classe DAO
      $utilisateurConnecte = $dao->getUnUtilisateur($pseudo);
      $lesTraces = $dao->getLesTraces($utilisateurConnecte->getId());

      // mémorisation du nombre d'utilisateurs
      $nbReponses = sizeof($lesTraces);

      if ($nbReponses == 0) {
          $msg = "Aucun parcours.";
          $code_reponse = 200;
      }
      else {
          $msg = $nbReponses . " parcours.";
          $code_reponse = 200;
      }
    }
  }
}
// ferme la connexion à MySQL :
unset($dao);

if ($lang == "xml") {
    $content_type = "application/xml; charset=utf-8";      // indique le format XML pour la réponse
    $donnees = creerFluxXML($msg, $lesTraces);
}
else {
    $content_type = "application/json; charset=utf-8";      // indique le format Json pour la réponse
    $donnees = creerFluxJSON($msg, $lesTraces);
}

// envoi de la réponse HTTP
$this->envoyerReponse($code_reponse, $content_type, $donnees);

// fin du programme (pour ne pas enchainer sur les 2 fonctions qui suivent)
exit;

function creerFluxXML($msg, $lesTraces)
{
  $doc = new DOMDocument();

	// specifie la version et le type d'encodage
	$doc->version = '1.0';
	$doc->encoding = 'UTF-8';

	// crée un commentaire et l'encode en UTF-8
	$elt_commentaire = $doc->createComment('Service web GetTousLesUtilisateurs - BTS SIO - Lycée De La Salle - Rennes');
	// place ce commentaire à la racine du document XML
	$doc->appendChild($elt_commentaire);

	// crée l'élément 'data' à la racine du document XML
	$elt_data = $doc->createElement('data');
	$doc->appendChild($elt_data);

	// place l'élément 'reponse' dans l'élément 'data'
	$elt_reponse = $doc->createElement('reponse', $msg);
	$elt_data->appendChild($elt_reponse);

	// traitement des utilisateurs
	if (sizeof($lesTraces) > 0) {
	    // place l'élément 'donnees' dans l'élément 'data'
	    $elt_donnees = $doc->createElement('donnees');
	    $elt_data->appendChild($elt_donnees);

	    // place l'élément 'lesUtilisateurs' dans l'élément 'donnees'
	    $elt_lesTraces = $doc->createElement('lesTraces');
	    $elt_donnees->appendChild($elt_lesTraces);

	    foreach ($lesTraces as $uneTrace)
		{
		    // crée un élément vide 'trace'
		    $elt_trace = $doc->createElement('trace');
		    // place l'élément 'trace' dans l'élément 'lesTraces'
		    $elt_lesTraces->appendChild($elt_trace);

		    // crée les éléments enfants de l'élément 'trace'
		    $elt_id = $doc->createElement('id', $uneTrace->getId());
		    $elt_trace->appendChild($elt_id);
        $elt_dateDebut = $doc->createElement('dateDebut', $uneTrace->getDateHeureDebut());
		    $elt_trace->appendChild($elt_dateDebut);
        if(empty($uneTrace->getDateHeureFin()) == false)
        {
          $elt_dateFin = $doc->createElement('dateFin', $uneTrace->getDateHeureFin());
  		    $elt_trace->appendChild($elt_dateFin);
        }
        $elt_terminee = $doc->createElement('terminee', $uneTrace->getTerminee());
		    $elt_trace->appendChild($elt_terminee);
        $elt_idUtilisateur = $doc->createElement('idUtilisateur', $uneTrace->getIdUtilisateur());
		    $elt_trace->appendChild($elt_idUtilisateur);
		}
	}
	// Mise en forme finale
	$doc->formatOutput = true;

	// renvoie le contenu XML
	return $doc->saveXML();
}

function creerFluxJSON($msg, $lesTraces)
{
    /* Exemple de code JSON
        {
            "data": {
                "reponse": "2 utilisateur(s).",
                "donnees": {
                    "lesUtilisateurs": [
                        {
                            "id": "2",
                            "pseudo": "callisto",
                            "adrMail": "delasalle.sio.eleves@gmail.com",
                            "numTel": "22.33.44.55.66",
                            "niveau": "1",
                            "dateCreation": "2018-08-12 19:45:23",
                            "nbTraces": "2",
                            "dateDerniereTrace": "2018-01-19 13:08:48"
                        },
                        {
                            "id": "3",
                            "pseudo": "europa",
                            "adrMail": "delasalle.sio.eleves@gmail.com",
                            "numTel": "22.33.44.55.66",
                            "niveau": "1",
                            "dateCreation": "2018-08-12 19:45:23",
                            "nbTraces": "0"
                        }
                    ]
                }
            }
        }
     */


    if (sizeof($lesTraces) == 0) {
        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg];
    }
    else {
        // construction d'un tableau contenant les traces
        $lesObjetsDuTableau = array();
        foreach ($lesTraces as $uneTrace)
        {	// crée une ligne dans le tableau
            $unObjetTrace = array();
            $unObjetTrace["id"] = $uneTrace->getId();
            $unObjetTrace["dateDebut"] = $uneTrace->getDateHeureDebut();
            if(empty($uneTrace->getDateHeureFin()) == false)
            {
              $unObjetTrace["dateFin"] = $uneTrace->getDateHeureFin();
            }
            $unObjetTrace["terminee"] = $uneTrace->getTerminee();
            $unObjetTrace["idUtilisateur"] = $uneTrace->getIdUtilisateur();
            $lesObjetsDuTableau[] = $unObjetTrace;
        }
        // construction de l'élément "lesTraces"
        $elt_trace = ["lesTraces" => $lesObjetsDuTableau];

        // construction de l'élément "data"
        $elt_data = ["reponse" => $msg, "donnees" => $elt_trace];
    }

    // construction de la racine
    $elt_racine = ["data" => $elt_data];

    // retourne le contenu JSON (l'option JSON_PRETTY_PRINT gère les sauts de ligne et l'indentation)
    return json_encode($elt_racine, JSON_PRETTY_PRINT);
}

<?php
// Projet TraceGPS
// fichier : modele/Trace.class.php
// Rôle : la classe Trace représente une trace ou un parcours
// Dernière mise à jour : 9/9/2019 par JM CARTRON

include_once ('PointDeTrace.class.php');

class Trace
{
    

// ATTENTION : on ne met pas de balise de fin de script pour ne pas prendre le risque
// d'enregistrer d'espaces après la balise de fin de script !!!!!!!!!!!!
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------- Attributs privés de la classe -------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    private $id;				// identifiant de la trace
    private $dateHeureDebut;		// date et heure de début
    private $dateHeureFin;		// date et heure de fin
    private $terminee;			// true si la trace est terminée, false sinon
    private $idUtilisateur;		// identifiant de l'utilisateur ayant créé la trace
    private $lesPointsDeTrace;		// la collection (array) des objets PointDeTrace formant la trace
    
    // ------------------------------------------------------------------------------------------------------
    // ----------------------------------------- Constructeur -----------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function __construct($unId, $uneDateHeureDebut, $uneDateHeureFin, $terminee, $unIdUtilisateur) {
        
        $this->lesPointsDeTrace = array();
        $this->id = $unId;
        $this->dateHeureDebut = $uneDateHeureDebut;
        $this->dateHeureFin = $uneDateHeureFin;
        $this->terminee = $terminee;
        $this->idUtilisateur = $unIdUtilisateur;
    }
    
    // ------------------------------------------------------------------------------------------------------
    // ---------------------------------------- Getters et Setters ------------------------------------------
    // ------------------------------------------------------------------------------------------------------
    
    public function getId() {return $this->id;}
    public function setId($unId) {$this->id = $unId;}
    
    public function getDateHeureDebut() {return $this->dateHeureDebut;}
    public function setDateHeureDebut($uneDateHeureDebut) {$this->dateHeureDebut = $uneDateHeureDebut;}
    
    public function getDateHeureFin() {return $this->dateHeureFin;}
    public function setDateHeureFin($uneDateHeureFin) {$this->dateHeureFin= $uneDateHeureFin;}
    
    public function getTerminee() {return $this->terminee;}
    public function setTerminee($terminee) {$this->terminee = $terminee;}
    
    public function getIdUtilisateur() {return $this->idUtilisateur;}
    public function setIdUtilisateur($unIdUtilisateur) {$this->idUtilisateur = $unIdUtilisateur;}
    
    public function getLesPointsDeTrace() {return $this->lesPointsDeTrace;}
    public function setLesPointsDeTrace($lesPointsDeTrace) {$this->lesPointsDeTrace = $lesPointsDeTrace;}
    
    public function toString() {
        $msg = "Id : " . $this->getId() . "<br>";
        $msg .= "Utilisateur : " . $this->getIdUtilisateur() . "<br>";
        if ($this->getDateHeureDebut() != null) {
            $msg .= "Heure de début : " . $this->getDateHeureDebut() . "<br>";
        }
        if ($this->getTerminee()) {
            $msg .= "Terminée : Oui  <br>";
        }
        else {
            $msg .= "Terminée : Non  <br>";
        }
        $msg .= "Nombre de points : " . $this->getNombrePoints() . "<br>";
        if ($this->getNombrePoints() > 0) {
            if ($this->getDateHeureFin() != null) {
                $msg .= "Heure de fin : " . $this->getDateHeureFin() . "<br>";
            }
            $msg .= "Durée en secondes : " . $this->getDureeEnSecondes() . "<br>";
            $msg .= "Durée totale : " . $this->getDureeTotale() . "<br>";
            $msg .= "Distance totale en Km : " . $this->getDistanceTotale() . "<br>";
            $msg .= "Dénivelé en m : " . $this->getDenivele() . "<br>";
            $msg .= "Dénivelé positif en m : " . $this->getDenivelePositif() . "<br>";
            $msg .= "Dénivelé négatif en m : " . $this->getDeniveleNegatif() . "<br>";
            $msg .= "Vitesse moyenne en Km/h : " . $this->getVitesseMoyenne() . "<br>";
            $msg .= "Centre du parcours : " . "<br>";
            $msg .= "   - Latitude : " . $this->getCentre()->getLatitude() . "<br>";
            $msg .= "   - Longitude : "  . $this->getCentre()->getLongitude() . "<br>";
            $msg .= "   - Altitude : " . $this->getCentre()->getAltitude() . "<br>";
        }
        return $msg;
    }
    
    function getNombrePoints()
    {
        return sizeof($this->lesPointsDeTrace);
    }
    
    function getCentre()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return null;
        }
        
        $lePremierPoint = $this->lesPointsDeTrace[0];
        
        $leCentre = new Point(0, 0, 0);
        $latitudeMin = $lePremierPoint->getLatitude();
        $latitudeMax = $lePremierPoint->getLatitude();
        $longitudeMin = $lePremierPoint->getLongitude();
        $longitudeMax = $lePremierPoint->getLongitude();
        
        for ($i = 0; $i < sizeof($this->lesPointsDeTrace) ; $i++) {
           $lePoint = $this->lesPointsDeTrace[$i];
           if ($lePoint->getLatitude() < $latitudeMin)
           {
               $latitudeMin = $lePoint->getLatitude();
           }
           
           if ($lePoint->getLatitude() > $latitudeMax)
           {
               $latitudeMax = $lePoint->getLatitude();
           }
           
           if ($lePoint->getLongitude() < $longitudeMin)
           {
               $longitudeMin = $lePoint->getLongitude();
           }
           
           if ($lePoint->getLongitude() > $longitudeMax)
           {
               $longitudeMax = $lePoint->getLongitude();
           }
        }
        $leCentre->setLatitude(($latitudeMax + $latitudeMin)/2);
        $leCentre->setLongitude(($longitudeMax + $longitudeMin)/2);
        return $leCentre;
    }
    
    function getDenivele()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        else 
        {
            $lePremierPoint = $this->lesPointsDeTrace[0];
            $altitudeMin = $lePremierPoint->getAltitude();
            $altitudeMax = $lePremierPoint->getAltitude();
            foreach ($this->lesPointsDeTrace as $unPoint)
            {
               if($unPoint->getAltitude() < $altitudeMin )
               {
                   $altitudeMin = $unPoint->getAltitude();
               }
            
               if ($unPoint->getAltitude() > $altitudeMax )
               {
                   $altitudeMax = $unPoint->getAltitude();
               }
            }
            $ecart = $altitudeMax - $altitudeMin;
            return $ecart;
        }
    }
    
    function getDureeEnSecondes()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        else
        {
            $leDernierPoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) -1];
             return $leDernierPoint->getTempsCumule();
        }
    }
    
    function getDureeTotale()
    {
        $secondes = $this->getDureeEnSecondes();
        $minutes = 0;
        $heures = 0;
        while($secondes >= 60)
        {
            $secondes = $secondes - 60;
            $minutes = $minutes + 1;
            if($minutes >= 60)
            {
                $minutes = $minutes - 60;
                $heures = $heures + 1;
            }
        }
        return sprintf("%02d",$heures) . ":" . sprintf("%02d",$minutes) . ":" . sprintf("%02d",$secondes);
    }
    
    function getDistanceTotale()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        else
        {
            $leDernierPoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) -1];
            return $leDernierPoint->getDistanceCumulee();
        }
    }
    
    /*function getDenivelePositif()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        else
        {
            $lePremierPoint = $this->lesPointsDeTrace[0];
            $altiPointPrecedent = $lePremierPoint->getAltitude();
            $ecartCumule = 0;
            
            for ($i = 0; $i < sizeof($this->lesPointsDeTrace) ; $i++) {
                $unPoint = $this->lesPointsDeTrace[$i];
                
                
                if ($unPoint->getAltitude() - $altiPointPrecedent > 0)
                {
                    $ecartCumule += $unPoint->getAltitude() - $altiPointPrecedent;
                }
                $altiPointPrecedent = $unPoint->getAltitude();
            }
            return $ecartCumule;
        }
    }*/
    
    function getDenivelePositif()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        else
        {
            $denivele = 0;
            for($i = 0; $i < sizeof($this->lesPointsDeTrace) - 1; $i++)
            {
                $lePoint1 = $this->lesPointsDeTrace[$i];
                $lePoint2 = $this->lesPointsDeTrace[$i + 1];
                if($lePoint2->getAltitude() > $lePoint1->getAltitude())
                {
                    $denivele += $lePoint2->getAltitude() - $lePoint1->getAltitude();
                }
            }
            return $denivele;
        }
    }
    
    function getDeniveleNegatif()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        else
        {
            $lePremierPoint = $this->lesPointsDeTrace[0];
            $altiPointPrecedent = $lePremierPoint->getAltitude();
            $ecartCumule = 0;
            
            for ($i = 0; $i < sizeof($this->lesPointsDeTrace) ; $i++) {
                $unPoint = $this->lesPointsDeTrace[$i];
                
                
                if ($unPoint->getAltitude() < $altiPointPrecedent)
                {
                    $ecartCumule += $unPoint->getAltitude() - $altiPointPrecedent;
                }
                $altiPointPrecedent = $unPoint->getAltitude();
            }
            return -$ecartCumule;
        }
    }
    
    function getVitesseMoyenne()
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            return 0;
        }
        
        $distance = $this->getDistanceTotale();
        $temps = $this->getDureeEnSecondes();
        $temps = $temps / 3600;
        return $distance / $temps;
    }
    
    function ajouterpoint($leNouveauPoint)
    {
        if (sizeof($this->lesPointsDeTrace) == 0)
        {
            $leNouveauPoint->setVitesse(0);
            $leNouveauPoint->setTempsCumule(0);
            $leNouveauPoint->setDistanceCumulee(0);
        }
        
        else
        {
            $dernierpoint = $this->lesPointsDeTrace[sizeof($this->lesPointsDeTrace) - 1];
            $distance = Point::getDistance($leNouveauPoint, $dernierpoint);
            $temps = strtotime($leNouveauPoint->getDateHeure()) - strtotime($dernierpoint->getDateHeure());
            $vitesse = $distance / ($temps / 3600);
            $leNouveauPoint->setVitesse($vitesse);
            $leNouveauPoint->setTempsCumule($temps + $dernierpoint->getTempsCumule());
            $leNouveauPoint->setDistanceCumulee($distance + $dernierpoint->getDistanceCumulee());
        }
        $this->lesPointsDeTrace[] = $leNouveauPoint;
        
    }
    
    function viderListePoints()
    {
        $this->lesPointsDeTrace = array();
    }
} // fin de la classe Trace
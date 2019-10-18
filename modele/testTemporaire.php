<?php
// connexion du serveur web à la base MySQL
include_once ('DAO.class.php');
//include_once ('_DAO.mysql.class.php');
$dao = new DAO();

$resultat = $dao->existeAdrMailUtilisateur("sdelasalle.sio.miszczuk.i@gmail.com");
echo $resultat;
?>
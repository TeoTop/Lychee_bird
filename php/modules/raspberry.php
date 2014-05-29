<?php

/**
 * @name        Raspberry Module
 * @author      Theo Chapon
 * description	Ce fichier permet de gérer les rasp de la base
 * @copyright   2014 by Philipp Maurer, Tobias Reich
 */

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');

function getRasp(){
	global $database;
	
	$result = $database->query("SELECT * FROM lychee_rasp;");
	while($row = $result->fetch_object()) {
		// Info
		$return[$row->id_rasp]['id_rasp'] = $row->id_rasp;
		$return[$row->id_rasp]['adresse'] = $row->adresse;
		$return[$row->id_rasp]['etat'] = $row->etat;
	}
	return $return;
    
}

function addRasp($adresse) {

	global $database;

    $result = $database->query("INSERT INTO lychee_rasp(adresse, etat) VALUES ('$adresse',0);");
    if (!$result) return false;

    return true;

}

function deleteRasp() {

	global $database;

    $result = $database->query("DELETE FROM lychee_rasp;");
    if (!$result) return false;

    return true;

}

?>
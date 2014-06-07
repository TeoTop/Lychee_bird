<?php

/**
 * @name        SSH Module
 * @author      Theo Chapon
 * @description	Ce fichier permet de g�rer les fonctions qui ont pour but d'envoyer des commandes aux Raspberry Pi
 * @copyright   2014 by Philipp Maurer, Tobias Reich
 */

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');

//Cette fonction permet de d�marrer/arreter l'ex�cutable ./bird situ� sur les modules cam�ra 
function turnOpenCV($address, $etat) {
	
	$result;
	
	if($adresse == 'all'){
		$raspberry = getRasp();
		if($raspberry != false){
			if($etat == 0){
				foreach($raspberry as $rasp){
					$result = stopOpenCV($rasp['adresse']);
					if($result == false){
						return false;
					}
				}
			} else {
				foreach($raspberry as $rasp){
					stopOpenCV($rasp['adresse']);
					$result = startOpenCV($rasp['adresse']);
					if($result == false){
						return false;
					}
				}
			}
		}
	} else {
		if($etat == 0){
			$result = stopOpenCV($address);
		} else {
			stopOpenCV($address);
			$result = startOpenCV($address);
		}
	}
	
	return $result;
}

//d�marre le script ./bird sur la Raspberry � l'adresse sp�cifi�e
function startOpenCV($address) {
	//connection en ssh
	$ssh = new Net_SSH2($address);
	if(!$ssh->login('pi', 'raspberry')){
		return false;
	}
	$result = $ssh->exec("./opencv/Bird > /dev/null 2>&1 &");
    return true;

}

//on stop tous les processus ./bird qui pourraient tourner
function stopOpenCV($address) {

	$ssh = new Net_SSH2($address);
	if(!$ssh->login('pi', 'raspberry')){
		return false;
	}
	$matched = array();
	do{
		$grep = $ssh->exec("ps -eaf");
		$match = preg_match('/.*?(\d+).*?Bird/', $grep,$matched);
		if($match){
			echo $ssh->exec("kill ".$matched[1]);
		}
	}while($match);
	return true;
}

//on prend une photo manuellement sur la Rasp sp�cifi�
function takePicture($rasp) {

	if($rasp == 'all'){
		$raspberry = getRasp();
		if($raspberry != false){
			foreach($raspberry as $rasp){
				$ssh = new Net_SSH2($rasp['adresse']);
				if(!$ssh->login('pi', 'raspberry')){
					return false;
				}
				$nom = $rasp["adresse"] . date("H.i.s") . ".png";
				$result = $ssh->exec("raspistill -w 640 -h 480 -o /mnt/upload/$nom -t 1");
			}
		}
	} else {
		$ssh = new Net_SSH2($rasp);
		if(!$ssh->login('pi', 'raspberry')){
			return false;
		}
		$nom = $rasp . date("H.i.s") . ".png";
		$result = $ssh->exec("raspistill -w 640 -h 480 -o /mnt/upload/$nom -t 1");
	}
    return true;
}

//cette fonction permet de r�cup�rer tous les Rasp sur le r�seau local (10.1.0.X) et de les placer dans la table de lychee_rasp
function setNetwork() {
	deleteRasp();
	$lines = array();
	$result = exec("netstat -nt",$lines);
	$o = "";
	foreach($lines as $line){
		$o .= " ".$line;
	}
	$match = array();
	$adresse = array();
	$regex = "/10\.1\.0\.\d+/";
	$matched = preg_match_all($regex,$o,$match);
	$inserted = array();
	if ($matched) {
		foreach($match[0] as $ip){
			if($ip!="10.1.0.1"){
				if(!in_array($ip, $inserted)) {
					addRasp($ip);
					$inserted[] = $ip;
				}
			}
		}
	}
	return json_encode(array($match[0],$o));
}
?>

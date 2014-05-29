<?php

/**
 * @name        Photo Module
 * @author      Philipp Maurer
 * @author      Tobias Reich
 * @author      Theo Chapon
 * @description	Ce fichier permet de gérer les photos de la base de données
 * @copyright   2014 by Philipp Maurer, Tobias Reich
 */

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');

function getPhoto($photoID, $albumID) {

	global $database;

	if (!is_numeric($photoID)) {
		$result = $database->query("SELECT COUNT(*) AS quantity FROM lychee_photos WHERE import_name = '../uploads/import/$photoID';");
		$row = $result->fetch_object();
		if ($row->quantity == 0) {
			importPhoto($photoID, 's');
		}
		if (is_file("../uploads/import/$photoID")) {
			importPhoto($photoID, 's');
		}
		$query = "SELECT * FROM lychee_photos WHERE import_name = '../uploads/import/$photoID' ORDER BY ID DESC;";
	} else {
		$query = "SELECT * FROM lychee_photos WHERE id = '$photoID';";
	}

    $result = $database->query($query);
    $return = $result->fetch_array();

    if ($albumID!='false') {

    	if ($return['album']!=0) {

    		$result = $database->query("SELECT public FROM lychee_albums WHERE id = '" . $return['album'] . "';");
    		$return_album = $result->fetch_array();
    		if ($return_album['public']=="1") $return['public'] = "2";

    	}

    	$return['original_album'] = $return['album'];
    	$return['album'] = $albumID;
    	$return['sysdate'] = date('d M. Y', strtotime($return['sysdate']));
    	if (strlen($return['takedate'])>0) $return['takedate'] = date('d M. Y', strtotime($return['takedate']));

	}

	unset($return['album_public']);

    return $return;

}

function setPhotoPublic($photoID, $url) {

	global $database;

    $result = $database->query("SELECT public FROM lychee_photos WHERE id = '$photoID';");
    $row = $result->fetch_object();
    if ($row->public == 0){
        $public = 1;
    } else {
        $public = 0;
    }
    $result = $database->query("UPDATE lychee_photos SET public = '$public' WHERE id = '$photoID';");

    if (!$result) return false;
    return true;

}

function setPhotoStar($photoID) {

	global $database;

    $result = $database->query("SELECT star FROM lychee_photos WHERE id = '$photoID';");
    $row = $result->fetch_object();
    if ($row->star == 0) {
        $star = 1;
    } else {
        $star = 0;
    }
    $result = $database->query("UPDATE lychee_photos SET star = '$star' WHERE id = '$photoID';");
    return true;

}

function setAlbum($photoID, $newAlbum) {

	global $database;

    $result = $database->query("UPDATE lychee_photos SET album = '$newAlbum' WHERE id = '$photoID';");

    if (!$result) return false;
    else return true;

}

function setPhotoTitle($photoID, $title) {

	global $database;

    if (strlen($title)>30) return false;
    $result = $database->query("UPDATE lychee_photos SET title = '$title' WHERE id = '$photoID';");

    if (!$result) return false;
    else return true;

}

function setPhotoDescription($photoID, $description) {

	global $database;

    $description = htmlentities($description);
    if (strlen($description)>800) return false;
    $result = $database->query("UPDATE lychee_photos SET description = '$description' WHERE id = '$photoID';");

    if (!$result) return false;
    return true;

}

function setPhotoColor($photoID, $color) {

	global $database;
	
	$photoID = floor($photoID/10)*10;
	$photoID1 = $photoID + 1;
	$photoID2 = $photoID + 2;
	$color = htmlentities($color);
	$album = idColorAlbum($color);
	
    if (strlen($color)>20) return false;
	$result = $database->query("UPDATE lychee_photos SET color = '$color' WHERE id = '$photoID' OR id = '$photoID1';");
    $result = $database->query("UPDATE lychee_photos SET color = '$color', album = $album WHERE id = '$photoID2';");

    if($database->affected_rows==0){
		$result = $database->query("INSERT INTO lychee_photos (id, album, title, url, description, color, birdsize, type, width, height, size, sysdate, systime, iso, aperture, make, model, shutter, focal, takedate, taketime, thumbUrl, public, star, import_name) 
		SELECT $photoID2, $album, title, url, description, color, birdsize, type, width, height, size, sysdate, systime, iso, aperture, make, model, shutter, focal, takedate, taketime, thumbUrl, public, star, import_name FROM lychee_photos WHERE id=$photoID;"); 
	}
	
	if (!$result) return false;
    return true;

}

function idColorAlbum($color) {

	global $database;
	$album = 0;
	
    $result = $database->query("SELECT * FROM lychee_albums WHERE type='color' AND title='".$color."';");
	
	if($result->num_rows == 0) {
		$result = $database->query("INSERT INTO lychee_albums(title, sysdate, public, type) VALUES ('".$color."','".date("d.m.Y")."',1,'color');");
		$resultat = $database->query('SELECT max(id) FROM lychee_albums;');
		while($row = $resultat->fetch_assoc()) {
			$album = $row["max(id)"];
		}
	} else {
		while($row = $result->fetch_object()) {
			$album = $row->id;
		}
	}
	
    return $album;

}

function setPhotoBirdsize($photoID, $birdsize) {

	global $database;
	
	$photoID = floor($photoID/10)*10;
	$photoID1 = $photoID + 1;
	$photoID2 = $photoID + 2;
	
    $birdsize = htmlentities($birdsize);
	$album = idBirdsizeAlbum($birdsize);
	$result = $database->query("UPDATE lychee_photos SET birdsize = '$birdsize' WHERE id = '$photoID' OR id = '$photoID2';");
    $result = $database->query("UPDATE lychee_photos SET birdsize = '$birdsize', album = $album WHERE id = '$photoID1';");

	if(($database->affected_rows)==0){
		$result = $database->query("INSERT INTO lychee_photos (id, album, title, url, description, color, birdsize, type, width, height, size, sysdate, systime, iso, aperture, make, model, shutter, focal, takedate, taketime, thumbUrl, public, star, import_name) 
		SELECT $photoID1, $album, title, url, description, color, birdsize, type, width, height, size, sysdate, systime, iso, aperture, make, model, shutter, focal, takedate, taketime, thumbUrl, public, star, import_name FROM lychee_photos WHERE id=$photoID;"); 
	}
	
    if (!$result) return false;
    return true;

}

function idBirdsizeAlbum($birdsize) {

	global $database;
	$album = 0;

	switch($birdsize) {
		case 'petit': $album = 1; break;
		case 'moyen': $album = 2; break;
		case 'grand': $album = 3; break;
		default: $album = 0;
	}
	
    return $album;

}

function deletePhoto($photoID) {

	global $database;

	$photoID = floor($photoID/10)*10;
	$photoID1 = $photoID + 1;
	$photoID2 = $photoID + 2;
	
    $result = $database->query("SELECT * FROM lychee_photos WHERE id = '$photoID';");
    if (!$result) return false;
    $row = $result->fetch_object();
    $retinaUrl = explode(".", $row->thumbUrl);
    $unlink1 = unlink("../uploads/big/".$row->url);
    $unlink2 = unlink("../uploads/thumb/".$row->thumbUrl);
    $unlink3 = unlink("../uploads/thumb/".$retinaUrl[0].'@2x.'.$retinaUrl[1]);
    $result = $database->query("DELETE FROM lychee_photos WHERE id = '$photoID' OR id = '$photoID1' OR id = '$photoID2';");
    if (!$unlink1 || !$unlink2 || !$unlink3) return false;
    if (!$result) return false;

    return true;

}

function isPhotoPublic($photoID, $password) {

	global $database;

	if (is_numeric($photoID)) {
		$query = "SELECT * FROM lychee_photos WHERE id = '$photoID';";
	} else {
		$query = "SELECT * FROM lychee_photos WHERE import_name = '../uploads/import/$photoID';";
	}
    $result = $database->query($query);
    $row = $result->fetch_object();
    if (!is_numeric($photoID)&&!$row) return true;
    if ($row->public==1) return true;
    else {
    	$cAP = checkAlbumPassword($row->album, $password);
    	$iAP = isAlbumPublic($row->album);
    	if ($iAP&&$cAP) return true;
    	else return false;
    }

}

function getPhotoArchive($photoID) {

	global $database;

	$result = $database->query("SELECT * FROM lychee_photos WHERE id = '$photoID';");
	$row = $result->fetch_object();

	$extension = array_reverse(explode('.', $row->url));

	if ($row->title=='') $row->title = 'Untitled';

	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$row->title.$extension[0]\"");
	header("Content-Length: " . filesize("../uploads/big/$row->url"));

	readfile("../uploads/big/$row->url");

	return true;

}

?>
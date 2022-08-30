<?php

if (! defined('BASEPATH')) exit('No direct script access allowed');

function resize($base64, $width, $height)
{
	ob_start();
	$image = imagecreatefromstring(base64_decode($base64));

	// Hoehe und Breite neu berechnen
	list ($width_orig, $height_orig) = getimagesizefromstring (base64_decode($base64));

	if ($width && ($width_orig < $height_orig))
	{
		$width = intval(($height / $height_orig) * $width_orig);
	}
	else
	{
		$height = intval(($width / $width_orig) * $height_orig);
	}

	$image_p = imagecreatetruecolor($width, $height);

	// Bild nur verkleinern aber nicht vergroessern
	if ($width_orig > $width || $height_orig > $height)
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	else
		$image_p = $image;

	imagejpeg($image_p);
	$retval =  ob_get_contents();
	ob_end_clean();
	$retval = base64_encode($retval);

	@imagedestroy($image_p);
	@imagedestroy($image);
	return $retval;
}

/**
 * Es sind mehrere Kartenlesegeraete im Umlauf
 * Manche Geraete liefern die Kartennummer hexcodiert zurueck.
 * Diese muessen erst in das richtige Format transformiert werden
 * zusaetzlich werden die fuehrenden nullen entfernt
 *
 * @param $kartennummer
 * @return $kartennummer
 */
function transform_kartennummer($kartennummer)
{
	$kartennummer = trim($kartennummer);
	//Kartennummern die im Hex-Format sind werden umgewandelt
	if (!is_numeric($kartennummer))
	{
		$kartennummer=substr($kartennummer,strlen($kartennummer)-2,2).substr($kartennummer,strlen($kartennummer)-4,2). substr($kartennummer,strlen($kartennummer)-6,2).substr($kartennummer,0,2);
		$kartennummer=hexdec( $kartennummer);
	}

	//Fuehrende nullen entfernen
	$kartennummer = preg_replace("/^0*/", "", $kartennummer);
	return $kartennummer;
}

function transform_umlaute($string)
{
	$string = str_replace("ö","oe",$string);
	$string = str_replace("Ö","Oe",$string);
	$string = str_replace("ä","ae",$string);
	$string = str_replace("Ä","Ae",$string);
	$string = str_replace("ü","ue",$string);
	$string = str_replace("Ü","Ue",$string);
	$string = str_replace("ß","ss",$string);
	return $string;

}

<?php
try
{
	$db = new PDO('mysql:host=localhost; dbname=vtc; charset=utf8', 'root', '', [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, // Active les erreurs SQL,
		// On récupère tous les résultats en tableau associatif
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);
}
catch(Exception $e)
{
	echo $e->getMessage();
	header('Location: https://www.google.fr/search?q='.$e->getMessage());
	die('Stop'); // Arrête le script si la base de données n'est pas dispo
}
?>
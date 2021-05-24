<?php
// Connexion à la BDD via PDO ! Try... catch pour tester
try {
	$db = new PDO('mysql:host=localhost; dbname=vtc; charset=utf8', 'root', '', [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, /* Active les erreurs SQL */
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC /* On récupère tous les résultats en tableau associatif */
	]);
}
catch(Exception $e) {
    echo 'Erreur de connexion à la base de données : '.$e->getMessage(); // Affiche message d'erreur si la connexion échoue
}
?>
<?php
// Connexion à la base de données : 
$pdo = new PDO('mysql:host=localhost;dbname=lokisalle', 'root', '', array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
));

// Session :
session_start(); 

// Variables : 
$msg = '';
$page = ''; 
$contenu = '';

// Chemin :
define('RACINE_SERVEUR', $_SERVER['DOCUMENT_ROOT']);
define('RACINE_SITE', '/projet/');

// Autres inclusions : 
require_once('fonctions.inc.php');
<?php 
// fonction pour faire les debug : 
function debug($arg){
	echo '<div style="color: white; padding: 10px; background:#' . rand(111111,999999) . '" >';
	$trace = debug_backtrace();
	
	echo 'Le debug a été demandé dans le fichier : ' . $trace[0]['file'] . ' à la ligne : ' . $trace[0]['line'] . '<hr/>'; 
	
	echo '<pre>';
	print_r($arg);
	echo '</pre>';
	
	echo '</div>';
}

// Fonction pour voir si user est connecté :
function userConnecte(){
	if(isset($_SESSION['membre'])){
		return TRUE; 
	}
	else{
		return FALSE; 
	}
	// S'il existe une session/membre, cela signifie que l'utilisateur est connecté. On retourne TRUE, sinon FALSE. 
}

// Fonction pour voir si user est admin
function userAdmin(){
	if(userConnecte() && $_SESSION['membre']['statut'] == 1){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

?>
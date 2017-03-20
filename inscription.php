<?php

require_once('inc/init.inc.php');

//Redirection si user est connecté
if(userConnecte()){
	header('location:index.php');
}

// Traitement pour l'inscription
if($_POST){
	
	//Vérification PSEUDO :
	$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $_POST['pseudo']);
	
	if(!empty($_POST['pseudo'])){
		if($verif_caractere){
			if(strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 20){
				$msg .= '<div class="erreur">Veuillez renseigner un pseudo de 3 à 20 caractères !</div>';
			}
		}
		else{
			$msg .= '<div class="erreur">Pseudo : Caractères autorisés : A à Z, 0 à 9 et "-._"</div>';
		}
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un pseudo !</div>'; 
	}


	//Vérification du MDP 
	if(!empty($_POST['mdp'])){
		if($verif_caractere){
			if(strlen($_POST['mdp']) < 4 || strlen($_POST['mdp']) > 20){
				$msg .= '<div class="erreur">Votre mot de passe doit comporter entre 4 et 20 caractères</div>';
			}
		}
		else{
			$msg .= '<div class="erreur">Caractères autorisés : A à Z, 0 à 9 et "-._"</div>';
		}
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un mot de passe !</div>';
	}
	
	// Vérification du mail
	if(!empty($_POST['email'])){
		if(!strpos($_POST['email'], '@')){
			$msg .= '<div class="erreur">Veuillez renseigner un email valide</div>';
		}
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un email !</div>';
	}
	
	//Vérification du nom
	if(!empty($_POST['nom'])){
		if($verif_caractere){
			if(strlen($_POST['nom']) < 2 || strlen($_POST['nom']) > 20){
				$msg .= '<div class="erreur">Votre nom doit comporter au moins 2 caractères</div>';
			}
		}
		else{
			$msg .= '<div class="erreur">Caractères autorisés : A à Z, 0 à 9 et "-._"</div>';
		}
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un nom !</div>';
	}
	//Vérification du prenom
	if(!empty($_POST['prenom'])){
		if($verif_caractere){
			if(strlen($_POST['prenom']) < 2 || strlen($_POST['prenom']) > 20){
				$msg .= '<div class="erreur">Votre prénom doit comporter entre 2 et 20 caractères</div>';
			}
		}
		else{
			$msg .= '<div class="erreur">Caractères autorisés : A à Z, 0 à 9 et "-._"</div>';
		}
	}
	else{
		$msg .= '<div class="erreur">Veuillez renseigner un prénom !</div>';
	}
	//****************** Enregistrement de l'utilisateur : 

	if(empty($msg)){ 
	
		// Vérification de la disponibilité du pseudo : 
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){ // S'il y a au moins un résultat cela signifie que le pseudo existe déjà en BDD. 
			$msg .= '<div class="erreur">Le pseudo ' . $_POST['pseudo'] . ' n\'est pas disponible. Veuillez choisir un autre pseudo.</div>';
		}

		//vérif de la disponibilité du mail

		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE email = :email");
		$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
		$resultat -> execute();

		if($resultat -> rowCount() > 0){

			$msg .= '<div class="erreur">L\'email saisi est déjà enregistré dans la base. Mot de passe oublié ?</div>';
		}
		else{
			$resultat = $pdo -> prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, 0, NOW())");
		
			$mdp = md5($_POST['mdp']);
			
			$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
			$resultat -> bindParam(':mdp', $mdp, PDO::PARAM_STR);
			$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
			$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
			$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
			$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
			
			
			if($resultat -> execute()){
				header('location:connexion.php');
			}
		}
	}
} // ---- fin du if($_POST) -----

// Création de variables pour garder en mémoire les infos saisies :

$pseudo = (isset($_POST['pseudo'])) ? $_POST['pseudo'] : '';
$nom 		= (isset($_POST['nom'])) ? $_POST['nom'] : '';
$prenom 	= (isset($_POST['prenom'])) ? $_POST['prenom'] : '';
$email 		= (isset($_POST['email'])) ? $_POST['email'] : '';
$civilite 	= (isset($_POST['civilite'])) ? $_POST['civilite'] : '';



$page = 'Inscription';
require_once('inc/header.inc.php');
?>

<!-- ------------------ PARTIE HTML ------------------- -->

<h1>Incription</h1>
<?= $msg ?>
<form action="" method="post">
	
	<label for="nom">Nom :</label><br/>
	<input id="nom" type="text" name="nom" value="<?= $nom ?>"/><br/><br/>
	
	<label for="prenom">Prénom :</label><br/>
	<input type="text" name="prenom" value="<?= $prenom ?>"/><br/><br/>
	
	<label for="email">Email :</label><br/>
	<input type="text" id="email" name="email" value="<?= $email ?>"/><br/><br/>
	
	<label>Civilité :</label><br/>
	<select name="civilite">
		<option value="m" <?=  ($civilite == 'm') ? 'selected' : '' ?>>Homme</option>
		<option value="f"<?=  ($civilite == 'f') ? 'selected' : '' ?>>Femme</option>
	</select><br/><br/>

	<label for="pseudo">Pseudo :</label><br/>
	<input type="text" id="pseudo" name="pseudo" value="<?= $pseudo ?>"/><br/><br/>
	
	<label for="mdp">Mot de passe :</label><br/>
	<input type="text" name="mdp" /><br/><br/>

	<input type="submit" value="Inscription" />
	
</form>


<?php
require_once('inc/footer.inc.php');
?>

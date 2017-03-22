<?php
require_once('../inc/init.inc.php');

//si user n'est pas admin, redicrection
if(!userAdmin()){
	header('location:../index.php');
}

//TRAITEMENT POUR AFFICHER TOUS LES MEMBRES :


	$resultat = $pdo -> query("SELECT * FROM membre");

	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i = 0; $i < $resultat -> columnCount(); $i++){
		$colonne = $resultat -> getColumnMeta($i);
		$contenu .= '<th>' . $colonne['name'] . '</th>';
	}
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';

	while($membres = $resultat -> fetch(PDO::FETCH_ASSOC)){
		$contenu .= '<tr>';
		foreach($membres as $indice => $valeur){
			if($indice == 'photo'){
				$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="80"/></td>';
			}
			else{
				$contenu .= '<td>' . $valeur . '</td>';
			}
		}
		$contenu .= '<td><a href="?action=modification&id=' . $membres['id_membre'] . '"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		$contenu .= '<td><a href="?action=suppression&id=' . $membres['id_membre'] . '"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		$contenu .= '<td><a href="?action=affichage&id=' . $membres['id_membre'] . '"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		
		$contenu .= '</tr>'; 
	}
	$contenu .= '</table>';


// TRAITEMENT POUR SUPPRIMER UN MEMBRE : 
// Dans un premier temps il faut supprimer la (les) photo(s) du serveur.
if(isset($_GET['action']) && $_GET['action'] == 'suppression'){ 
	if(isset($_GET['id']) && is_numeric($_GET['id'])){ 
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE id_membre = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){ 
			$membre_supp = $resultat -> fetch(PDO::FETCH_ASSOC);

			// Pour pouvoir supprimer la photo, il nous faut son emplacement (chemin) exact. 
			$chemin_photo_a_supprimer = RACINE_SERVEUR . RACINE_SITE . 'img/' . utf8_decode($membre_supp['photo']);
			
			
			if(file_exists($chemin_photo_a_supprimer) && $membre_supp['photo'] != 'default.jpg'){
				unlink($chemin_photo_a_supprimer); // Supprime le fichier du serveur.
			}
			
			// Maintenant que la photo est supprimée, on va supprimer la membre de la BDD : 
			$resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $membre_supp[id_membre]"); 
			
			if($resultat != FALSE){ // Si la requête est un succès
				$_GET['action'] = 'affichage';
				$msg .= '<div class="validation">Le membre N°' . $membre_supp['id_membre'] . ' a bien été supprimé !</div>';
			}
		}
		// Ici dans le else, on pourrait faire une redirection vers 404.php
	}
	// Ici dans le else, on pourrait faire une redirection vers 404.php
}




// // TRAITEMENT POUR ENREGISTRER/MODIFIER UN MEMBRE : 
if($_POST){
	
	debug($_POST);
	debug($_FILES);
	
	// traitement sur les photos :
	$nom_photo = 'default.jpg';
	
	//Si je suis dans le cadre d'une modification de membre, alors il doit y avoir un champs photo dans le formulaire. Donc $nom_photo va prendre la valeur de la photo actuelle pour la ré-enregistrer telle qu'elle est ! 
	if(isset($_POST['photo_actuelle'])){
		$nom_photo = $_POST['photo_actuelle'];
	}	
	
	if(!empty($_FILES['photo']['name'])){ // Si l'utilisateur nous a transmis une photo
		if($_FILES['photo']['error'] == 0){	
			$ext = explode('/', $_FILES['photo']['type']);
			$ext_autorisee = array('jpeg', 'gif', 'png');		
			if(in_array($ext[1], $ext_autorisee)){
				if($_FILES['photo']['size'] < 1000000){
			
					// On renomme la photo pour éviter les doublons dans le dossier photo/
					$nom_photo = $_POST['reference'] . '_' . $_FILES['photo']['name'];
					$nom_photo = utf8_decode($nom_photo);
					// enregistrer la photo dans le dossier photo/
					$chemin_photo = RACINE_SERVEUR . RACINE_SITE . 'photo/' . $nom_photo;
					
					copy($_FILES['photo']['tmp_name'], $chemin_photo); // La fonction copy() permet de copier/coller un fichier d'un emplacement à un autre. Elle attend 2 args : 1/ L'emplacement du fichier à copier et 2/ l'emplacement définitif de la copie. 
					
				}
				else{
					$msg .= '<div class="error">Taille maximum des images : 1Mo</div>';
				}
			}
			else{
				$msg .= '<div class="error">Extensions autorisées : PNG, JPG, JPEG, GIF</div>';
			}
		}
		else{
			$msg .= '<div class="error">Veuillez sélectionner une nouvelle image</div>';
		}
	}
	
	// Je sors de cette condition avec $nom_photo ayant soit la valeur 'default.jpg', soit le nom de la photo chargée par User auquel nous avons ajouté la référence, soit la photo du membre_supp que je suis en train de modifier. 
	
	//Enregistrement dans la BDD
	
	if(isset($_GET['action']) && $_GET['action'] == 'modification'){
		$resultat = $pdo -> prepare("UPDATE membre set pseudo= :pseudo, nom=:nom, prenom=:prenom, email=:email, civilite=:civilite WHERE id_membre=:id");
		
		
		$resultat -> bindParam(':id', $_POST['id_membre'], PDO::PARAM_INT);
	}
	
	else{
		$resultat = $pdo -> prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut, NOW())");

		$mdp = md5($_POST['mdp']);
		$resultat -> bindParam(':mdp', $_POST['mdp'], PDO::PARAM_STR);
		$resultat -> bindParam(':statut', $_POST['statut'], PDO::PARAM_STR);
	}
	
	//STR
	$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
	$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
	$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
	$resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
	$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
	$resultat -> bindParam(':photo', $nom_photo, PDO::PARAM_STR);
	$nom_photo =  utf8_encode($nom_photo);
	
	
	if($resultat -> execute()){
		$_GET['action'] = 'affichage';
		$last_id = $pdo -> lastInsertId();
		$msg .= '<div class="validation">La membre N°' . $last_id . ' a été enregistré avec succès !</div>';
	}
}

$page = 'Gestion Membres';
require_once('../inc/header.inc.php');


if(isset($_GET['id']) && is_numeric($_GET['id'])){ // Si j'ai un ID dans l'URL, et que cet ID est bien une valeur numérique, je récupère les infos du produit correspondant dans la BDD :
	$resultat = $pdo -> prepare("SELECT * FROM membre WHERE id_membre = :id");
	$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
	$resultat -> execute();
	
	if($resultat -> rowCount() > 0){
		$membre_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
	}
}// fin du if !!!! 

$pseudo = (isset($membre_actuel)) ? $membre_actuel['pseudo'] : '';
$nom = (isset($membre_actuel)) ? $membre_actuel['nom'] : '';
$prenom = (isset($membre_actuel)) ? $membre_actuel['prenom'] : '';
$email = (isset($membre_actuel)) ? $membre_actuel['email'] : '';
$civilite = (isset($membre_actuel)) ? $membre_actuel['civilite'] : '';


$id_membre = (isset($membre_actuel)) ? $membre_actuel['id_membre'] : '';
$action = (isset($membre_actuel)) ? 'Modifier' : 'Ajouter';
?>

<!-- Formulaire d'ajout d'inscription --> 

<form method="post" action="" enctype="multipart/form-data">
	<input type="hidden" value="<?= $id_membre ?>" name="id_membre" />
	
	<label for="pseudo">Pseudo: </label>
	<input type="text" name="pseudo" value="<?= $pseudo ?>" /><br/>
	<label for="nom">Nom: </label>
	<input type="text" name="nom" value="<?= $nom ?>"/><br/>
	<label for="prenom">Prénom: </label>
	<input type="text" name="prenom" value="<?= $prenom ?>"/><br/>
	<label for="email">Email: </label>
	<input type="email" name="email" value="<?= $email ?>"/><br/>
	
	<!-- en cas de modif : --> 
	<?php if(isset($_GET['action']) && $_GET['action'] != 'modification') : ?>
	<label for="mdp">Mot de passe :</label><br/>
	<input type="password" name="mdp"/><br/>
	<label>Satut</label><br/>
	<select name="statut">
		<option>-- Selectionnez --</option>
		<option value="0" <?= ($civilite == '0') ? 'selected' : '' ?>>User</option>
		<option value="1" <?= ($civilite == '1') ? 'selected' : '' ?>>Admin</option>
	</select><br/>
	<?php endif; ?>
	<!-- fin du cas de modif -->

	<label>Civilité : </label>
	<select name="civilite">
		<option>-- Selectionnez --</option>
		<option value="m" <?= ($civilite == 'm') ? 'selected' : '' ?>>Homme</option>
		<option value="f" <?= ($civilite == 'f') ? 'selected' : '' ?>>Femme</option>
	</select><br/>
	
	
	<?php if(isset($membre_actuel)) : ?>
	
	<img src="<?= RACINE_SITE ?>img/<?= $photo ?>" width="100" /><br/>
	<input type="hidden" name="photo_actuelle" value="<?= $photo ?>"/>
	
	<?php endif; ?>
	
	
	<label>Photo : </label>
	<input type="file" name="photo"/><br/>
	<input type="submit" value="Enregistrer"/><br/>
	
</form>


<h1>Gestion de la boutique</h1>

<ul>
<li><a href="?action=affichage">Afficher les membres</a></li>
<li><a href="?action=affichage">Ajouter un membre</a></li>

<?= $contenu ?>
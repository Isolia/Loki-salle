<?php
require_once('../inc/init.inc.php');

//si user n'est pas admin, redicrection
// if(!userAdmin()){
// 	header('location:../index.php');
// }

//TRAITEMENT POUR AFFICHER TOUTES LES SALLES :

if(isset($_GET['action']) && $_GET['action'] == 'affichage'){
	$resultat = $pdo -> query("SELECT * FROM salle");

	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i = 0; $i < $resultat -> columnCount(); $i++){
		$colonne = $resultat -> getColumnMeta($i);
		$contenu .= '<th>' . $colonne['name'] . '</th>';
	}
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';

	while($salles = $resultat -> fetch(PDO::FETCH_ASSOC)){
		$contenu .= '<tr>';
		foreach($salles as $indice => $valeur){
			if($indice == 'photo'){
				$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="80"/></td>';
			}
			else{
				$contenu .= '<td>' . $valeur . '</td>';
			}
		}
		$contenu .= '<td><a href="?action=modification&id=' . $salles['id_salle'] . '"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		$contenu .= '<td><a href="?action=suppression&id=' . $salles['id_salle'] . '"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		
		$contenu .= '</tr>'; 
	}
	$contenu .= '</table>';
}

// TRAITEMENT POUR SUPPRIMER UNE SALLE : 
// Dans un premier temps il faut supprimer la (les) photo(s) du serveur.
if(isset($_GET['action']) && $_GET['action'] == 'suppression'){ 
	if(isset($_GET['id']) && is_numeric($_GET['id'])){ 
		$resultat = $pdo -> prepare("SELECT * FROM salle WHERE id_salle = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){ 
			$salle_supp = $resultat -> fetch(PDO::FETCH_ASSOC);

			// Pour pouvoir supprimer la photo, il nous faut son emplacement (chemin) exact. 
			$chemin_photo_a_supprimer = RACINE_SERVEUR . RACINE_SITE . 'img/' . utf8_decode($salle_supp['photo']);
			
			
			if(file_exists($chemin_photo_a_supprimer) && $salle_supp['photo'] != 'default.jpg'){
				unlink($chemin_photo_a_supprimer); // Supprime le fichier du serveur.
			}
			
			// Maintenant que la photo est supprimée, on va supprimer la salle de la BDD : 
			$resultat = $pdo -> exec("DELETE FROM salle WHERE id_salle = $salle_supp[id_salle]"); 
			
			if($resultat != FALSE){ // Si la requête est un succès
				$_GET['action'] = 'affichage';
				$msg .= '<div class="validation">La salle N°' . $salle_supp['id_salle'] . ' a bien été supprimée !</div>';
			}
		}
		// Ici dans le else, on pourrait faire une redirection vers 404.php
	}
	// Ici dans le else, on pourrait faire une redirection vers 404.php
}




// // TRAITEMENT POUR ENREGISTRER/MODIFIER UNE SALLE : 
if($_POST){
	
	debug($_POST);
	debug($_FILES);
	
	// traitement sur les photos :
	$nom_photo = 'default.jpg';
	
	//Si je suis dans le cadre d'une modification de salle_supp, alors, il doit y avoir un champs photo actuelle dans le formulaire. Donc $nom_photo va prendre la valeur de la photo actuelle pour la ré-enregistrer telle qu'elle est ! 
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
	
	// Je sors de cette condition avec $nom_photo ayant soit la valeur 'default.jpg', soit le nom de la photo chargée par User auquel nous avons ajouté la référence, soit la photo du salle_supp que je suis en train de modifier. 
	
	//Enregistrement dans la BDD
	
	if(isset($_GET['action']) && $_GET['action'] == 'modification'){
		$resultat = $pdo -> prepare("UPDATE salle set titre= :titre, description=:description, photo=:photo, pays=:pays, ville=:ville, adresse=:adresse, cp=:cp, capacite=:capacite, categorie:categorie WHERE id_salle=:id_salle");
		
		//$resultat = $pdo -> prepare("UPDATE salle_supp set reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock = :stock WHERE id_salle_supp = :id_salle_supp ");
		
		$resultat -> bindParam(':id_salle', $_POST['id_salle'], PDO::PARAM_INT);
	}
	else{
		$resultat = $pdo -> prepare("INSERT INTO salle (titre, description, photo, pays, ville, adresse, cp, capacite, categorie) VALUES (:titre, :description, :photo, :pays, :ville, :adresse, :cp, :capacite, :categorie)");
	}
	
	//STR
	$resultat -> bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
	$resultat -> bindParam(':description', $_POST['description'], PDO::PARAM_STR);
	$resultat -> bindParam(':pays', $_POST['pays'], PDO::PARAM_STR);
	$resultat -> bindParam(':ville', $_POST['ville'], PDO::PARAM_STR);
	$resultat -> bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR);
	$resultat -> bindParam(':photo', $nom_photo, PDO::PARAM_STR);
	$nom_photo =  utf8_encode($nom_photo);
	$resultat -> bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);
	
	//INT
	$resultat -> bindParam(':capacite', $_POST['capacite'], PDO::PARAM_INT);
	$resultat -> bindParam(':cp', $_POST['cp'], PDO::PARAM_INT);
	
	if($resultat -> execute()){
		$_GET['action'] = 'affichage';
		$last_id = $pdo -> lastInsertId();
		$msg .= '<div class="validation">La salle N°' . $last_id . ' a été enregistrée avec succès !</div>';
	}
}

$page = 'Gestion des salles';
require_once('../inc/header.inc.php');

?>

<!-- CONTENU HTML --> 

<h1>Gestion de la boutique</h1>
<ul>
	<li><a href="?action=affichage">Afficher les produits</a></li>
	<li><a href="?action=ajout">Ajouter un produit</a></li>
</ul><hr/>

<?= $contenu ?>

<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) : ?>

<?php endif; ?>	


<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])){ // Si j'ai un ID dans l'URL, et que cet ID est bien une valeur numérique, je récupère les infos du produit correspondant dans la BDD :
	$resultat = $pdo -> prepare("SELECT * FROM salle WHERE id_salle = :id");
	$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
	$resultat -> execute();
	
	if($resultat -> rowCount() > 0){
		$salle_actuelle = $resultat -> fetch(PDO::FETCH_ASSOC);
	}
}// fin du if !!!! 

$titre = (isset($salle_actuelle)) ? $salle_actuelle['titre'] : '';
$description = (isset($salle_actuelle)) ? $salle_actuelle['description'] : '';
$photo = (isset($salle_actuelle)) ? $salle_actuelle['photo'] : '';
$pays = (isset($salle_actuelle)) ? $salle_actuelle['pays'] : '';
$ville = (isset($salle_actuelle)) ? $salle_actuelle['ville'] : '';
$adresse = (isset($salle_actuelle)) ? $salle_actuelle['adresse'] : '';
$cp = (isset($salle_actuelle)) ? $salle_actuelle['cp'] : '';
$capacite = (isset($salle_actuelle)) ? $salle_actuelle['capacite'] : '';
$categorie = (isset($salle_actuelle)) ? $salle_actuelle['categorie'] : '';


$id_salle = (isset($produit_actuel)) ? $produit_actuel['id_salle'] : '';
$action = (isset($produit_actuel)) ? 'Modifier' : 'Ajouter';
?>

<h3><?= $action ?> une salle</h3>

<form method="post" action="" enctype="multipart/form-data">
	<!-- L'attribut enctype permet de gérer les fichiers "uploadés" et de les traiter grâce à superglobale $_FILES -->
	<input type="hidden" value="<?= $id_salle ?>" name="id_salle" />
	<!-- Un champs type="hidden" est un champs caché -->
	
	<label for="titre">Titre: </label>
	<input type="text" name="titre" value="<?= $titre ?>" /><br/>
	<label for="description">Description: </label>
	<textarea name="description"><?= $description ?></textarea><br/>
	<label for="pays">Pays: </label>
	<input type="text" name="pays" value="<?= $pays ?>"/><br/>
	<label for="ville">Ville: </label>
	<input name="ville" value="<?= $ville ?>"/><br/>
	<label for="adresse">Adresse: </label>
	<textarea name="adresse"><?= $adresse ?></textarea><br/>
	<label for="cp">Code postal: </label>
	<input type="number" name="cp" value="<?= $cp ?>"/><br/>
	<label for="capacite">Capacité: </label>
	<input type="number" name="capacite" value="<?= $capacite ?>"/><br/>
	
	<label>Catégorie : </label>
	<select name="categorie">
		<option>-- Selectionnez --</option>
		<option value="Réunion" <?= ($categorie == 'Réunion') ? 'selected' : '' ?>>Réunion</option>
		<option value="Bureau" <?= ($categorie == 'Bureau') ? 'selected' : '' ?>>Bureau</option>
		<option value="mixte" <?= ($categorie == 'Formation') ? 'selected' : '' ?>>Formation</option>
	</select><br/>
	
	
	<?php if(isset($salle_actuelle)) : ?>
	
	<img src="<?= RACINE_SITE ?>img/<?= $photo ?>" width="100" /><br/>
	<input type="hidden" name="photo_actuelle" value="<?= $photo ?>"/>
	
	<?php endif; ?>
	
	
	<label>Photo : </label>
	<input type="file" name="photo"/><br/>
	<input type="submit" value="<?= $action ?>"/><br/>
	
</form>
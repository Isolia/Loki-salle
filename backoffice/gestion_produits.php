<?php 
require_once('../inc/init.inc.php');

// Si l'utilisateur n'est pas ADMIN :
if(!UserAdmin()){
    header('location:../connexion.php');
}

// traitement suppresion produits
if(isset($_GET['action']) && $_GET['action'] == 'suppression'){ 
	if(isset($_GET['id']) && is_numeric($_GET['id'])){ // 
		$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();
		if($resultat -> rowCount() > 0){
			$produit = $resultat -> fetch(PDO::FETCH_ASSOC);
			
			//Suppresion de la BDD
			$resultat = $pdo -> exec("DELETE FROM produit WHERE id_produit = $produit[id_produit]"); 
			
			if($resultat != FALSE){ // Si la requête est un succès
				$_GET['action'] = 'affichage';
				$msg .= '<div class="validation">Le produit N°' . $produit['id_produit'] . ' a bien été supprimé !</div>';
			}
		}
	}
} // Fin du IF SUPP


// Traitement pour ajouter / modifier un produit
if($_POST){
	//Enregistrement dans la BDD
	if(isset($_GET['action']) && $_GET['action'] == 'modification'){
		$resultat = $pdo -> prepare
			("UPDATE produit set  id_salle = :id_salle, date_arrivee = :date_arrivee, date_depart = :date_depart, prix = :prix, etat = :etat WHERE id_produit = :id");
		$resultat -> bindParam(':id', $_POST['id_produit'], PDO::PARAM_INT);
	}
	else{
		$resultat = $pdo -> prepare
		("INSERT INTO produit (id_salle, date_arrivee, date_depart, prix, etat) 
		VALUES (:id_salle, :date_arrivee, :date_depart, :prix, :etat)");
	}
	
	//STR
	$resultat -> bindParam(':id_salle', $_POST['id_salle'], PDO::PARAM_INT);
	$resultat -> bindParam(':date_arrivee', $_POST['date_arrivee'], PDO::PARAM_STR);
	$resultat -> bindParam(':date_depart', $_POST['date_depart'], PDO::PARAM_STR);
	$resultat -> bindParam(':prix', $_POST['prix'], PDO::PARAM_INT);
	$resultat -> bindParam(':etat', $_POST['etat'], PDO::PARAM_STR);
	
	if($resultat -> execute()){
		$_GET['action'] = 'affichage';
		$msg .= '<div class="validation">Le produit a été enregistré avec succès !</div>';
	}
}

// Traitement aller chercher la table SALLE
$salle = $pdo -> query("SELECT * FROM salle"); 

// traitement affichage des produits
    // Alors je vais chercher les produits:
	$resultat = $pdo -> query ("SELECT s.titre, s.photo, p.* FROM produit p, salle s WHERE p.id_salle = s.id_salle");
	
	$contenu .= '<table border="1">';
	$contenu .= '<tr>';

	$contenu .= '<th>ID Produit</th>';
	$contenu .= '<th>Date d\'arrivée</th>';
	$contenu .= '<th>Date de départ</th>';
	$contenu .= '<th>ID salle</th>';
	$contenu .= '<th>Prix</th>';
	$contenu .= '<th>Etat</th>';
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';

	while($produits = $resultat -> fetch(PDO::FETCH_ASSOC)){
		$contenu .= '<tr>';
		$contenu .= '<td>' . $produits['id_produit'] . '</td>';
		$contenu .= '<td>' . $produits['date_arrivee'] . '</td>';
		$contenu .= '<td>' . $produits['date_depart'] . '</td>';
		$contenu .= '<td>' . $produits['id_salle'] . ' - ' . $produits['titre'] . '<img src="' . RACINE_SITE . 'img/' . $produits['photo'] . '" height="80"/></td>';
		$contenu .= '<td>' . $produits['prix'] . '</td>';
		$contenu .= '<td>' . $produits['etat'] . '</td>';

		$contenu .= 
		'<td><a href="?action=modification&id=' . $produits['id_produit'] . '"><img src="' . RACINE_SITE . 'img/edit.png"/></a>' . 
		'<a href="?action=voir&id=' . $produits['id_produit'] . '"><img src="' . RACINE_SITE . 'img/view.png"/></a>' .
		'<a href="?action=suppression&id=' . $produits['id_produit'] . '"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		
		$contenu .= '</tr>'; 
	}
	$contenu .= '</table>'; 

$page='Gestion des Produit';
require_once('../inc/header.inc.php');
?>

<!-- Contenu HTML -->
<main class="conteneur">
    <section id="gestion_produits">
        <h1>Gestion des Produits</h1>

        <?= $msg ?>
        <?= $contenu ?>


		<?php
		// Si j'ai un ID dans l'URL, et que cet ID est bien une valeur numérique, je récupère les infos du produit correspondant dans la BDD :
		if(isset($_GET['id']) && is_numeric($_GET['id'])){ 
			$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id");
			$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
			$resultat -> execute();
			
			if($resultat -> rowCount() > 0){
				$produit_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
			}
		}// fin du if !!!! 

		$date_arrivee = (isset($produit_actuel)) ? $produit_actuel['date_arrivee'] : '';
		$date_depart = (isset($produit_actuel)) ? $produit_actuel['date_depart'] : '';
		$prix = (isset($produit_actuel)) ? $produit_actuel['prix'] : '';
		$etat = (isset($produit_actuel)) ? $produit_actuel['etat'] : '';
		$id_produit = (isset($produit_actuel)) ? $produit_actuel['id_produit'] : '';
		$action = (isset($produit_actuel)) ? 'Modifier' : 'Ajouter';


		?>
		<h3><?= $action ?> un produit</h3>

		<form method="post" action="" enctype="multipart/form-data">
			<input type="hidden" value="<?= $id_produit ?>" name="id_produit" />
			<!-- Un champs type="hidden" est un champs caché -->
			<div class="float-left">

				<label>Salle : </label>
				<select name="id_salle">

				<?php while($listeSalle = $salle -> fetch(PDO::FETCH_ASSOC)) : ?>
					<option value="<?php echo $listeSalle['id_salle']?>"><?php echo $listeSalle['id_salle'] . ' - ' . $listeSalle['titre'] . ' - ' . $listeSalle['adresse'] . ' ' . $listeSalle['cp']. ' ' . $listeSalle['ville'] . ' - ' . $listeSalle['capacite']?></option>
				<?php endwhile; ?>
				</select>


				<label>Date arrivée: </label>
				<input type="date" name="date_arrivee" value="<?= $date_arrivee ?>" /><br/>
				<label>Date départ: </label>
				<input type="date" name="date_depart" value="<?= $date_depart ?>"/><br/>
			</div>
			<div class="float-left">
				<label>Prix: </label>
				<input type="text" name="prix" value="<?= $prix ?>"/><br/>
				<label>Etat: </label>
				<select name="etat">
					<option value="libre" <?= ($etat == 'libre') ? 'selected' : '' ?>>Libre</option>
					<option value="reservation" <?= ($etat == 'reservation') ? 'selected' : '' ?>>Réservé</option>
				</select><br/>
			</div>
			<div class="clear"></div>
			<?php if(isset($produit_actuel)) : ?>
			
			<?php endif; ?>

			<input type="submit" value="<?= $action ?>"/><br/>
		</form>

    </section>
</main>

<?php
require_once('../inc/footer.inc.php');
?>

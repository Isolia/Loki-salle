<?php 
require_once('../inc/init.inc.php');

// Si l'utilisateur n'est pas ADMIN :
if(!UserAdmin()){
    header('location:../connexion.php');
}

// traitement affichage des produits
// Si il y a une action et que l'action est égale à "affichage'
if(isset($_GET['action']) && $_GET['action'] == 'affichage'){
    // Alors je vais chercher les produits:
    $resultat = $pdo -> query("SELECT * FROM produit");
    $contenu .= '<table border="1">';
	$contenu .= '<tr>';
	for($i = 0; $i < $resultat -> columnCount(); $i++){
		$colonne = $resultat -> getColumnMeta($i); 
		$contenu .= '<th>' . $colonne['name'] . '</th>';
	}
	$contenu .= '<th colspan="2">Actions</th>';
    
	$contenu .= '</tr>';

	while($produits = $resultat -> fetch(PDO::FETCH_ASSOC)){
		$contenu .= '<tr>'; 
		foreach($produits as $indice => $valeur){
			if($indice == 'img'){
				$contenu .= '<td><img src="' . RACINE_SITE . 'img/' . $valeur . '" height="80"/></td>';
			}
			else{
				$contenu .= '<td>' . $valeur . '</td>';
			}	
		}
		$contenu .= '<td><a href="?action=modification&id=' . $produits['id_produit'] . '"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		$contenu .= '<td><a href="?action=suppression&id=' . $produits['id_produit'] . '"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		
		$contenu .= '</tr>'; 
	}
	$contenu .= '</table>'; 
}

// traitement ajout produits

// traitement suppresion produits









$page='Gestion des Produit';
require_once('../inc/header.inc.php');
?>

<!-- Contenu HTML -->
<main>
    <section id="gestion_produits">
        <h1>Gestion des Produits</h1>
        <ul>
	        <li><a href="?action=affichage">Afficher les produits</a></li>
        </ul>

        <?= $msg ?>
        <?= $contenu ?>

    </section>
</main>

<?php
require_once('../inc/footer.inc.php');
?>

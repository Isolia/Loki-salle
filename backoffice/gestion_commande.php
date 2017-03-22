<?php 
require_once('../inc/init.inc.php');

//si user n'est pas admin, redicrection
// if(!userAdmin()){
// 	header('location:../index.php');
// }



//TRAITEMENT POUR AFFICHER TOUS LES commandes :


	$resultat = $pdo -> query("SELECT m.email, s.titre, p.date_arrivee, p.date_depart, c.* FROM commande c, membre m, produit p, salle s WHERE m.id_membre = c.id_membre AND p.id_salle = s.id_salle");

// 	SELECT a.prenom, e.date_sortie, e.date_retour --> Ce qu'on souhaite afficher
// FROM abonne a, emprunt e --> Liste des tables convernées
// WHERE a.id_abonne = e.id_abonne

	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	
	$contenu .= '<th>Référence de la commande</th>';
	$contenu .= '<th>Membre</th>';
	$contenu .= '<th>Produit</th>';
	$contenu .= '<th>Prix</th>';
	$contenu .= '<th>Date</th>';
	$contenu .= '<th colspan="2">Actions</th>';
	$contenu .= '</tr>';

	while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){
		$contenu .= '<tr>';
		$contenu .= '<td>' . $commandes['id_commande'] . '</td>';
		$contenu .= '<td>' . $commandes['id_membre'] . ' - ' . $commandes['email'] . '</td>';
		$contenu .= '<td>' . $commandes['id_salle'] . ' - ' . $commandes['titre'] . '<br/>' . $commandes['date_arrivee'] . ' au ' . $commandes['date_depart'] . '</td>';
		$contenu .= '<td>' . $commandes['prix'] . '</td>';
		
		$contenu .= '<td>' . $commandes['date_enregistrement'] . '</td>';
		$contenu .= '<td><a href="?action=voir&id=' . $commandes['id_membre'] . '"><img src="' . RACINE_SITE . 'img/view.png"/></a></td>';
		$contenu .= '<td><a href="?action=suppression&id=' . $commandes['id_membre'] . '"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		
		$contenu .= '</tr>'; 
	}
	$contenu .= '</table>';


echo $contenu;

?>

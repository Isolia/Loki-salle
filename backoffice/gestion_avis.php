<?php 
require_once('../inc/init.inc.php');

// si user n'est pas admin, redicrection
if(!userAdmin()){
	header('location:../index.php');
}



//TRAITEMENT POUR AFFICHER TOUS LES avis :


	$resultat = $pdo -> query("SELECT m.email, s.titre, a.* FROM avis a, membre m, salle s WHERE m.id_membre = a.id_membre AND a.id_salle = s.id_salle");

// 	SELECT a.prenom, e.date_sortie, e.date_retour --> Ce qu'on souhaite afficher
// FROM abonne a, emprunt e --> Liste des tables convernées
// WHERE a.id_abonne = e.id_abonne

	$contenu .= '<table border="1">';
	$contenu .= '<tr>';
	
	$contenu .= '<th>Référence de l\'avis</th>';
	$contenu .= '<th>Membre</th>';
	$contenu .= '<th>Salle</th>';
	$contenu .= '<th>Commentaire</th>';
	$contenu .= '<th>Note</th>';
	$contenu .= '<th>Date</th>';
	$contenu .= '<th colspan="3">Actions</th>';
	$contenu .= '</tr>';

	while($avis = $resultat -> fetch(PDO::FETCH_ASSOC)){
		$contenu .= '<tr>';
		$contenu .= '<td>' . $avis['id_avis'] . '</td>';
		$contenu .= '<td>' . $avis['id_membre'] . ' - ' . $avis['email'] . '</td>';
		$contenu .= '<td>' . $avis['id_salle'] . ' - ' . $avis['titre'] . '</td>';
		$contenu .= '<td>' . $avis['commentaire'] . '</td>';
		foreach($avis as $indice => $valeur){
			if($indice == 'note'){
				if($valeur == '1'){
					$contenu .= '<td>★</td>';
				}
				elseif($valeur == '2'){
					$contenu .= '<td>★★</td>'; 
				}
				elseif($valeur == '3'){
					$contenu .= '<td>★★★</td>'; 
				}
				elseif($valeur == '4'){
					$contenu .= '<td>★★★★</td>'; 
				}
				elseif($valeur == '5'){
					$contenu .= '<td>★★★★★</td>'; 
				}
			}
		}
		$contenu .= '<td>' . $avis['date_enregistrement'] . '</td>';
		$contenu .= '<td><a href="?action=voir&id=' . $avis['id_membre'] . '"><img src="' . RACINE_SITE . 'img/view.png"/></a></td>';
		$contenu .= '<td><a href="?action=suppression&id=' . $avis['id_membre'] . '"><img src="' . RACINE_SITE . 'img/delete.png"/></a></td>';
		$contenu .= '<td><a href="?action=modification&id=' . $avis['id_membre'] . '"><img src="' . RACINE_SITE . 'img/edit.png"/></a></td>';
		
		$contenu .= '</tr>'; 
	}
	$contenu .= '</table>';


echo $contenu;

?>

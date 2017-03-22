<?php require_once('inc/header.inc.php'); ?>

<?php
//Traitements pour récupérer toutes les catégories :
$resultat = $pdo -> query("SELECT DISTINCT categorie FROM salle");
$cat = $resultat -> fetchAll(PDO::FETCH_ASSOC);

//Traitements pour récupérer toutes les villes :
$resultat = $pdo -> query("SELECT DISTINCT ville FROM salle");
$ville = $resultat -> fetchAll(PDO::FETCH_ASSOC);

//Traitements pour récupérer tous les produits :
$resultat = $pdo -> query("SELECT p.*, s.* FROM produit p, salle s WHERE p.id_salle = s.id_salle");
$produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);

?>
<?php $page = 'Accueil'; ?>
<?php require_once('inc/header.inc.php'); ?>



<!-- ------------------ Contenu HTML ------------------ -->
<main class="conteneur">
    <section id="accueil">
        <div class="col-md-2">
            <div class="categorie">
                <p>Catégorie :</p>
	            <ul>
                    <?php foreach($cat as $valeur) : ?>
                    <li><a href="?categorie=<?= $valeur['categorie'] ?>"><?= $valeur['categorie'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="ville">
                <p>Ville :</p>
	            <ul>
                    <?php foreach($ville as $valeur) : ?>
                    <li><a href="?ville=<?= $valeur['ville'] ?>"><?= $valeur['ville'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="prix">
                <label>Prix :</label>
                <input type="range" min="0" max="5000">
                
            </div>
        </div>

        </div>
        <div class="col-md-10">
            <?php foreach($produits as $valeur) : ?> 
                <!-- début vignette produit -->
                <div class="index-produit">
                    <a href="fiche_produit.php?id=<?= $valeur['id_produit'] ?>"><img src="<?= RACINE_SITE . 'img/' . $valeur['photo'] ?>" height="100"/></a>
                    <h3><?= $valeur['titre'] ?></h3>
                    <p><?= $valeur['prix'] ?>€</p>
                    <p><?= $valeur['date_arrivee'] ?></p>
                    <p><?= $valeur['date_depart'] ?></p>
                    <p><?= $valeur['description'] ?></p>

                    <p style="height: 40px;"><?= substr($valeur['description'], 0, 45) . '...' ?></p>
                    <a href="fiche_produit.php?id=<?= $valeur['id_produit'] ?>">Voir la fiche</a>
                </div>
                <!-- fin vignette produit -->
            <?php endforeach; ?>

        </div>



    </section>
</main>
<?php require_once('inc/footer.inc.php'); ?>
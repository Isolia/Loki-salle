<?php require_once('init.inc.php'); ?>
<?php 
// récupérer les infos de User
//debug($_SESSION['membre']);
extract($_SESSION['membre']);
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loki Salle | <?= $page ?> </title>
    <link rel="stylesheet" href="<?= RACINE_SITE ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= RACINE_SITE ?>css/style.css"/>
</head>
<body>
<header>
    <div class="conteneur"> 
        <nav id="mainNav" class="navbar navbar-default navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand page-scroll" href="#page-top">Loki Salle</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">

                        <!-- Si l'utilisateur EST CONNECTE -->
                        <?php if(userConnecte()) : ?>
                        <li>
                            <p>Bienvenue <?= $pseudo ?> !</p>
                        </li>
                        <li>
                            <a <?= ($page == 'Accueil') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>index.php">Accueil</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Profil') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>profil.php">Profil</a>
				        </li>
                        <li>
                            <a href="<?= RACINE_SITE ?>connexion.php?action=deconnexion">Deconnexion</a>
                        </li>

                         <!-- Si l'utilisateur n'est PAS CONNECTE -->
                        <?php else : ?>
                        <li>
                            <a <?= ($page == 'Accueil') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>index.php">Accueil</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Connexion') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>connexion.php">Connexion</a>
				        </li>
                        <li>
                            <a <?= ($page == 'Inscription') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>inscription.php">Inscription</a>
				        </li>

                        <!-- Si l'utilisateur est ADMIN -->
                        <?php endif; ?>
					
                        <?php if(userAdmin()) : ?>
                        <li>
                            <a <?= ($page == 'Gestion des salles') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>backoffice/gestion_salle.php">Gestion des salles</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Gestion des produits') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>backoffice/gestion_produits.php">Gestion des produits</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Gestion des membres') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>backoffice/gestion_membres.php">Gestion des membres</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Gestion des avis') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>backoffice/gestion_avis.php">Gestion des avis</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Gestion des commandes') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>backoffice/gestion_commandes.php">Gestion des commades</a>
                        </li>
                        <li>
                            <a <?= ($page == 'Statistiques') ? 'class=active' : '' ?> href="<?= RACINE_SITE ?>backoffice/gestion_statistiques.php">Statistiques</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </div> <!-- END div conteneur -->
</header>
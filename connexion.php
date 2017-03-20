<?php
require_once('inc/init.inc.php');

// Traitement pour la déconnexion
// connexion.php?action=deconnexion
if(isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
	unset($_SESSION['membre']);
}

// Redirection si user est connecté
if(userConnecte()){
	header('location:index.php');
}
// Traitement pour la connexion
if($_POST){
    // Verifier si le pseudo existe
    if(!empty($_POST['pseudo'])){ // Si le pseudo n'est pas vide... 
        $resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
        $resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
        $resultat -> execute();

        // Le pseudo existe bien dans la BDD donc :
        if($resultat -> rowCount() > 0){
            // On vérifie si le MDP correspond bien en BDD
            $membre = $resultat -> fetch(PDO::FETCH_ASSOC);
            
            debug($membre);
            // Si le MDP correspond :
            if($membre['mdp'] == ($_POST['mdp'])){
                
                foreach($membre as $indice => $valeur){
                    if($indice != 'mdp'){
                        $_SESSION['membre'][$indice] = $valeur;
                    }
                }
                debug($_SESSION);
                // Une fois connecté, redirection vers ... 
                header('location:index.php');
            }
            else{
                $msg .= '<div class="erreur">Mot de passe incorrect !</div>';
            }
        }
        else{
            $msg .= '<div class="erreur">Pseudo incorrect !</div>';
        }
    }
    else{
        $msg .= '<div class="erreur">Veuillez renseigner un pseudo !</div>';
    }
} // END if($_POST)

$page = 'Connexion';
require_once('inc/header.inc.php');
?>

<!-- ------------------ Contenu HTML ------------------ -->
<main>
    <section id="page-connexion">
        <h1>Connexion</h1>
        <?= $msg ?>

        <form action="" method="post">
            <label>Pseudo :</label><br/>
            <input type="text" name="pseudo"/><br/><br/>

            <label>Mot de passe :</label><br/>
            <input type="password" name="mdp" /><br/><br/>
            
            <input type="submit" value="Connexion" name="connexion"/>
            
        </form>
    </section>
</main>
<?php
require_once('inc/footer.inc.php');
?>
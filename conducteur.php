<?php
$title = 'Conducteur';
include_once(__DIR__.'/inc/db.php');
include_once(__DIR__.'/inc/header.php');
?>
<main>

    <h1>Conducteur</h1>

    <?php
    // ======================================== Suppresion : ========================================

    // Vérifier si id_conducteur et del sont définis
    if (isset($_GET['id_conducteur']) and isset($_GET['del'])) {

        $del = strip_tags($_GET['del']); // strip_tags() supprime toutes les balises HTML
        $id_conducteur = intval( strip_tags($_GET['id_conducteur']) ); // intval() met en nb entier

        if ((!is_numeric($id_conducteur)) or ($del !== 'yes')) {
            header('Location: conducteur.php');
            exit;
        }

        // Si id_conducteur récupérée par l'url est supérieure au nombre d'id_conducteur dans la BDD, alors on redirige :
        $query0 = $db->query('SELECT * FROM conducteur ORDER BY id_conducteur DESC LIMIT 1'); // Avec DESC LIMIT 1, on récup la dernière ligne
        $result0 = $query0->fetch();
        $id_conducteur_nb = $result0['id_conducteur'];
        if ($id_conducteur > $id_conducteur_nb) {
            header('Location: conducteur.php');
            exit;
        }

        // $db->query('DELETE FROM conducteur WHERE id_conducteur='.$id_conducteur); // Effacer dans la BDD sans méthode prepare.

        $query = $db->prepare('DELETE FROM conducteur WHERE id_conducteur = :id_conducteur'); // Effacer dans la BDD avec méthode prepare.
        $query->bindValue(':id_conducteur', $id_conducteur, PDO::PARAM_INT);

        // $query->execute();
        if ($query->execute()) {
            // Logger IP de l'effaceur :
            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $navigateur = $_SERVER["HTTP_USER_AGENT"];
            $file = fopen('ip.log', 'a+'); // Ouvrir le fichier et place le pointeur (le curseur) à la fin
            fwrite($file, 'Conducteur effacé par: '.$_SERVER['REMOTE_ADDR']." / ".$hostname." / ".$navigateur." / Date: ".date("d-m-Y H:i:s")."<br>\n");
            fclose($file);

            $count = $query->rowCount(); // rowCount() retourne le nombre de lignes effacées
            echo '<h2>Le conducteur est supprimé !</h2>';
            echo '<p class="center">Effacement de '.$count.' ligne(s) dans la BDD</p>';

        }

    }


    // ======================================== Modification : ========================================

    if (isset($_GET['id_conducteur']) and isset($_GET['edit'])) {

        $edit = strip_tags($_GET['edit']); // strip_tags() supprime toutes les balises HTML
        $id_conducteur = intval( strip_tags($_GET['id_conducteur']) ); // intval() met en nb entier
        if ((!is_numeric($id_conducteur)) or ($edit !== 'yes')) {
            header('Location: conducteur.php');
            exit;
        }

        // Si id_conducteur récupérée par l'url est supérieure au nombre d'id_conducteur dans la BDD, alors on redirige :
        $query0 = $db->query('SELECT * FROM conducteur ORDER BY id_conducteur DESC LIMIT 1');
        $result0 = $query0->fetch(); // Avec DESC LIMIT 1, on récup la dernière ligne
        $id_conducteur_nb = $result0['id_conducteur'];
        if ($id_conducteur > $id_conducteur_nb) {
            header('Location: conducteur.php');
            exit;
        }

        $query= $db->query('SELECT * FROM conducteur WHERE id_conducteur='.$id_conducteur);
        $result = $query->fetch();

        echo '<h2>Modifier id_conducteur '.$id_conducteur.' :</h2><br>
            <form method="post">

                <label for="prenom">Prénom : <input type="text" name="prenom" id="prenom" maxlength="255" value="'.$result['prenom'].'"></label><br>
                <label for="nom">Nom : <input type="text" name="nom" id="nom" maxlength="255" value="'.$result['nom'].'"></label><br>

                <button type="submit">Valider</button>

            </form>';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $prenom = $_POST['prenom'];
            $nom = $_POST['nom'];

            $erreur = "";
            if (strlen($prenom) < 2 or strlen($prenom) > 255) $erreur .= '<h2>Mettez un prénom correct !</h2>'; // Concaténation du message d'erreur dans la variable $erreur
            if (strlen($nom) < 2 or strlen($nom) > 255) $erreur .= '<h2>Mettez un nom correct !</h2>';
            if(strlen($erreur) > 0) exit($erreur); // Si la longueur du message d'erreur est > 0, alors on fait exit en affichant le message d'erreur
  

            // Modif dans la BDD (avec méthode prepare) :
            $query = $db->prepare('UPDATE conducteur SET id_conducteur = :id_conducteur, prenom = :prenom, nom = :nom WHERE id_conducteur = :id_conducteur');

            $query->bindValue(':id_conducteur', $id_conducteur, PDO::PARAM_INT);
            $query->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $query->bindValue(':nom', $nom, PDO::PARAM_STR);

            $query->execute();

            echo '<h2>Le conducteur '.$id_conducteur.' est modifié !</h2><br>';
            echo '<script>
                    setTimeout(function(){
                        window.location = "conducteur.php";
                    }, 3000);
                </script>';
        }

    }


    // ======================================== Affichage des conducteurs : ========================================
    ?>

    <hr>
    <div class="flex">

        <div class="info b">
            id_conducteur<br>
        </div>

        <div class="info b">
            prenom
        </div>

        <div class="info b">
            nom
        </div>

        <div class="info b">
            modification
        </div>

        <div class="info b">
            suppression
        </div>

    </div>

    <?php

    $query = $db->query('SELECT * FROM conducteur ORDER BY id_conducteur ASC');
    $results = $query->fetchAll();

    foreach ($results as $result) {
        echo '<hr>
        <div class="flex">

            <div class="info">
                '.$result['id_conducteur'].'
            </div>

            <div class="info">
                '.$result['prenom'].'
            </div>

            <div class="info">
                '.$result['nom'].'
            </div>

            <div class="info">
                <a href="?edit=yes&id_conducteur='.$result['id_conducteur'].'">🖍</a>
            </div>

            <div class="info">
                <a href="?del=yes&id_conducteur='.$result['id_conducteur'].'" onclick="if(window.confirm(\'Voulez-vous vraiment supprimer ?\')) {return true;} else {return false;}">✖</a>
            </div>

        </div>';
    }


    // ======================================== Formulaire - Ajout de conducteurs : ========================================
    ?>
    <br>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

        <label for="prenom">Prénom :<br>
            <input type="text" name="prenom" id="prenom" maxlength="255" placeholder="Prénom">
        </label><br>

        <label for="nom">Nom :<br>
            <input type="text" name="nom" id="nom" maxlength="255" placeholder="Nom">
        </label><br>

        <button type="submit">Ajouter ce conducteur</button>

    </form>

    <?php
    // POST sans variable dans l'url :
    if ( ($_SERVER["REQUEST_METHOD"] == "POST") and (!isset($_GET['id_conducteur'])) and (!isset($_GET['del'])) and (!isset($_GET['edit'])) ) {
        $prenom = $nom = null;

        $prenom = strip_tags($_POST['prenom']); // strip_tags() supprime toutes les balises HTML
        $nom = strip_tags($_POST['nom']);

        $erreur = "";
        if ((strlen($prenom) < 2) or (strlen($prenom) > 255)) $erreur .= '<h2>Mettez un prénom correct !</h2>';
        if ((strlen($nom) < 2) or (strlen($nom) > 255)) $erreur .= '<h2>Mettez un nom correct !</h2>';
        if (strlen($erreur) > 0) exit($erreur);

        // Ajout de conducteur dans la BDD :

        $query = $db->prepare('INSERT INTO conducteur (`prenom`, `nom`) VALUES (:prenom, :nom)');

        $query->bindValue(':prenom', $prenom, PDO::PARAM_STR);
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);

        // $query->execute();
        if ($query->execute()) {

            // Logger le personnel qui ajoute dans la BDD :
            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']); // Nom du serveur hôte
            $navigateur = $_SERVER["HTTP_USER_AGENT"]; // Navigateur utilisé
            $file = fopen('ip.log', 'a+'); // Ouvrir le fichier et placer le pointeur (le curseur) à la fin
            fwrite($file, 'Conducteur ajouté par: '.$_SERVER['REMOTE_ADDR']." / ".$hostname." / Navigateur: ".$navigateur." / Date: ".date("d-m-Y H:i:s")."<br>\n");
            fclose($file);

            echo '<h2>Le conducteur '.$prenom.' '.$nom.' est ajouté !</h2>';
            echo '<script>
                    setTimeout(function(){
                        window.location = "conducteur.php";
                    }, 3000);
                </script>';

        }

    }
    ?>

</main>

<?php
include_once(__DIR__.'/inc/footer.php');
?>
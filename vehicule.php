<?php
$title = 'Véhicule';
include_once(__DIR__.'/inc/db.php');
include_once(__DIR__.'/inc/header.php');
?>
    <main>

      <h1>Véhicule</h1>

      <?php
      // ======================================== Suppresion : ========================================

      // Vérifier si id_vehicule et del sont définis
      if (isset($_GET['id_vehicule']) and isset($_GET['del']))
      {

        $del = strip_tags($_GET['del']); // strip_tags() supprime toutes les balises HTML
        $id_vehicule = intval( strip_tags($_GET['id_vehicule']) ); // intval() met en nb entier

        if ((!is_numeric($id_vehicule)) or ($del !== 'yes')) {
          header('Location: vehicule.php');
          exit;
        }

        // Si id_vehicule récupérée par l'url est supérieure au nombre d'id_vehicule dans la BDD, alors on redirige :
        $query0 = $db->query('SELECT * FROM vehicule ORDER BY id_vehicule DESC LIMIT 1');
        $result0 = $query0->fetch(); // Avec DESC LIMIT 1, on récup la dernière ligne
        $id_vehicule_nb = $result0['id_vehicule'];
        if ($id_vehicule > $id_vehicule_nb) {
          header('Location: vehicule.php');
          exit;
        }

        // $db->query('DELETE FROM vehicule WHERE id_vehicule='.$id_vehicule); // Effacer dans la BDD sans méthode prepare.
        
        $query = $db->prepare('DELETE FROM vehicule WHERE id_vehicule = :id_vehicule'); // Effacer dans la BDD avec méthode prepare.
        $query->bindValue(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
        
        // $query->execute();
        if ($query->execute())
        {
          // Logger IP de l'effaceur :
          $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
          $navigateur = $_SERVER["HTTP_USER_AGENT"];
          $file = fopen('ip.log', 'a+'); // Ouvrir le fichier et place le pointeur (le curseur) à la fin
          fwrite($file, 'Véhicule effacé par: '.$_SERVER['REMOTE_ADDR']." / ".$hostname." / ".$navigateur." / Date: ".date("d-m-Y H:i:s")."<br>\n");
          fclose($file);

          $count = $query->rowCount(); // rowCount() retourne le nombre de lignes effacées
          echo '<h2>Le vehicule est supprimé !</h2>';
          echo '<p class="center">Effacement de '.$count.' ligne(s) dans la BDD</p>';

        }
  
      }


      // ======================================== Modification : ========================================

      if (isset($_GET['id_vehicule']) and isset($_GET['edit']))
      {

        $edit = strip_tags($_GET['edit']); // strip_tags() supprime toutes les balises HTML
        $id_vehicule = intval( strip_tags($_GET['id_vehicule']) ); // intval() met en nb entier
        if ((!is_numeric($id_vehicule)) or ($edit !== 'yes')) {
          header('Location: vehicule.php');
          exit;
        }

        // Si id_vehicule récupérée par l'url est supérieure au nombre d'id_vehicule dans la BDD, alors on redirige :
        $query0 = $db->query('SELECT * FROM vehicule ORDER BY id_vehicule DESC LIMIT 1');
        $result0 = $query0->fetch(); // Avec DESC LIMIT 1, on récup la dernière ligne
        $id_vehicule_nb = $result0['id_vehicule'];
        if ($id_vehicule > $id_vehicule_nb) {
          header('Location: vehicule.php');
          exit;
        }

        $query= $db->query('SELECT * FROM vehicule WHERE id_vehicule='.$id_vehicule);
        $result = $query->fetch();

        echo '<h2>Modifier id_vehicule '.$id_vehicule.' :</h2><br>
              <form method="post">
      
                <label for="marque">Marque : <input type="text" name="marque" id="marque" maxlength="255" value="'.$result['marque'].'"></label><br>
                <label for="modele">Modèle : <input type="text" name="modele" id="modele" maxlength="255" value="'.$result['modele'].'"></label><br>
                <label for="couleur">Couleur : <input type="text" name="couleur" id="couleur" maxlength="255" value="'.$result['couleur'].'"></label><br>
                <label for="immatriculation">Immatriculation : <input type="text" name="immatriculation" id="immatriculation" maxlength="255" value="'.$result['immatriculation'].'"></label><br>

                <button type="submit">Valider</button>

              </form>';
        
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
          $marque = $_POST['marque'];
          $modele = $_POST['modele'];
          $couleur = $_POST['couleur'];
          $immatriculation = $_POST['immatriculation'];

          $erreur = "";
  
          if (strlen($marque) < 2 or strlen($marque) > 255) {
            $erreur .= '<h2>Marque incorrect !</h2>';  // Concaténation du message d'erreur dans la variable $erreur
          }

          if (strlen($modele) < 2 or strlen($modele) > 255) {
            $erreur .= '<h2>Modèle incorrect !</h2>';
          }

          if (strlen($couleur) < 2 or strlen($couleur) > 255) {
            $erreur .= '<h2>Couleur incorrect !</h2>';
          }

          if (strlen($immatriculation) < 2 or strlen($immatriculation) > 99) {
            $erreur .= '<h2>Immatriculation incorrect !</h2>';
          }
  
          if(strlen($erreur) > 0) {
            exit($erreur); // Si la longueur du message d'erreur est > 0, alors on fait exit en affichant le message d'erreur
          }
  
          // Modif dans la BDD (avec méthode prepare) :
          $query = $db->prepare('UPDATE vehicule SET id_vehicule = :id_vehicule, marque = :marque, modele = :modele, couleur = :couleur, immatriculation = :immatriculation WHERE id_vehicule = :id_vehicule');
  
          $query->bindValue(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
          $query->bindValue(':marque', $marque, PDO::PARAM_STR);
          $query->bindValue(':modele', $modele, PDO::PARAM_STR);
          $query->bindValue(':couleur', $couleur, PDO::PARAM_STR);
          $query->bindValue(':immatriculation', $immatriculation, PDO::PARAM_STR);

          $query->execute();
  
          echo '<h2>Le vehicule id_vehicule '.$id_vehicule.' est modifié !</h2><br>';
          echo '<script>
                setTimeout(function(){
                  window.location = "vehicule.php";
                }, 3000);
                </script>';
        }

      }


      // ======================================== Affichage des vehicules : ========================================
      ?>

      <hr>
              <div class="flex">

                <div class="info b">
                  id_vehicule<br>
                </div>

                <div class="info b">
                  marque
                </div>

                <div class="info b">
                  modele
                </div>

                <div class="info b">
                  couleur
                </div>

                <div class="info b">
                  immatriculation
                </div>

                <div class="info b">
                  modification
                </div>

                <div class="info b">
                  suppression
                </div>

              </div>

      <?php
      $query = $db->query('SELECT * FROM vehicule ORDER BY id_vehicule ASC');
      $results = $query->fetchAll();

      foreach ($results as $result)
      {
        echo '<hr>
              <div class="flex">

                <div class="info">
                  '.$result['id_vehicule'].'
                </div>

                <div class="info">
                  '.$result['marque'].'
                </div>

                <div class="info">
                  '.$result['modele'].'
                </div>

                <div class="info">
                  '.$result['couleur'].'
                </div>

                <div class="info">
                  '.$result['immatriculation'].'
                </div>

                <div class="info">
                  <a href="?edit=yes&id_vehicule='.$result['id_vehicule'].'">🖍</a>
                </div>

                <div class="info">
                  <a href="?del=yes&id_vehicule='.$result['id_vehicule'].'" onclick="if(window.confirm(\'Voulez-vous vraiment supprimer ?\')) {return true;} else {return false;}">X</a>
                </div>

              </div>';
      }


      // ======================================== Formulaire - Ajout de vehicules : ========================================
      ?>
      <br>

      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">

        <label for="marque">Marque :<br>
          <input type="text" name="marque" id="marque" maxlength="255" placeholder="Marque">
        </label><br>

        <label for="modele">Modèle :<br>
          <input type="text" name="modele" id="modele" maxlength="255" placeholder="Modèle">
        </label><br>

        <label for="couleur">Couleur :<br>
          <input type="text" name="couleur" id="couleur" maxlength="255" placeholder="Modèle">
        </label><br>

        <label for="immatriculation">Immatriculation :<br>
          <input type="text" name="immatriculation" id="immatriculation" maxlength="255" placeholder="Immatriculation">
        </label><br>

        <button type="submit">Ajouter ce vehicule</button>

      </form>

      <?php
      // POST sans variable dans l'url :
      if ( ($_SERVER["REQUEST_METHOD"] == "POST") and (!isset($_GET['id_vehicule'])) and (!isset($_GET['del'])) and (!isset($_GET['edit'])) )
      {
        $marque = $modele = $couleur = $immatriculation = null;

        $marque = strip_tags($_POST['marque']); // strip_tags() supprime toutes les balises HTML
        $modele = strip_tags($_POST['modele']);
        $couleur = strip_tags($_POST['couleur']);
        $immatriculation = strip_tags($_POST['immatriculation']);

        $erreur = "";
        if (strlen($marque) < 2 or strlen($marque) > 255) $erreur .= '<h2>Marque incorrect !</h2>';
        if (strlen($modele) < 2 or strlen($modele) > 255) $erreur .= '<h2>Modèle incorrect !</h2>';
        if (strlen($couleur) < 2 or strlen($couleur) > 255) $erreur .= '<h2>Couleur incorrect !</h2>';
        if ((strlen($immatriculation) < 2) or (strlen($immatriculation) > 99)) $erreur .= '<h2>Immatriculation incorrect !</h2>';
        if(strlen($erreur) > 0) exit($erreur);


        // ----- Ajout de vehicule dans la BDD : -----

        $query = $db->prepare('INSERT INTO vehicule (marque, modele, couleur, immatriculation) VALUES (:marque, :modele, :couleur, :immatriculation)');

        $query->bindValue(':marque', $marque, PDO::PARAM_STR);
        $query->bindValue(':modele', $modele, PDO::PARAM_STR);
        $query->bindValue(':couleur', $couleur, PDO::PARAM_STR);
        $query->bindValue(':immatriculation', $immatriculation, PDO::PARAM_STR);

        // $query->execute();
        if ($query->execute()) {

          // ----- Logger le personnel qui ajoute dans la BDD : -----
          $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']); // Nom du serveur hôte
          $navigateur = $_SERVER["HTTP_USER_AGENT"]; // Navigateur utilisé
          $file = fopen('ip.log', 'a+'); // Ouvrir le fichier et placer le pointeur (le curseur) à la fin
          fwrite($file, 'Véhicule ajouté par: '.$_SERVER['REMOTE_ADDR']." / ".$hostname." / Navigateur: ".$navigateur." / Date: ".date("d-m-Y H:i:s")."<br>\n");
          fclose($file);

          echo '<h2>Le vehicule immatriculé '.$immatriculation.' est ajouté !</h2>';
          echo '<script>
                setTimeout(function(){
                  window.location = "vehicule.php";
                }, 3000);
                </script>';

        }

      }
      ?>


    </main>

<?php
include_once(__DIR__.'/inc/footer.php');
?>
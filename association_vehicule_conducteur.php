<?php
$title = 'Association Vehicule-Conducteur';
include_once(__DIR__.'/inc/db.php');
include_once(__DIR__.'/inc/header.php');
?>
    <main>

      <h1>Association Vehicule-Conducteur</h1>

      <?php
      
      // ======================================== Suppresion : ========================================

      // Si id_association et del sont définis
      if (isset($_GET['id_association']) and isset($_GET['del']))
      {
        $del = strip_tags($_GET['del']);
        $id_association = strip_tags($_GET['id_association']);

        if ((!is_numeric($id_association)) or ($del !== 'yes')) {
          header('Location: association_vehicule_conducteur.php');
          exit;
        }
        
        $query = $db->prepare('DELETE FROM association_vehicule_conducteur WHERE id_association = :id_association'); // Effacer dans la BDD avec méthode prepare
        $query->bindValue(':id_association', $id_association, PDO::PARAM_INT);
        
        if ($query->execute())
        {
          // Logger IP de l'effaceur :
          $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
          $navigateur = $_SERVER["HTTP_USER_AGENT"];
          $file = fopen('ip.log', 'a+'); // Ouvrir le fichier et place le pointeur (le curseur) à la fin
          fwrite($file, 'Association effacé par: '.$_SERVER['REMOTE_ADDR']." / ".$hostname." / ".$navigateur." / Date: ".date("d-m-Y H:i:s")."<br>\n");
          fclose($file);

          $count = $query->rowCount(); // rowCount() retourne le nombre de lignes effacées
          echo '<h2>Association supprimé !</h2>';
          echo '<p class="center">Effacement de '.$count.' ligne(s) dans la BDD</p>';

        }
  
      }


      // ======================================== Modification : ========================================

      if (isset($_GET['id_association']) and isset($_GET['edit']))
      {

        $edit = strip_tags($_GET['edit']);
        $id_association = strip_tags($_GET['id_association']);
        if ((!is_numeric($id_association)) or ($edit !== 'yes')) {
          header('Location: association_vehicule_conducteur.php');
          exit;
        }

        $query= $db->query('SELECT * FROM association_vehicule_conducteur WHERE id_association='.$id_association);
        $result = $query->fetch();

        echo '<h2>Modifier id_association '.$id_association.' :</h2><br>
              <form method="post">
      
                <label for="id_conducteur">Conducteur : <input type="text" name="id_conducteur" id="id_conducteur" maxlength="255" value="'.$result['id_conducteur'].'"></label><br>
                <label for="id_vehicule">Véhicule : <input type="text" name="id_vehicule" id="id_vehicule" maxlength="255" value="'.$result['id_vehicule'].'"></label><br>

                <button type="submit">Valider</button>

              </form>';
        
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
          $id_conducteur = $_POST['id_conducteur'];
          $id_vehicule = $_POST['id_vehicule'];

          $erreur = "";
          if (strlen($id_conducteur) < 1 or strlen($id_conducteur) > 255) $erreur .= '<h2>Mettez un id_conducteur correct !</h2>';
          if (strlen($id_vehicule) < 1 or strlen($id_vehicule) > 255) $erreur .= '<h2>Mettez un id_vehicule correct !</h2>';
          if(strlen($erreur) > 0) exit($erreur);    
  
          // Modif dans la BDD avec méthode prepare :
          $query = $db->prepare('UPDATE association_vehicule_conducteur SET id_association = :id_association, id_conducteur = :id_conducteur, id_vehicule = :id_vehicule WHERE id_association = :id_association');
  
          $query->bindValue(':id_association', $id_association, PDO::PARAM_INT);
          $query->bindValue(':id_conducteur', $id_conducteur, PDO::PARAM_INT);
          $query->bindValue(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
  
          if ($query->execute()) {
            echo '<h2>Le conducteur '.$id_association.' est modifié !</h2><br>';
            echo '<script>
                  setTimeout(function(){
                    window.location = "association_vehicule_conducteur.php";
                  }, 3000);
                  </script>';
          }
  
        }

      }


      //======================================== Affichage des associations : ========================================
      ?>

      <hr>
              <div class="flex">

                <div class="info b">
                  id_association<br>
                </div>

                <div class="info b">
                  conducteur
                </div>

                <div class="info b">
                  véhicule
                </div>

                <div class="info b">
                  modification
                </div>

                <div class="info b">
                  suppression
                </div>

              </div>

      <?php

      // $query = $db->query('SELECT ass.*, con.*
      // FROM association_vehicule_conducteur ass INNER JOIN conducteur con
      //   ON ass.id_conducteur = con.id_conducteur'); // Associer seulement 2 tables
      // $results = $query->fetchAll();

      $query = $db->query('SELECT * FROM (association_vehicule_conducteur
                                    INNER JOIN conducteur ON association_vehicule_conducteur.id_conducteur = conducteur.id_conducteur)
                                    INNER JOIN vehicule ON association_vehicule_conducteur.id_vehicule = vehicule.id_vehicule'); // Associer 3 tables

      // Possible d'utilisation d'alias : ass.*, con.*, veh.*
      // $query = $db->query('SELECT ass.*, con.*, veh.* FROM (association_vehicule_conducteur ass
      //                                                 INNER JOIN conducteur con ON ass.id_conducteur = con.id_conducteur)
      //                                                 INNER JOIN vehicule veh ON ass.id_vehicule = veh.id_vehicule'); // Associer 3 tables

      $results = $query->fetchAll();

      foreach ($results as $result)
      {
        echo '<hr>
              <div class="flex">

                <div class="info">
                  '.$result['id_association'].'
                </div>

                <div class="info">
                  '.$result['prenom'].' '.$result['nom'].'<br>
                  '.$result['id_conducteur'].'
                </div>

                <div class="info">
                  '.$result['marque'].' '.$result['modele'].'<br>
                  '.$result['id_vehicule'].'
                </div>

                <div class="info">
                  <a href="?edit=yes&id_association='.$result['id_association'].'">🖍</a>
                </div>

                <div class="info">
                  <a href="?del=yes&id_association='.$result['id_association'].'" onclick="if(window.confirm(\'Voulez-vous vraiment supprimer ?\')) {return true;} else {return false;}">✖</a>
                </div>

              </div>';
      }


      // ======================================== Formulaire : Ajout d'association : ========================================
      ?>
      <br>

      <form method="post">

        <label for="id_conducteur">Conducteur :<br>
          <select name="id_conducteur" id="id_conducteur">
            <option value="">Choisir le conducteur</option>
            <?php          
            foreach ($results as $result)
            {
              echo '<option>'.$result['id_conducteur'].' - '.$result['prenom'].' '.$result['nom'].'</option>';
            }
            ?>
          </select>
        </label><br class="s-margin">

        <label for="id_vehicule">Véhicule :<br>
          <select name="id_vehicule" id="id_vehicule">
            <option value="">Choisir le véhicule</option>
            <?php           
            foreach ($results as $result)
            {
              echo '<option>'.$result['id_vehicule'].' - '.$result['marque'].' '.$result['modele'].'</option>';
            }
            ?>
          </select>

        </label><br>

        <button type="submit">Ajouter cette association</button>

      </form>



      <?php
      // POST sans variable dans l'url :
      if ( ($_SERVER["REQUEST_METHOD"] == "POST") and (!isset($_GET['id_association'])) and (!isset($_GET['del'])) and (!isset($_GET['edit'])) )
      {
        $id_conducteur = $id_vehicule = null;

        $id_conducteur = $_POST['id_conducteur'];
        $id_vehicule = $_POST['id_vehicule'];

        $erreur = "";
        if (strlen($id_conducteur) < 2 or strlen($id_conducteur) > 255) $erreur .= '<h2>Mettez un id_conducteur correct !</h2>';
        if (strlen($id_vehicule) < 2 or strlen($id_vehicule) > 255) $erreur .= '<h2>Mettez un id_vehicule correct !</h2>';
        if(strlen($erreur) > 0) exit($erreur);
        

        // ----- Modif (Association) dans la BDD avec requete preparée : MARCHE PAS !!! ----
        /*
        $query = $db->prepare('UPDATE `association_vehicule_conducteur` SET `id_conducteur` = :id_conducteur WHERE `association_vehicule_conducteur`.`id_association` = :id_association');
        
        $query->bindValue(':id_conducteur', $id_conducteur, PDO::PARAM_INT);
        $query->bindValue(':id_vehicule', $id_vehicule, PDO::PARAM_INT);
        */

        // ----- Modif (Association) dans la BDD sans requete preparée : MARCHE PAS !!! ----
        
        $query = $db->query("INSERT INTO `association_vehicule_conducteur` (`id_association`, `id_vehicule`, `id_conducteur`) VALUES (NULL, '507', '5');");


        if ($query->execute()) {

          echo '<h2>Mis à jour !</h2>';
          echo '<script>
                setTimeout(function(){
                  window.location = "association_vehicule_conducteur.php";
                }, 3000);
                </script>';

        }

      }
      ?>

    </main>

<?php
include_once(__DIR__.'/inc/footer.php');
?>
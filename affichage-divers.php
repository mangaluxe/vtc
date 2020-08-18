<?php
$title = 'Affichage divers';
include_once(__DIR__.'/inc/db.php');
include_once(__DIR__.'/inc/header.php');
?>
<main>

    <h1>Affichage divers</h1>

    <p>Afficher le nombre de conducteurs :</p>
    <?php
    $query = $db->query('SELECT COUNT(*) FROM conducteur');
    $result = $query->fetch();
    echo $result['COUNT(*)'];
    ?>

    <hr class="separate"> <!-- ============================================================================= -->

    <p>Afficher le nombre de vehicules :</p>
    <?php
    $query = $db->query('SELECT COUNT(*) FROM vehicule');
    $result = $query->fetch();
    echo $result['COUNT(*)'];
    ?>

    <hr class="separate"> <!-- ============================================================================= -->

    <p>Afficher le nombre d’associations :</p>
    <?php
    $query = $db->query('SELECT COUNT(*) FROM association_vehicule_conducteur');
    $result = $query->fetch();
    echo $result['COUNT(*)'];
    ?>

    <hr class="separate"> <!-- ============================================================================= -->



    <p>Afficher les vehicules n’ayant pas de conducteur :</p>

    <?php
    $query = $db->query('SELECT ass.*, veh.* FROM (vtc.association_vehicule_conducteur ass 
    RIGHT OUTER JOIN vtc.vehicule veh ON ass.id_vehicule = veh.id_vehicule)
    WHERE ass.id_association IS NULL');
    $results = $query->fetchAll();

    echo '<hr>
    <div class="flex">

        <div class="info b">
            id_vehicule
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

    </div>';

    foreach ($results as $result) {
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

        </div>';
    }
    ?>

    
    
    <hr class="separate"> <!-- ============================================================================= -->


    <p>Afficher les conducteurs n’ayant pas de vehicule :</p>

    <?php
    $query = $db->query('SELECT ass.*, con.* FROM (association_vehicule_conducteur ass 
    RIGHT OUTER JOIN conducteur con ON ass.id_conducteur = con.id_conducteur)
    WHERE ass.id_association IS NULL');
    $results = $query->fetchAll();

    echo '<hr>
    <div class="flex">

        <div class="info b">
            id_conducteur
        </div>

        <div class="info b">
            prenom
        </div>

        <div class="info b">
            nom
        </div>

    </div>';

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

        </div>';
    }
    ?>

    <hr class="separate"> <!-- ============================================================================= -->



    <p>Afficher les vehicules conduit par Philippe Pandre :</p>
    <?php
    $query = $db->query('SELECT * FROM (association_vehicule_conducteur
    INNER JOIN conducteur ON association_vehicule_conducteur.id_conducteur = conducteur.id_conducteur)
    INNER JOIN vehicule ON association_vehicule_conducteur.id_vehicule = vehicule.id_vehicule');
    $results = $query->fetchAll();

    echo '<hr>
    <div class="flex">

        <div class="info b">
            id_vehicule
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

    </div>';

    foreach ($results as $result) {
        if (($result['prenom'] == 'Philippe') and ($result['nom'] == 'Pandre')) {
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

            </div>';
        }

    }
    ?>


    <hr class="separate"> <!-- ============================================================================= -->




    <p>Afficher tous les conducteurs (meme ceux qui n'ont pas de correspondance) ainsi que les vehicules :</p>

    <?php
    // $query = $db->query('SELECT * FROM (association_vehicule_conducteur 
    // RIGHT OUTER JOIN conducteur ON association_vehicule_conducteur.id_conducteur = conducteur.id_conducteur)
    // INNER JOIN vehicule ON vehicule.id_vehicule = association_vehicule_conducteur.id_vehicule');

    // $query = $db->query('SELECT * FROM (association_vehicule_conducteur RIGHT OUTER JOIN conducteur ON association_vehicule_conducteur.id_conducteur = conducteur.id_conducteur)');

    $query = $db->query('SELECT C.*, V.* from conducteur as c LEFT JOIN association_vehicule_conducteur as A ON A.id_conducteur = c.id_conducteur LEFT JOIN vehicule as V ON V.id_vehicule = A.id_vehicule');

    $results = $query->fetchAll();
    
    echo '<hr>
    <div class="flex">

        <div class="info b">
            modele
        </div>

        <div class="info b">
            prenom
        </div>

    </div>';

    foreach ($results as $result) {
        echo '<hr>
        <div class="flex">

            <div class="info">';

                if (isset($result['modele'])) {
                    echo $result['modele'];
                }
                else {
                    echo 'NULL';
                }

            echo '</div>

            <div class="info">
                '.$result['prenom'].'
            </div>

        </div>';
    }
    ?>

    <hr class="separate"> <!-- ============================================================================= -->



    <p>Afficher les conducteurs et tous les vehicules (meme ceux qui n'ont pas de correspondance) :</p>

    <?php
    // $query = $db->query('SELECT * FROM (association_vehicule_conducteur
    // INNER JOIN conducteur ON association_vehicule_conducteur.id_conducteur = conducteur.id_conducteur)
    // INNER JOIN vehicule ON association_vehicule_conducteur.id_vehicule = vehicule.id_vehicule');

    $query = $db->query('SELECT C.*, V.* from vehicule as V LEFT JOIN association_vehicule_conducteur as A ON A.id_vehicule = V.id_vehicule LEFT JOIN conducteur as C ON C.id_conducteur = A.id_conducteur');


    
    $results = $query->fetchAll();

    echo '<hr>
    <div class="flex">

        <div class="info b">
            modele
        </div>

        <div class="info b">
            prenom
        </div>

    </div>';

    foreach ($results as $result) {
        echo '<hr>
        <div class="flex">

            <div class="info">
                '.$result['modele'].'
            </div>

            <div class="info">';

            if (isset($result['prenom'])) {
                echo $result['prenom'];
            }
            else {
                echo 'NULL';
            }

        echo '</div>

        </div>';
    }
    ?>

    <hr class="separate"> <!-- ============================================================================= -->



    <p>Afficher tous les conducteurs et tous les vehicules, peu importe les correspondances :</p>

    <?php
    // $query = $db->query('SELECT * FROM (association_vehicule_conducteur
    // INNER JOIN conducteur ON association_vehicule_conducteur.id_conducteur = conducteur.id_conducteur)
    // INNER JOIN vehicule ON association_vehicule_conducteur.id_vehicule = vehicule.id_vehicule');

    $query = $db->query('SELECT V.modele, C.prenom from conducteur as C LEFT JOIN association_vehicule_conducteur AS A ON C.id_conducteur = A.id_conducteur LEFT JOIN vehicule AS V ON V.id_vehicule = A.id_vehicule
    UNION
    SELECT V.modele, C.prenom from vehicule as V LEFT JOIN association_vehicule_conducteur AS A ON V.id_vehicule = A.id_vehicule LEFT JOIN conducteur AS C ON C.id_conducteur = A.id_conducteur');



    $results = $query->fetchAll();

    echo '<hr>
    <div class="flex">

        <div class="info b">
            modele
        </div>

        <div class="info b">
            prenom
        </div>

    </div>';

    foreach ($results as $result) {
        echo '<hr>
        <div class="flex">

            <div class="info">';

            if (isset($result['modele'])) {
                echo $result['modele'];
            }
            else {
                echo 'NULL';
            }

        echo '</div>

            <div class="info">';

            if (isset($result['prenom'])) {
                echo $result['prenom'];
            }
            else {
                echo 'NULL';
            }

        echo '</div>

        </div>';
    }
    ?>

</main>

<?php
include_once(__DIR__.'/inc/footer.php');
?>
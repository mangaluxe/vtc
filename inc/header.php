<?php
$nomsite = 'VTC';
$page_actuelle = basename($_SERVER['SCRIPT_FILENAME'], '.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>
  <?php
    echo $title.' - '.$nomsite;
  ?>
  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <nav class="navbar" id="top_navbar">
    <a href="javascript:void(0);" class="<?php echo ($page_actuelle === 'index') ? 'active' : ''; ?>">🏠</a>
    <a href="conducteur.php" class="<?php echo ($page_actuelle === 'conducteur') ? 'active' : ''; ?>">Conducteur</a>
    <a href="vehicule.php" class="<?php echo ($page_actuelle === 'vehicule') ? 'active' : ''; ?>">Véhicule</a>
    <a href="association_vehicule_conducteur.php" class="<?php echo ($page_actuelle === 'association_vehicule_conducteur') ? 'active' : ''; ?>">Association Véhicule-Conducteur</a>
    <a href="#" class="icon" onclick="function_menu()">&#9776;</a>
  </nav>

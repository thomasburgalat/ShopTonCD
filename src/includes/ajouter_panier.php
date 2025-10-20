<?php
session_start();

if (isset($_POST['cd_id']) && !empty($_POST['cd_id'])) {
    $cd_id = $_POST['cd_id'];

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$cd_id])) {
        $_SESSION['panier'][$cd_id]++;
    } else {
        $_SESSION['panier'][$cd_id] = 1;
    }
}

// Redirection vers la page d'accueil
header('Location: ../../public/index.php');
exit();
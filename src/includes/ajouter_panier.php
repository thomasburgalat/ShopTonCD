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


header('Location: index.php');
exit();

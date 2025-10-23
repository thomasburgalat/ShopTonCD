<?php

session_start();

// S'il n'y a pas de panier, on ne fait rien
if (!isset($_SESSION['panier'])) {
    header('Location: ../../public/panier.php');
    exit();
}

// --- CAS 1 : Mettre à jour la quantité (via le formulaire POST) ---
if (isset($_POST['cd_id']) && isset($_POST['quantite'])) {
    $cd_id = $_POST['cd_id'];
    $quantite = (int)$_POST['quantite']; // On convertit en nombre entier

    // Si la quantité est 0 ou moins, on supprime l'article
    if ($quantite <= 0) {
        if (isset($_SESSION['panier'][$cd_id])) {
            unset($_SESSION['panier'][$cd_id]);
        }
    } // Sinon, si l'article existe bien dans le panier, on met à jour la quantité
    elseif (isset($_SESSION['panier'][$cd_id])) {
        $_SESSION['panier'][$cd_id] = $quantite;
    }
}

// --- CAS 2 : Supprimer l'article (via le lien GET) ---
if (isset($_GET['supprimer_id'])) {
    $cd_id = $_GET['supprimer_id'];

    if (isset($_SESSION['panier'][$cd_id])) {
        unset($_SESSION['panier'][$cd_id]);
    }
}

// À la fin, on redirige toujours vers le panier
header('Location: ../../public/panier.php');
exit();
<?php
session_start(); // On démarre la session pour pouvoir la manipuler

// On détruit toutes les variables de la session (panier, 'user_logged_in', etc.)
session_unset();

// On détruit la session elle-même
session_destroy();

// On redirige l'utilisateur vers la page de connexion
header('Location: ../../public/login.php');
exit(); // On s'assure que le script s'arrête ici
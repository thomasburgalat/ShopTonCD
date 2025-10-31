<?php
session_start();

// --- SÉCURITÉ ---
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// On récupère le nombre d'articles (qui devrait être 0 maintenant)
$nombre_articles_panier = !empty($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='../node_modules/bootstrap/dist/css/bootstrap.css'>
    <title>Paiement Réussi</title>
</head>
<body>
<header class="bg-dark text-white p-3 mb-4">
    <nav class="container d-flex justify-content-between">
        <h1><a href="index.php" class="text-white text-decoration-none">ShoptonCD</a></h1>
        <ul class="nav">
            <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a></li>
            <li class="nav-item"><a class="nav-link text-warning" href="ajouter_album.php">Ajouter un album</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="../src/includes/logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<main class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="alert alert-success">
                <h4 class="alert-heading">Paiement réaliser succès !</h4>
                <p>Merci pour votre commande. Votre panier a été vidé.</p>
                <hr>
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</main>

</body>
</html>

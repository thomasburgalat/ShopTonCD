<?php
session_start();

// --- SÉCURITÉ ---
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Si le panier est vide, on ne peut pas payer. On redirige.
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}
// --- FIN SÉCURITÉ ---

// Re-calculer le total pour l'afficher
$json = file_get_contents('../data/cd.json');
$donnees = json_decode($json, true);
$cds_par_id = array_column($donnees['cds'], null, 'id');
$total_general = 0;
foreach ($_SESSION['panier'] as $cd_id => $quantite) {
    if (isset($cds_par_id[$cd_id])) {
        $total_general += $cds_par_id[$cd_id]['price'] * $quantite;
    }
}

$nombre_articles_panier = !empty($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='../node_modules/bootstrap/dist/css/bootstrap.css'>
    <title>Paiement</title>
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
        <div class="col-md-8">
            <h2>Paiement</h2>
            <p>Le montant total de votre commande est de : <strong><?php echo number_format($total_general, 2); ?> €</strong></p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Entrez vos informations de paiement</h5>
                    <form action="../src/includes/process_payment.php" method="post">
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Numéro de carte (16 chiffres)</label>
                            <input type="text" class="form-control" id="card_number" name="card_number"
                                   required
                                   maxlength="16"
                                   pattern="[0-9]{16}"
                                   title="Veuillez entrer 16 chiffres.">
                        </div>
                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">Date d'expiration (MM/AAAA)</label>
                            <input type="month" class="form-control" id="expiry_date" name="expiry_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Payer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>

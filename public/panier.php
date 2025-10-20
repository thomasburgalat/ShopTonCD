<?php
session_start();

// --- SÉCURITÉ ---
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
// --- FIN SÉCURITÉ ---

$json = file_get_contents('../data/cd.json');
$donnees = json_decode($json, true);
$cds_par_id = array_column($donnees['cds'], null, 'id');

$nombre_articles_panier = !empty($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='../node_modules/bootstrap/dist/css/bootstrap.css'>
    <title>Votre Panier</title>
</head>
<body>
<header class="bg-dark text-white p-3 mb-4">
    <nav class="container d-flex justify-content-between">
        <h1><a href="index.php" class="text-white text-decoration-none">ShoptonCD</a></h1>
        <ul class="nav">
            <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a></li>
            <li class="nav-item"><a class="nav-link text-warning" href="../src/includes/logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<main class="container mt-4">
    <h2>Votre Panier</h2>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="alert alert-info">Votre panier est vide.</div>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>Article</th>
                <th>Prix unitaire</th>
                <th class="text-center">Quantité</th>
                <th class="text-end">Sous-total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_general = 0;
            foreach ($_SESSION['panier'] as $cd_id => $quantite):
                if (!isset($cds_par_id[$cd_id])) continue; // Sécurité au cas où un ID n'existe plus
                $cd = $cds_par_id[$cd_id];
                $sous_total = $cd['price'] * $quantite;
                $total_general += $sous_total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($cd['title']); ?> - <?php echo htmlspecialchars($cd['author']); ?></td>
                    <td><?php echo number_format($cd['price'], 2); ?> €</td>
                    <td class="text-center"><?php echo $quantite; ?></td>
                    <td class="text-end"><?php echo number_format($sous_total, 2); ?> €</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3" class="text-end h4">Total :</th>
                <th class="text-end h4"><?php echo number_format($total_general, 2); ?> €</th>
            </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</main>

</body>
</html>
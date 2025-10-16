<?php
session_start();

// Charger les données des CD pour récupérer les détails (titre, prix, etc.)
$json = file_get_contents('cd.json');
$donnees = json_decode($json, true);

// Créer un tableau associatif avec les IDs comme clés pour un accès facile
$cds_par_id = [];
foreach ($donnees['cds'] as $cd) {
    $cds_par_id[$cd['id']] = $cd;
}

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
<header>
    <nav>
        <h1><a href="index.php">ShoptonCD v0 !</a></h1>
        <ul>
            <li><a href="index.php">Genres</a></li>
            <li><a href="recettes.php">Artistes</a></li>
            <li><a href="panier.php">Panier</a></li> </ul>
    </nav>
</header>

<main class="container mt-4">
    <h2>Votre Panier</h2>

    <?php if (empty($_SESSION['panier'])): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_general = 0;
                foreach ($_SESSION['panier'] as $cd_id => $quantite):
                    // On récupère les infos complètes du CD grâce à son ID
                    $cd = $cds_par_id[$cd_id];
                    $sous_total = $cd['price'] * $quantite;
                    $total_general += $sous_total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cd['title']); ?> - <?php echo htmlspecialchars($cd['author']); ?></td>
                        <td><?php echo number_format($cd['price'], 2); ?> €</td>
                        <td><?php echo $quantite; ?></td>
                        <td><?php echo number_format($sous_total, 2); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total :</th>
                    <th><?php echo number_format($total_general, 2); ?> €</th>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</main>

</body>
</html>

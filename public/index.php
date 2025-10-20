<?php
session_start();

// --- SÉCURITÉ ---
// Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
// --- FIN SÉCURITÉ ---

// Initialisation du panier
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
$nombre_articles_panier = !empty($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

// Charger les données des CD
$donnees = null;
$json_path = '../data/cd.json';
if (file_exists($json_path)) {
    $json = file_get_contents($json_path);
    $donnees = json_decode($json, true);
}

// Fonction pour créer les vignettes
function faireVignette($image_path) {
    if (!file_exists($image_path)) {
        return 'vignettes/default.jpg'; // Image par défaut si l'originale n'existe pas
    }

    $vignette_path = 'vignettes/' . basename($image_path);

    // Pour éviter de recréer les vignettes à chaque chargement de page
    if (file_exists($vignette_path)) {
        return $vignette_path;
    }

    $extension = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
    $img = null;

    switch ($extension) {
        case 'webp': $img = @imagecreatefromwebp($image_path); break;
        case 'png': $img = @imagecreatefrompng($image_path); break;
        case 'jpeg':
        case 'jpg': $img = @imagecreatefromjpeg($image_path); break;
    }

    if (!$img) {
        return 'vignettes/default.jpg'; // Retourne une image par défaut si la création échoue
    }

    $largeur = imagesx($img);
    $hauteur = imagesy($img);
    $vignette = imagecreatetruecolor(200, 200);
    imagecopyresampled($vignette, $img, 0, 0, 0, 0, 200, 200, $largeur, $hauteur);

    imagejpeg($vignette, $vignette_path, 90);

    imagedestroy($img);
    imagedestroy($vignette);

    return $vignette_path;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='../node_modules/bootstrap/dist/css/bootstrap.css'>
    <title>Shop ton CD</title>
</head>
<body>
<header class="bg-dark text-white p-3 mb-4">
    <nav class="container d-flex justify-content-between align-items-center">
        <h1><a href="index.php" class="text-white text-decoration-none">ShoptonCD</a></h1>
        <ul class="nav">
            <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a></li>
            <li class="nav-item"><a class="nav-link text-warning" href="../src/includes/logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<main class="container">
    <?php if (!$donnees || !isset($donnees['cds'])): ?>
        <div class="alert alert-danger">
            <strong>Erreur :</strong> Impossible de charger les données des CD. Vérifiez le chemin et le contenu du fichier `<?php echo $json_path; ?>`.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($donnees['cds'] as $cd): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <?php $path_image = faireVignette('images/' . $cd['image']); ?>
                        <img src="<?php echo $path_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($cd['title']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($cd['title']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($cd['author']); ?></p>
                            <p class="card-text mt-auto"><strong><?php echo number_format($cd['price'], 2); ?> €</strong></p>
                            <form action="../src/includes/ajouter_panier.php" method="post" class="mt-2">
                                <input type="hidden" name="cd_id" value="<?php echo $cd['id']; ?>">
                                <button type="submit" class="btn btn-primary w-100">Ajouter au panier</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
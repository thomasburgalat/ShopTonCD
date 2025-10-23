<?php
session_start();

// --- SÉCURITÉ ---
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
// --- FIN SÉCURITÉ ---

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
$nombre_articles_panier = !empty($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

$json_path = '../data/cd.json';
$donnees = json_decode(file_get_contents($json_path), true);

/**
 * Crée une vignette pour une image.
 * Vérifie l'existence des dossiers et des fonctions GD.
 */
function faireVignette($image_nom) {
    $dossier_images = 'images/';
    $dossier_vignettes = 'vignettes/';
    $image_originale_path = $dossier_images . $image_nom;
    $vignette_path = $dossier_vignettes . $image_nom;

    // Si l'image originale n'existe pas, on arrête.
    if (!file_exists($image_originale_path)) {
        return $dossier_images . 'default.jpg'; // Prévoir une image default.jpg dans /images
    }

    // Si la vignette existe déjà, on la retourne.
    if (file_exists($vignette_path)) {
        return $vignette_path;
    }

    // On vérifie si le dossier vignettes existe ET si on peut écrire dedans.
    if (!is_dir($dossier_vignettes)) {
        // On tente de le créer. Si ça échoue, c'est un problème de permissions.
        if (!mkdir($dossier_vignettes, 0755, true)) {
            error_log("Impossible de créer le dossier des vignettes. Vérifiez les permissions sur le dossier 'public'.");
            return $image_originale_path; // On retourne l'image originale si on ne peut pas créer la vignette
        }
    }

    $extension = strtolower(pathinfo($image_originale_path, PATHINFO_EXTENSION));
    $img = null;

    // On charge l'image en mémoire
    switch ($extension) {
        case 'jpeg':
        case 'jpg':
            if (function_exists('imagecreatefromjpeg')) {
                $img = @imagecreatefromjpeg($image_originale_path);
            }
            break;
        case 'png':
            if (function_exists('imagecreatefrompng')) {
                $img = @imagecreatefrompng($image_originale_path);
            }
            break;
        case 'webp':
            if (function_exists('imagecreatefromwebp')) {
                $img = @imagecreatefromwebp($image_originale_path);
            }
            break;
    }

    // Si l'image n'a pas pu être chargée (extension GD manquante pour ce format)
    if (!$img) {
        error_log("L'extension GD pour le format '$extension' n'est pas activée.");
        return $image_originale_path; // On retourne l'image originale
    }

    // On crée la vignette
    $largeur = imagesx($img);
    $hauteur = imagesy($img);
    $vignette = imagecreatetruecolor(250, 250);
    imagecopyresampled($vignette, $img, 0, 0, 0, 0, 250, 250, $largeur, $hauteur);

    // On sauvegarde la vignette en JPEG (format le plus compatible)
    imagejpeg($vignette, $vignette_path, 90);

    // On libère la mémoire
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
    <div class="row">
        <?php foreach ($donnees['cds'] as $cd): ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <?php $path_image = faireVignette($cd['image']); ?>
                    <img src="<?php echo $path_image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($cd['title']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($cd['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($cd['author']); ?></p>
                        <p class="card-text mt-auto"><strong><?php echo number_format($cd['price'], 2); ?> €</strong></p>
                        <form action="../src/includes/ajouter_panier.php" method="post" class="mt-2">
                            <input type="hidden" name="cd_id" value="<?php echo $cd['id']; ?>">
                            <button type="submit" class="btn btn-primary w-100">Ajouter au panier</button>
                        </form>

                        <button type="button" class="btn btn-outline-secondary w-100 mt-2"
                                data-bs-toggle="modal"
                                data-bs-target="#detailsModal"
                                data-cd-title="<?php echo htmlspecialchars($cd['title']); ?>"
                                data-cd-author="<?php echo htmlspecialchars($cd['author']); ?>"
                                data-cd-genre="<?php echo htmlspecialchars($cd['genre']); ?>"
                                data-cd-price="<?php echo number_format($cd['price'], 2); ?> €"
                                data-cd-image="<?php echo $path_image; ?>">
                            Plus d'infos
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Détails du CD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" id="modal-cd-image" class="img-fluid mb-3 w-100" alt="Image du CD">
                <p><strong>Titre :</strong> <span id="modal-cd-title"></span></p>
                <p><strong>Auteur :</strong> <span id="modal-cd-author"></span></p>
                <p><strong>Genre :</strong> <span id="modal-cd-genre"></span></p>
                <p><strong>Prix :</strong> <span id="modal-cd-price"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
<script>
    // Ce script s'exécute quand la modale est sur le point de s'ouvrir
    var detailsModal = document.getElementById('detailsModal');
    detailsModal.addEventListener('show.bs.modal', function (event) {
        // Bouton qui a déclenché la modale
        var button = event.relatedTarget;

        // Extraire les infos des attributs data-* du bouton
        var title = button.getAttribute('data-cd-title');
        var author = button.getAttribute('data-cd-author');
        var genre = button.getAttribute('data-cd-genre');
        var price = button.getAttribute('data-cd-price');
        var image = button.getAttribute('data-cd-image');

        // Mettre à jour le contenu de la modale avec ces infos
        var modalTitle = detailsModal.querySelector('.modal-title');
        var modalImage = detailsModal.querySelector('#modal-cd-image');
        var modalCdTitle = detailsModal.querySelector('#modal-cd-title');
        var modalAuthor = detailsModal.querySelector('#modal-cd-author');
        var modalGenre = detailsModal.querySelector('#modal-cd-genre');
        var modalPrice = detailsModal.querySelector('#modal-cd-price');

        modalTitle.textContent = title;
        modalImage.src = image;
        modalCdTitle.textContent = title;
        modalAuthor.textContent = author;
        modalGenre.textContent = genre;
        modalPrice.textContent = price;
    });
</script>
</body>
</html>
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

// On lit le JSON (qui ne contient que les noms de fichiers)
$json_path = '../data/cd.json';
$donnees = json_decode(file_get_contents($json_path), true);

/**
 * Crée une vignette à partir d'un nom de fichier image.
 */
function faireVignette($image_nom_fichier) { // $image_nom_fichier est "booba.jpg"
    $dossier_images = 'images/';
    $dossier_vignettes = 'vignettes/';

    // Chemin de l'image originale (ex: "images/booba.jpg")
    $image_originale_path = $dossier_images . $image_nom_fichier;

    // Chemin de la vignette (ex: "vignettes/booba.jpg")
    $vignette_path = $dossier_vignettes . $image_nom_fichier;

    // Si l'originale n'existe pas, on sort
    if (!file_exists($image_originale_path)) {
        return 'images/default.jpg'; // Avoir une image par défaut
    }

    // Si la vignette existe déjà, on la retourne direct
    if (file_exists($vignette_path)) {
        return $vignette_path;
    }

    // Si le dossier vignettes/ n'existe pas, on le crée
    if (!is_dir($dossier_vignettes)) {
        if (!mkdir($dossier_vignettes, 0755, true)) {
            return $image_originale_path; // Si on n'arrive pas à créer, on renvoie l'originale
        }
    }

    $extension = strtolower(pathinfo($image_originale_path, PATHINFO_EXTENSION));
    $img = null;

    // On charge l'image en mémoire (c'est ça qui peut planter si GD n'est pas là)
    switch ($extension) {
        case 'jpeg':
        case 'jpg': $img = @imagecreatefromjpeg($image_originale_path); break;
        case 'png': $img = @imagecreatefrompng($image_originale_path); break;
        case 'webp': $img = @imagecreatefromwebp($image_originale_path); break;
    }

    // Si ça a planté, on renvoie l'image originale
    if (!$img) {
        return $image_originale_path;
    }

    // On crée la vignette
    $vignette = imagecreatetruecolor(250, 250);
    imagecopyresampled($vignette, $img, 0, 0, 0, 0, 250, 250, imagesx($img), imagesy($img));

    // On la sauvegarde
    imagejpeg($vignette, $vignette_path, 90);

    // On libère la mémoire
    imagedestroy($img);
    imagedestroy($vignette);

    // On renvoie le chemin de la vignette qui vient d'être créée
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
            <?php
            // 1. On prépare le chemin de la VRAIE image (ex: "images/booba.jpg")
            $path_originale = 'images/' . $cd['image'];

            // 2. On TENTE de créer la vignette.
            // $path_vignette sera "vignettes/booba.jpg" (si ça marche)
            // ou "images/booba.jpg" (si ça rate)
            $path_vignette = faireVignette($cd['image']);
            ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100">

                    <img src="<?php echo $path_vignette; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($cd['title']); ?>">

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
                                data-cd-image="<?php echo $path_originale; ?>"> Plus d'infos
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
    var detailsModal = document.getElementById('detailsModal');
    detailsModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        // Le script récupère le chemin de la VRAIE image
        var image = button.getAttribute('data-cd-image');
        var title = button.getAttribute('data-cd-title');
        // ...et le reste
        var author = button.getAttribute('data-cd-author');
        var genre = button.getAttribute('data-cd-genre');
        var price = button.getAttribute('data-cd-price');

        // Il met tout à jour dans la modale
        var modalImage = detailsModal.querySelector('#modal-cd-image');
        modalImage.src = image; // <-- C'est ici qu'il met la VRAIE image

        detailsModal.querySelector('.modal-title').textContent = title;
        detailsModal.querySelector('#modal-cd-title').textContent = title;
        detailsModal.querySelector('#modal-cd-author').textContent = author;
        detailsModal.querySelector('#modal-cd-genre').textContent = genre;
        detailsModal.querySelector('#modal-cd-price').textContent = price;
    });
</script>

</body>
</html>
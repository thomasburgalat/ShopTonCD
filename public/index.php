<?php
session_start();


// On initialise le panier seulement s'il est vide (première visite)
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Calcule le nombre d'articles pour l'affichage dans le header
$nombre_articles_panier = 0;
if (!empty($_SESSION['panier'])) {
    // array_sum est parfait pour additionner toutes les quantités
    $nombre_articles_panier = array_sum($_SESSION['panier']);
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='../node_modules/bootstrap/dist/css/bootstrap.css'>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.js"></script>
    <title>Shop ton CD</title>
</head>
<body>
<header>
    <nav>
        <h1><a href="index.php">ShoptonCD v0 !</a></h1>
        <ul>
            <li>
                <a class="menu-active" href="index.php">Genres</a>
            </li>
            <li>
                <a href="recettes.php">Artistes</a>
            </li>
            <li>
                <a href="panier.php">Panier (<?php echo $nombre_articles_panier; ?>)</a>
            </li>
        </ul>
    </nav>
</header>
</body>
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Détails du CD</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" id="modal-cd-image" class="img-fluid mb-3" alt="Image du CD">
                <p><strong>Auteur :</strong> <span id="modal-cd-author"></span></p>
                <p><strong>Prix :</strong> <span id="modal-cd-price"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
</html>
<?php

$json = file_get_contents('cd.json');
$donnees = json_decode($json, true);

foreach ($donnees['cds'] as $cd) {
    $image = $cd['image'];
    $path_image = faireVignette($image);

    echo "ID : " . $cd['id'] . "</br>";
    echo "Title : " . $cd['title'] . "</br>";
    echo "Author : " . $cd['author'] . "</br>";
    echo "Genre : " . $cd['genre'] . "</br>";
    echo "Price : " . $cd['price'] . "</br>";
    echo "<img src='" . $path_image . "'alt=" . $cd['title'] . "'><br>";
    echo "<form action='../src/includes/ajouter_panier.php' method='post'>";
    echo "<input type='hidden' name='cd_id' value='" . $cd['id'] . "'>";
    echo "<button type='submit' class='btn btn-primary'>Ajouter au panier</button>";
    echo "</form>";
    echo "<button class='btn btn-outline-primary' 
        data-bs-toggle='modal' 
        data-bs-target='#detailsModal' 
        data-cd-title='" . htmlspecialchars($cd['title']) . "'
        data-cd-author='" . htmlspecialchars($cd['author']) . "'
        data-cd-image='" . $path_image . "'
        data-cd-price='" . $cd['price'] . " €'>
        View Details
      </button>";
    echo "------------------------------------------------" . "</br>";
}

faireVignette('images/image.webp');

function faireVignette($image){
    $extension = pathinfo(basename($image), PATHINFO_EXTENSION);

    switch ($extension){
        case 'webp':
            $img = imagecreatefromwebp($image);
            break;
        case 'png':
            $img = imagecreatefrompng($image);
            break;
        case 'jpg' || 'jpeg':
            $img = imagecreatefromjpeg($image);
            break;
        default:
            die("Format non accepte : $extension");
    }


    $src = imagecreatefromstring(file_get_contents($image));
    $vignette = imagecreatetruecolor(100,100);

    $largeur = imagesx($img);
    $hauteur = imagesy($img);

    imagecopyresampled($vignette, $src, 0, 0, 0, 0, 100, 100, $largeur, $hauteur);
    $filename = 'vignettes/' . basename($image);
    imagejpeg($vignette, $filename, 90);

    imagedestroy($src);
    imagedestroy($vignette);

    return $filename;

}

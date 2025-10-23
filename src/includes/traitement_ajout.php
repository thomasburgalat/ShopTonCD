<?php

session_start();

// --- SÉCURITÉ ---
// On vérifie que la personne est connectée ET que le formulaire a été envoyé (méthode POST)
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/login.php');
    exit;
}

// --- 1. VÉRIFIER LES CHAMPS DU FORMULAIRE ---
if (empty($_POST['title']) || empty($_POST['author']) || empty($_POST['genre']) || empty($_POST['price']) || empty($_FILES['image']['name'])) {
    header('Location: ../../public/ajouter_album.php?error=Tous les champs sont obligatoires.');
    exit;
}

$title = $_POST['title'];
$author = $_POST['author'];
$genre = $_POST['genre'];
$price = (float)$_POST['price']; // Convertir le prix en nombre

$image_file = $_FILES['image'];

// --- 2. GÉRER L'UPLOAD DE L'IMAGE ---

// Emplacement où l'image sera sauvegardée
$dossier_upload = '../../public/images/';
$nom_fichier_image = basename($image_file['name']);
$chemin_cible = $dossier_upload . $nom_fichier_image;

// Vérifier si le fichier est une vraie image
$check = @getimagesize($image_file['tmp_name']);
if ($check === false) {
    header('Location: ../../public/ajouter_album.php?error=Le fichier n\'est pas une image valide.');
    exit;
}

// Vérifier si le fichier existe déjà
if (file_exists($chemin_cible)) {
    header('Location: ../../public/ajouter_album.php?error=Une image avec ce nom existe déjà.');
    exit;
}

// Déplacer le fichier du dossier temporaire vers le dossier public/images/
if (!move_uploaded_file($image_file['tmp_name'], $chemin_cible)) {
    header('Location: ../../public/ajouter_album.php?error=Erreur lors de l\'upload de l\'image.');
    exit;
}

// --- 3. METTRE À JOUR LE FICHIER JSON ---

$json_path = '../../data/cd.json';

// Lire le JSON actuel
$json_actuel = file_get_contents($json_path);
$donnees = json_decode($json_actuel, true);

// Trouver le nouvel ID (on prend l'ID le plus haut et on fait +1)
$max_id = 0;
foreach ($donnees['cds'] as $cd) {
    if ($cd['id'] > $max_id) {
        $max_id = $cd['id'];
    }
}
$nouvel_id = $max_id + 1;

// Créer le nouvel objet CD
$nouveau_cd = [
    'id' => $nouvel_id,
    'title' => $title,
    'author' => $author,
    'genre' => $genre,
    'price' => $price,
    'image' => $nom_fichier_image // On sauvegarde juste le nom du fichier
];

// Ajouter le nouveau CD au tableau
$donnees['cds'][] = $nouveau_cd;

// Ré-encoder le JSON en gardant un format lisible (JSON_PRETTY_PRINT)
$nouveau_json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Écrire le nouveau contenu dans le fichier cd.json
file_put_contents($json_path, $nouveau_json);

// --- 4. REDIRIGER AVEC UN SUCCÈS ---
header('Location: ../../public/ajouter_album.php?success=1');
exit;

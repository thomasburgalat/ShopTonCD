<?php
session_start();
// Si l'utilisateur est déjà connecté, on le redirige vers l'accueil
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ShopTonCD</title>
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height: 100vh;">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Connexion</h2>
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">
                                Identifiant ou mot de passe incorrect.
                            </div>
                        <?php endif; ?>
                        <form action="../src/includes/verifier_connexion.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Identifiant</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
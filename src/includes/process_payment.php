
<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/login.php');
    exit;
}

$card_number = $_POST['card_number'] ?? '';
$expiry_date_str = $_POST['expiry_date'] ?? '';

if (!preg_match('/^[0-9]{16}$/', $card_number)) {
    header('Location: ../../public/checkout.php?error=Le numéro de carte doit contenir exactement 16 chiffres.');
    exit;
}

if ($card_number[0] !== $card_number[15]) {
    header('Location: ../../public/checkout.php?error=Échec de la simulation : Le premier et le dernier chiffre ne sont pas identiques.');
    exit;
}

try {
    $cutoff_date = new DateTime();
    $cutoff_date->modify('+3 months');

    $user_expiry_date = new DateTime($expiry_date_str . '-01');

    $user_expiry_date->modify('last day of this month');


    $user_expiry_date->setTime(23, 59, 59);

    if ($user_expiry_date <= $cutoff_date) {
        header('Location: ../../public/checkout.php?error=La date d\'expiration doit être supérieure de 3 mois à la date actuelle.');
        exit;
    }

} catch (Exception $e) {
    // Si la date est invalide (ex: "abc")
    header('Location: ../../public/checkout.php?error=Format de date invalide.');
    exit;
}




$_SESSION['panier'] = [];

header('Location: ../../public/order_success.php');
exit;
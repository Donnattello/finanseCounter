
<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
}
?>

<?php

$comment = $_POST['comment'] ?? '';
$amount = floatval($_POST['amount']);
$data = $_POST['data'] ?? '';
$user = $_POST['user_id'] ?? '';

$plikDocelowy = 'tranzactions.json';
$tranzakcje = [];

if (file_exists($plikDocelowy)) {
    $tranzakcje = json_decode(file_get_contents($plikDocelowy), true);
}

    $nowy_wpis = [
        'id_tr' => count($tranzakcje) + 1,
        'data' => $data,
        'user_id' => $user,
        'comment' => $comment,
        'counted' => false,
        'amount' => $amount,
        'sptint' => 0
    ];

    $tranzakcje[] = $nowy_wpis;


// Zapisz do pliku
file_put_contents($plikDocelowy, json_encode($tranzakcje, JSON_PRETTY_PRINT));

header('Location: finanseCounter.php');

?>
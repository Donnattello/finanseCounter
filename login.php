<?php
//logowanie
session_start();

// Dane z formularza
$imie = $_POST['imie'] ?? '';
$haslo = $_POST['password'] ?? '';

// Wczytaj użytkowników
$users = json_decode(file_get_contents('users.json'), true);

// Szukamy użytkownika
$zalogowany = null;

foreach ($users as $user) {
    if ($user['imie'] === $imie && $user['password'] === $haslo) {
        $zalogowany = $user;
        break;
    }
}

if ($zalogowany) {
    // Zaloguj użytkownika
    $_SESSION['user'] = $zalogowany;
    header('Location: finanseCounter.php');
    exit; // Ważne, by zatrzymać dalsze wykonywanie skryptu
} else {
    echo "Błędne dane logowania!";
}
?>
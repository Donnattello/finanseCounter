<?php
//logowanie
session_start();

// Dane z formularza
$u_name = $_POST['imie'] ?? '';
$haslo = $_POST['password'] ?? '';

require_once 'db.php';
// Wczytaj użytkowników
try {
    // Zapytanie SQL do bazy danych
    $stmt = $pdo->prepare("SELECT * FROM users WHERE u_name = :u_name AND password = :password");
    $stmt->execute([
        ':u_name' => $u_name,
        ':password' => $haslo // UWAGA: lepiej hasło zahaszować (patrz niżej)
    ]);

    $zalogowany = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($zalogowany) {
        $_SESSION['user'] = $zalogowany;
        header('Location: group.php');
        exit;
    } else {
        echo "Błędne dane logowania!";
    }

} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage();
}
?>
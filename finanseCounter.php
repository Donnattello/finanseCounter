<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h3><?php  echo "Hello, " . $_SESSION['user']['imie'];  ?></h3>
    <div class="tablyczka">
        <table >
        <thead>
            <tr>
                <th>ID</th>
                <th>Imie</th>
                <th>Comment</th>
                <th>suma</th>
                <th>data</th>
                <th>edit</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            
            // Wczytaj dane z pliku JSON
            $transactions = json_decode(file_get_contents('tranzactions.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($transactions === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }
            
            $users = json_decode(file_get_contents('users.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($users === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }
            
            foreach ($transactions as $transaction): ?>
                <tr>
                    <td style="width: 8%;"><?php echo htmlspecialchars($transaction['id_tr']); ?></td>
                    <td style="width: 16%;"><?php 
                        $user_id = $transaction['user_id']; // ID zalogowanego użytkownika



                        // Przeszukaj tablicę $users, aby znaleźć odpowiednie imię użytkownika
                        $user = null;
                        foreach ($users as $u) {
                            if ($u['id_user'] == $user_id) {
                                $user = $u;
                                break;
                            }
                        }
                        $transactions1 = json_decode(file_get_contents('tranzactions.json'), true);
            
                        // Sprawdź, czy dane zostały poprawnie wczytane
                        if ($transactions1 === null) {
                            echo "Błąd wczytywania danych z pliku.";
                            exit;
                        }
                        // Sprawdź, czy znaleziono użytkownika i czy ID użytkownika w transakcji jest zgodne z zalogowanym
                        if ($user !== null) {
                            // Zakładając, że masz tablicę $tranzactions (transakcje) z transakcjami
                            foreach ($transactions1 as $transaction1) {
                                if ($transaction1['user_id'] == $user_id) {
                                    echo htmlspecialchars($user['imie']); // Wyświetl imię użytkownika
                                    break; // Zakończ po znalezieniu pierwszej pasującej transakcji
                                }
                            }
                        }
                    
                    ?></td>
                    <td style="width: 30%;"><?php echo htmlspecialchars($transaction['comment']); ?></td>
                    <td style="width: 12%;"><?php echo htmlspecialchars($transaction['amount']); ?> zł</td>
                    <td style="width: 14%;"><?php echo htmlspecialchars($transaction['data']); ?></td>
                    <td style="width: 20%;"><button><?php  ?>edit</button></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
    <div class="addform">
        <h3><?php echo "dodawanie nowego wpisu dla urzytkownika " . $_SESSION['user']['imie'];?></h3>
        <form action="addTranzaction.php" method="post">
            <label>data:<br>
            <input type="date" name="data" required>
            </label><br><br>

            <label>Kwota:<br>
            <input type="number" name="amount" step="0.01" required>
            </label><br><br>
            
            <label>user:<br>
            <input type="number" name="user_id" step="1" required>
            </label><br><br>

            <label>Komentarz:<br>
            <textarea name="comment"></textarea>
            </label><br><br>

            <input class="button" type="submit" value="Zapisz">
        </form>
    </div>

    <form action="countSA.php" method="post">
        <button class="button" type="submit">count s.a.</button>
    </form>
    <h3>dana <?php 
    $history = json_decode(file_get_contents("historySprints.json"), true);
    $lastEntry = end($history);
    echo htmlspecialchars($lastEntry['user1']); ?></h3>

    <h3>vika <?php 
    $history = json_decode(file_get_contents("historySprints.json"), true);
    $lastEntry = end($history);
    echo htmlspecialchars($lastEntry['user2']); ?></h3>
</body>
</html>






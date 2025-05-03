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
    <title>g</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="t_style.css">
</head>

<body>
    <?php
   

    require_once __DIR__ . '/../db.php';

    if (isset($_GET['id'])) {
        $groupId = intval($_GET['id']);
    }

    try {
        // Zapytanie SQL do bazy danych
        $stmt = $pdo->prepare("SELECT gu.user_id, u.u_name, COALESCE(SUM(t.amount), 0) AS total_amount
                                FROM group_users gu
                                JOIN users u ON u.id_user = gu.user_id
                                LEFT JOIN transactions t ON gu.user_id = t.user_id AND t.group_id = gu.group_id AND t.counted = 0
                                WHERE gu.group_id = :group_id
                                GROUP BY gu.user_id
                                ORDER BY gu.user_id");
        $stmt->execute(['group_id' => $groupId]);
        $rows = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT COUNT(*) AS user_count FROM group_users WHERE group_id = :group_id");
        $stmt->execute(['group_id' => $groupId]);
        $result = $stmt->fetch();

        $userCount = $result['user_count'];
        
    }catch (PDOException $e) {
        echo "Database connection error: " . $e->getMessage();
    }
    
    // nadawanie useram lokalnych id i liczenie sumy wrzystkich transakcij
    $indexedUsers = [];
    $userNames = [];
    
    $counter = 0;
    $totalSum = 0;
    foreach ($rows as $row) {
        $indexedUsers[] = [
            'new_id' => $counter, // lokalne ID od 0 w górę
            'user_id' => $row['user_id'], // oryginalne ID z bazy
            'name' => $row['u_name'],
            'total_amount' => $row['total_amount'], // suma kwot
            'owe' => 0,
        ];
        $totalSum += $row['total_amount'];
        $counter++;
    }

    



    foreach ($indexedUsers as $user) {
        $userNames[$user['new_id']] = $user['name'];
    }
    $sa = $totalSum / $userCount;   //srednia wszystkich

    foreach ($indexedUsers as $index => $r) {
        $indexedUsers[$index]['owe'] = round($sa - $r['total_amount'], 2); // zaokrąglone do 2 miejsc
    }
    



    $debtors = [];  // osoby które muszą zapłacić (owe > 0)
    $creditors = []; // osoby które mają dostać (owe < 0)

    // Podział na dwie grupy
    foreach ($indexedUsers as $user) {
        if ($user['owe'] > 0) {
            $debtors[] = $user;
        } elseif ($user['owe'] < 0) {
            $creditors[] = $user;
        }
    }

    // Inicjalizacja macierzy dla  
    $debtMatrix = [];
    
    for ($i = 0; $i < $userCount; $i++) {
        for ($j = 0; $j < $userCount; $j++) {
            $debtMatrix[$i][$j] = 0;
        }
    }

    // Wypełnianie macierzy – algorytm rozliczania
    foreach ($debtors as &$debtor) {
        foreach ($creditors as &$creditor) {
            if ($debtor['owe'] <= 0) break;
            if (abs($creditor['owe']) <= 0) continue;

            // Ile może oddać?
            $amount = min($debtor['owe'], abs($creditor['owe']));
            
            // Zapisz do macierzy: debtor płaci creditorowi
            $debtMatrix[$debtor['new_id']][$creditor['new_id']] = round($amount, 2);

            // Zmniejsz wartości
            $debtor['owe'] -= $amount;
            $creditor['owe'] += $amount; // bo creditor['owe'] jest ujemny
        }
    }


    // wpisywanie do bazy danych obliczonych danych
    $updateStmt = $pdo->prepare("UPDATE transactions 
                                SET counted = 1 
                                WHERE group_id = :group_id AND counted = 0");
    $updateStmt->execute(['group_id' => $groupId]);

    $stmt_add = $pdo->prepare("INSERT INTO counts (mean, total, done_c)
                                VALUES ( :mean, :total, 0)
    ");
    $stmt_add->execute([
        'mean' => $sa,
        'total' => $totalSum
    ]);
    $countId = $pdo->lastInsertId(); 
    for ($i = 0; $i < $userCount; $i++) {
        for ($j = 0; $j < $userCount; $j++) {
            $amount = $debtMatrix[$i][$j];
            if ($amount > 0) {
                $userId = $indexedUsers[$i]['user_id'];   // kto płaci
                $oweTo = $indexedUsers[$j]['user_id'];    // komu płaci

                $stmt_add = $pdo->prepare("
                    INSERT INTO group_user_count (user_id, group_id, owe_to, amount, count_id)
                    VALUES (:user_id, :group_id, :owe_to, :amount, :count_id)
                ");
                $stmt_add->execute([
                    'user_id' => $userId,
                    'group_id' => $groupId,
                    'owe_to' => $oweTo,
                    'amount' => $amount,
                    'count_id' => $countId
                ]);
            }
        }
    }
    



?>w00
<h3>total sum<?php echo htmlspecialchars($totalSum); ?></h3>
<h2>Who owes whom:</h2>
<ul>
<?php
for ($i = 0; $i < $userCount; $i++) {
    for ($j = 0; $j < $userCount; $j++) {
        $amount = $debtMatrix[$i][$j];
        if ($amount > 0) {
            echo "<li><strong>" . htmlspecialchars($userNames[$i]) . "</strong> owes <strong>" . 
                 htmlspecialchars($userNames[$j]) . "</strong>: <strong>" . number_format($amount, 2) . " zł</strong></li>";
        }
    }
}
?>
</ul>


</body>
</html>

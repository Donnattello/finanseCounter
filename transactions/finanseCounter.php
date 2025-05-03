
<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
}
?>
<?php 
    require_once __DIR__ . '/../db.php';
    // pobieranie id grupy
    if (isset($_GET['id'])) {
        $groupId = (int)$_GET['id']; 
                
    } else {
        echo "Nie podano ID grupy.";
        exit;
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
    
    

    <form action="countSA.php?id=<?= $groupId ?>" method="post">
        <button class="button" type="submit">count s.a.</button>
    </form>
           
            <?php
            try {
                
                $stmt = $pdo->prepare("SELECT u.u_name,  t.user_id, t.comment, t.amount,
                                        FROM transactions t
                                        JOIN users u ON t.user_id = u.id_user
                                        JOIN groups g ON t.group_id = g.id_g
                                        JOIN group_users gu ON gu.user_id = u.id_user AND gu.group_id = g.id_g
                                        WHERE t.group_id = :groupId and t.counted = 0 
                                        ORDER BY t.data DESC
                                        ");
                $stmt->execute([
                    ':groupId' => $groupId,
                ]);
                $Transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt_g = $pdo->prepare("SELECT g_name FROM groups WHERE id_g = :groupId");
                $stmt_g->execute([':groupId' => $groupId,]);
                $group_name = $stmt_g->fetchAll();
               
                
         
            
            } catch (PDOException $e) {
                echo "Database connection error: " . $e->getMessage();
            }?>

            <h3><?php  htmlspecialchars($g_name);  ?></h3>
            
            <?php
            if ($Transactions == null)
            {
                echo "no transactions yet!";
            }
            else
            {
                foreach ($Transactions as $transaction): ?>
                <div class="transaction_box">
                    <div class="t1">
                        <h3><?php echo htmlspecialchars($transaction['u_name']);  ?></h3>
                        <h4><?php echo htmlspecialchars($transaction['comment']);  ?></h4>
                    </div>
                    <div class=t2>
                        <h3><?php echo htmlspecialchars($transaction['amount']);  ?></h3>
                       
                    </div>  
                </div>
            <?php endforeach; 
            }?>
            
        
    
    <div class="addform">
        <h3><?php echo "dodawanie nowego wpisu dla urzytkownika " . $_SESSION['user']['u_name'];?></h3>
        <form action="addTranzaction.php?id=<?= $groupId ?>" method="post">
            <label>date:<br>
            <input type="date" name="data" required>
            </label><br><br>

            <label>Amount:<br>
            <input type="number" name="amount" step="0.01" required>
            </label><br><br>

            <label>Comment:<br>
            <textarea name="comment"></textarea>
            </label><br><br>

            <input class="button" type="submit" value="Submit">
        </form>
    </div>
    
</body>
</html>







<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
}
?>
<?php 
    require_once 'db.php';
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
    <title></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="t_style.css">
</head>

<body>
        <div class="top_line">
            <a href="/group.php"><img class="arrow_back" src="/imgs/arrow_back.png" alt="arrow back"></a>

   
           
            <?php
            try {
                
                $stmt = $pdo->prepare("SELECT u.u_name,  t.user_id, t.comment, t.amount
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
               
                $colorSets = [
                    ['bg' => '#FD987B', 'text' => '#5B1100'],
                    ['bg' => '#D0A1D7', 'text' => '#5D016B'],
                    ['bg' => '#FCEC94', 'text' => '#746929'],
                    ['bg' => '#A9D1F7', 'text' => '#002244'],
                    ['bg' => '#A0EAC4', 'text' => '#004434'],
                    ['bg' => '#FC9DD6', 'text' => '#74094A'],
                ];
                $userColorMap = [];
                $colorIndex = 0;

                foreach ($Transactions as $transaction) {
                    $userId = $transaction['user_id'];
                    if (!isset($userColorMap[$userId])) {
                        $userColorMap[$userId] = $colorSets[$colorIndex % count($colorSets)];
                        $colorIndex++;
                    }
                }
            
            } catch (PDOException $e) {
                echo "Database connection error: " . $e->getMessage();
            }?>

            <h3><?php echo htmlspecialchars($group_name[0]['g_name']);  ?></h3>
            <a href=""><img class="arrow_back" src="/imgs/settings.png" alt="settings"></a>
        </div>
        
        
            <form action="countSA.php?id=<?= $groupId ?>" method="post">
                <input class="buttons" type="submit" value="rozlicz">
            </form> 
        <div class="transactions">    
            <?php
            if ($Transactions == null)
            {
                echo "niema jeszcze żadnych tranzakcji!";
            }
            else
            {
                foreach ($Transactions as $transaction): 
                    $userId = $transaction['user_id'];
                    $colors = $userColorMap[$userId];
                
                    $bgColor = $colors['bg'];
                    $textColor = $colors['text'];?>

                    <div class="transaction_box" style="background-color: <?= htmlspecialchars($bgColor) ?>;">
                        <div class="t1">
                            <h3 style="color: <?= htmlspecialchars($textColor) ?>;"><?php echo htmlspecialchars($transaction['u_name']);  ?></h3>
                            <h4 style="color: <?= htmlspecialchars($textColor) ?>;"><?php echo htmlspecialchars($transaction['comment']);  ?></h4>
                        </div>
                        <div class=t2>
                            <h3 style="color: <?= htmlspecialchars($textColor) ?>;"><?php echo htmlspecialchars($transaction['amount']);  ?></h3>
                        
                        </div>  
                    </div>
                <?php endforeach; 
            }?>
        </div>    
        
    
    <div class="addform">
        <h3><?php echo "dodawanie tranzakcji dla " . $_SESSION['user']['u_name'];?></h3>
        <form action="addTranzaction.php?id=<?= $groupId ?>" method="post">
            <label>date:<br>
            <input type="date" name="data" required>
            </label>

            <label>Amount:<br>
            <input type="number" name="amount" step="0.01" required>
            </label>

            <label>Comment:<br>
            <textarea name="comment"></textarea>
            </label>

            <input class="buttons" type="submit" value="Submit">
        </form>
    </div>
    
</body>
</html>






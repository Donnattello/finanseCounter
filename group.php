
<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>your groups</title>
    <link rel="stylesheet" href="style_group.css">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <h3><?php  echo "Hello, " . $_SESSION['user']['u_name'];  ?></h3>


    <?php 
            require_once 'db.php';
            // Wczytaj użytkowników
            try {
                // Zapytanie SQL do bazy danych
                $stmt = $pdo->prepare("SELECT u.u_name, g.id_g,  g.g_name, g.owner_id, g.img_g, COUNT(gu2.user_id) AS member_count
                                        FROM group_users AS ug 
                                        JOIN groups AS g ON g.id_g = ug.group_id
                                        JOIN users AS u ON u.id_user = g.owner_id
                                        left JOIN group_users AS gu2 ON gu2.group_id = g.id_g
                                        WHERE  ug.user_id = :id_u
                                        GROUP BY g.id_g
                                        ");
                $stmt->execute([
                    ':id_u' => $_SESSION['user']['id_user'],
                ]);
                
                $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            
            } catch (PDOException $e) {
                echo "Database connection error: " . $e->getMessage();
            }
            // Wczytaj dane z pliku JSON
            $ava = json_decode(file_get_contents('imgs.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($ava === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }
            
            foreach ($groups as $g): ?>
                <a href="finanseCounter.php?id=<?= $g['id_g'] ?>" style="text-decoration: none;color:black">
                    <div class="group_box">
                        <div>
                            <?php
                            $imgPath = '';

                            foreach ($ava as $a) {
                                if ($a['id_img'] == $g['img_g']) {
                                    $imgPath = htmlspecialchars($a['path']);
                                    break;
                                }
                            }
                            
                            ?>
                            <img class="ava" src="<?= $imgPath ?>" alt="img of group">
                        </div>
                        <div>
                            <h3><?php echo htmlspecialchars($g['g_name']); ?></h3>
                            <h4><?php echo htmlspecialchars($g['member_count']); ?> members | admin: <?php echo htmlspecialchars($g['u_name']); ?></h4>
                        </div>
                    </div>
                </a>

                

            <?php endforeach; ?>
</body>
</html>
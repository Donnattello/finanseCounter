
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
</head>
<body>
    <h3><?php  echo "Hello, " . $_SESSION['user']['imie'];  ?></h3>


    <?php 
            
            // Wczytaj dane z pliku JSON
            $groups = json_decode(file_get_contents('groups.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($groups === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }

            $Users = json_decode(file_get_contents('users.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($Users === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }

            $Users_g = json_decode(file_get_contents('group_users.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($Users_g === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }

            $user_group_ids = [];
            foreach ($Users_g as $user_g) {
                if ($user_g['user_id'] === $_SESSION['user']['id_user']) {
                    $user_group_ids[] = $user_g['group_id'];
                }
            }

           
            $user_groups = [];
            foreach ($groups as $group) {
                if (in_array($group['id_g'], $user_group_ids)) {
                    $user_groups[] = $group; 
                }
            }

            $users = json_decode(file_get_contents('users.json'), true);
            
            // Sprawdź, czy dane zostały poprawnie wczytane
            if ($users === null) {
                echo "Błąd wczytywania danych z pliku.";
                exit;
            }
            
            foreach ($user_groups as $g): ?>
                <a href="finanseCounter.php?id=<?= $g['id_g'] ?>" style="text-decoration: none;color:black">
                    <div class="group_box">
                        <h3><?php echo htmlspecialchars($g['g_name']); ?></h3>
                        <h4><?php echo htmlspecialchars($g['amount_of_users']); ?> members | admin: <?php 
                        foreach ($Users as $u):
                        if ($u['id_user'] == $g['owner_id']) {    
                            echo htmlspecialchars($u['imie']);     
                            } 
                        endforeach;?>
                </h4>
                    </div>
                </a>

                

            <?php endforeach; ?>
</body>
</html>
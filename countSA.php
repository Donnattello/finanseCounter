<?php
    session_start();

    if (!isset($_SESSION['user'])) {
        die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
    }


    $transactions = json_decode(file_get_contents('tranzactions.json'), true);
    $user1 = 0;
    $user2 = 0;

    $plikDocelowy = 'historySprints.json';
    $history = [];

    $history = json_decode(file_get_contents($plikDocelowy), true);

    foreach ($transactions as &$tr)
    {
        if ($tr['counted'] == false)
        {
            if ($tr['user_id'] == 1)
            {
                $user1 += $tr['amount'];
                $tr['counted'] = true;
                $tr['sptint'] = count($history) + 1;
            }
                
            elseif ($tr['user_id'] == 2)
            {
                $user2 += $tr['amount'];
                $tr['counted'] = true;
                $tr['sptint'] = count($history) + 1;
            }
        }
           
                
    }
    $sa = ($user1 + $user2)/2;
    $winien1 = $user1 - $sa ;
    $winien2 = $user2 - $sa ;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    echo "Dana: " . number_format($winien1, 2) . "<br>";
    echo "Vika: " . number_format($winien2, 2) . "<br>";

    $nowy_wpis = [
        'sprint_id' => count($history) + 1,
        'done'=> $_SESSION['user']['imie'],
        's.a' => $sa,
        'user1' => $winien1,
        'user2' => $winien2
    ];

    $history[] = $nowy_wpis;


    // Zapisz do pliku

    if (file_put_contents('historySprints.json', json_encode($history, JSON_PRETTY_PRINT)) === false) {
        echo "Błąd zapisu do pliku.";
    } else {
        echo "Dane zostały zapisane.";
    }

    if (file_put_contents('tranzactions.json', json_encode($transactions, JSON_PRETTY_PRINT)) === false) {
        echo "Błąd zapisu do pliku.";
    } else {
        echo "Dane zostały zapisane.";
    }

    header('Location: finanseCounter.php');

?>
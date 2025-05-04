
<?php
session_start();

if (!isset($_SESSION['user'])) {
    die("Dostęp tylko dla zalogowanych. <a href='index.html'>Zaloguj się</a>");
}
?>

<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $date = $_POST['data'];
    $amount = (float)$_POST['amount'];
    $comment = trim($_POST['comment']);

    // Check if the user is logged in
    if (!isset($_SESSION['user']['id_user'])) {
        die("User not logged in.");
    }
    $userId = (int)$_SESSION['user']['id_user'];

    // Get the group ID from the URL
    if (!isset($_GET['id'])) {
        die("Group ID is missing.");
    }
    $groupId = (int)$_GET['id'];

    // Optional: verify that the user belongs to the group
    /*
    $checkStmt = $pdo->prepare("SELECT 1 FROM group_users WHERE user_id = :user_id AND group_id = :group_id");
    $checkStmt->execute([
        'user_id' => $userId,
        'group_id' => $groupId
    ]);

    if ($checkStmt->rowCount() === 0) {
        die("You do not belong to this group.");
    }
    */
    // Insert the transaction
    $stmt = $pdo->prepare("
        INSERT INTO transactions (data, user_id, comment, counted, amount, group_id)
        VALUES (:date, :user_id, :comment, 0, :amount, :group_id)
    ");
    $stmt->execute([
        'date' => $date,
        'user_id' => $userId,
        'comment' => $comment,
        'amount' => $amount,
        'group_id' => $groupId
    ]);

    // Redirect or show success message
    header("Location: finanseCounter.php?id=" . $groupId);
    exit;
}


?>
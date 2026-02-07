<?php
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sender_id = (int) ($_POST['from_user_id'] ?? 0);
        $receiver_id = (int) ($_POST['to_user_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);

        if (!$sender_id || !$receiver_id) {
            die("Sender or Receiver id is null");
        }

        if ($sender_id === $receiver_id) {
            die("Sender id cant equal Receiver id");
        }

        if ($amount <= 0) {
            die("Negative or zero amount of money");
        }

        $db->beginTransaction();

        $senderStmt = $db->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
        $senderStmt->execute(['id' => $sender_id]);
        $sender = $senderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$sender) {
            throw new Exception('Sender is not found');
        }

        $receiverStmt = $db->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
        $receiverStmt->execute(['id' => $receiver_id]);
        $receiver = $receiverStmt->fetch(PDO::FETCH_ASSOC);

        if (!$receiver) {
            throw new Exception('Receiver is not found');
        }

        if ($sender['balance'] < $amount) {
            throw new Exception('Too litle money on balance');
        }

        $send = $db->prepare("UPDATE users SET balance = balance - :amount WHERE id = :id");
        $send->execute([
            'amount' => $amount,
            'id' => $sender_id
        ]);

        if ($send->rowCount() !== 1) {
            throw new Exception('Sender update failed');
        }

        $from_log = $db->prepare("INSERT INTO logs(action,user_id,created_at,amount) VALUES('transfer_out', :id,NOW(),:amount)");
        $from_log->execute(['id' => $sender_id, 'amount' => $amount]);

        $receive = $db->prepare("UPDATE users SET balance = balance + :amount WHERE id = :id");
        $receive->execute([
            'amount' => $amount,
            'id' => $receiver_id
        ]);
        if ($receive->rowCount() !== 1) {
            throw new Exception('Receiver update failed');
        }

        $to_log = $db->prepare("INSERT INTO logs(action,user_id,created_at,amount) VALUES('transfer_in', :id,NOW(), :amount)");
        $to_log->execute(['id' => $receiver_id, 'amount' => $amount]);

        $db->commit();


        echo 'Transaction sucesfull!!!';
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        echo 'Error: ' . $e->getMessage();
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="post">
        <label for="sender">From</label>
        <input type="text" name="from_user_id" id="sender">
        <label for="receiver">To</label>
        <input type="text" name="to_user_id" id="receiver">
        <label for="amount">Amount</label>
        <input type="text" name="amount" id="amount">
        <button type="submit">Submit</button>
    </form>
</body>

</html>
<?php
require_once('db.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        $sender_id = (int) ($_POST['sender'] ?? 0);
        $receiver_id = (int) ($_POST['receiver'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);

        if(!$sender_id || !$receiver_id){
            die("Sender or Receiver id is null");
        }

        if($sender_id === $receiver_id){
            die("Sender id cant equal Receiver id");
        }

        $db->beginTransaction();

        $stmt = $db->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $sender_id]);
        $sender = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$sender){
            throw new Exception('Sender is not found');
        }

        $stmt->execute(['id' => $receiver_id]);
        $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$receiver){
            throw new Exception('Receiver is not found');
        }

        $db->commit();



    }catch(Exception $e){
        if($db->inTransaction()){
            $db->rollBack();
        }
        echo 'Error: '. $e->getMessage();
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
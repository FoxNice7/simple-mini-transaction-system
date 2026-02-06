<?php
require_once('db.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        $sender = (int) $_POST['sender'];
        $receiver = (int) $_POST['receiver'];
        $amount = (float) $_POST['amount'];


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
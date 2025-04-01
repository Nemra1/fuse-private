<?php
$array_data = array();
if ($f == 'wallet') {
// Database connection details
    require_once("system/wallet/paypal_config.php");
           
    if ($s == 'get_wallet') {
        $xhr['content']= boomTemplate('wallet/my_wallet', $data);
         header("Content-type: application/json");
        echo json_encode($xhr);
        exit();
    }
    if ($s == 'pay_paypal') {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
        
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                PAYPAL_CLIENT_ID,
                PAYPAL_CLIENT_SECRET
            )
        );
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_payment') {
            $token = $_POST['token'];
            $amount = cl_rn_strip($_POST['amount']);
            // Create payment
            $payment = new \PayPal\Api\Payment();
            $payment->setIntent('sale')
                ->setPayer(array('payment_method' => 'paypal'))
                ->setRedirectUrls(array(
                    'return_url' => $data['domain'].'/requests.php?f=wallet&s=pay_paypal&success=true', // Update with your actual return URL
                    'cancel_url' => $data['domain'].'/requests.php?f=wallet&s=pay_paypal&cancel', // Update with your actual cancel URL
                ))
                ->setTransactions(array(array(
                    'amount' => array('total' => $amount, 'currency' => $data['currency']),
                    'name' => 'Wallet Replenishment',
                    'description' => 'Pay For Drop200',
                )));
        
            try {
                $payment->create($apiContext);
                foreach ($payment->getLinks() as $link) {
                    if ($link->getRel() == 'approval_url') {
                        // Return approval URL as JSON
                       echo json_encode(['approvalUrl' => $link->getHref()]);
                      exit;
                    }
                }
            } catch (Exception $ex) {
                echo json_encode(['error' => $ex->getMessage()]);
                exit;
            }
        }
        if (isset($_GET['success']) && $_GET['success'] === 'true') {
            // Handle successful payment
            if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
                $paymentId = $_GET['paymentId'];
                $payerId = $_GET['PayerID'];
                $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
        
                $execution = new \PayPal\Api\PaymentExecution();
                $execution->setPayerId($payerId);
        
                try {
                    $result = $payment->execute($execution, $apiContext);
        
                    // Record payment details in the database
                    $transactionId = $result->getId();
                    $amount = $result->getTransactions()[0]->getAmount()->getTotal();
                    $currency = $result->getTransactions()[0]->getAmount()->getCurrency();
                    $status = $result->getState();
                    $payerEmail = $result->getPayer()->getPayerInfo()->getEmail(); // Get payer email
                    $payerName = $result->getPayer()->getPayerInfo()->getFirstName() . ' ' . $result->getPayer()->getPayerInfo()->getLastName(); // Get payer name
                    $stmt = $pdo->prepare("INSERT INTO boom_payments (transaction_id, amount, currency, status, payer_email, payer_name,hunter,type,notes) VALUES (:transaction_id, :amount, :currency, :status, :payer_email, :payer_name,:hunter,:type, :notes)");
                    $stmt->execute([
                        ':transaction_id' => $transactionId,
                        ':amount' => $amount,
                        ':currency' => $currency,
                        ':status' => $status,
                        ':payer_email' => $payerEmail,
                        ':payer_name' => $payerName,
                        ':hunter' => $data['user_id'],
                        ':type' => 'deposit',
                        ':notes' => 'Deposit successful! Transaction ID: ' . $transactionId,
                    ]);
                    $update_wallet = $data['wallet'] + $amount;
                    $mysqli->query("UPDATE boom_users SET  wallet = wallet+{$update_wallet} WHERE user_id = {$data['user_id']}");
                    $content = $data['user_name']. '<font color="red" class="withdraw_msg"> Deposit '.$amount.' '.$data['currency'].' successful </font>';
                    systemPostChat($data['user_roomid'], $content, array('type'=> 'system__action'));
                    boomNotify('withdraw', array('target'=> $data['user_id'], 'custom'=> $content));
                    header("Location: {$data['domain']}");
                    //header("Content-type: application/json");
                    //echo json_encode($data);
                exit();
                } catch (Exception $ex) {
                    echo "Error executing payment: " . $ex->getMessage();
                }
            } else {
                echo "Payment failed. No payment ID or Payer ID found.";
            }
        }
        
        if (isset($_GET['cancel'])) {
            header("Location: {$data['domain']}");
            //echo "Payment was cancelled. Please try again.";
        }
    }
     if ($s == 'send_money_search') {
          if (isset($_POST['search_box'], $_POST['q'])){
           $text = escape($_POST['q']);
          $search_query = runWalletSearch($text);
            header("Content-type: application/json");
            echo json_encode($search_query);
            exit(); 
        }
     }
if ($s == 'send' && boomLogged() === true) {
    $user_id  = (!empty($_POST['user_id']) && is_numeric($_POST['user_id'])) ? $_POST['user_id'] : 0;
    $amount   = (!empty($_POST['amount_to_user']) && is_numeric($_POST['amount_to_user'])) ? $_POST['amount_to_user'] : 0;
    $userdata = fuse_user_data($user_id);
    $my_wallet = floatval($data['wallet']);
    
    if (empty($user_id) || empty($amount) || empty($userdata) || $amount <= 0) {
        $array_data['message'] = [
            "amount" => $amount,
            "alert" => 'Amount OR Receiver cannot be 0 or Empty',
        ];              
        $array_data['status']  = 150;
    } else if ($my_wallet < $amount) {
        $array_data['message'] = [
            "amount" => $amount,
            "alert" => 'You dont have enough money to send',
        ];              
        $array_data['status']  = 100;
    } else {
        $me = $data['user_id'];
        $him = $user_id;
        
        if ($me == $him) {
            $array_data['message'] = [
                "amount" => $amount,
                "alert" => 'You cant send money to yourself.',
            ];              
            $array_data['status']  = 300;
        } else {
            // Prepared statements to prevent SQL injection
            $update_receiver = $mysqli->prepare("UPDATE `boom_users` SET `wallet` = wallet + ? WHERE `user_id` = ?");
            $update_sender   = $mysqli->prepare("UPDATE `boom_users` SET `wallet` = wallet - ? WHERE `user_id` = ?");
            $update_receiver->bind_param('di', $amount, $user_id);
            $update_sender->bind_param('di', $amount, $me);
            if ($update_receiver->execute() && $update_sender->execute()) {
                $recipient_name = $userdata['user_name'];
                $local_transactionId = generateRandomString(6);
                $donation_msg = $data['user_name'] . ' Sent ' . $amount . ' -' . $data['currency'] . ' To ' . $recipient_name;
                $insert_trans = $mysqli->prepare("INSERT INTO `boom_payments` 
                    (transaction_id, amount, currency, status, payer_email, payer_name, hunter, type, notes, target) 
                    VALUES (?, ?, ?, 'approved', ?, ?, ?, 'donation', ?, ?)");
                $insert_trans->bind_param('sdsssiss', $local_transactionId, $amount, $data['currency'], $data['user_email'], $data['user_name'], $data['user_id'], $donation_msg, $userdata['user_id']);
                if ($insert_trans->execute()) {
                    $array_data['message'] = [
                        "amount" => $amount,
                        "alert" => 'Your money was successfully sent to ' . $userdata['user_name'],
                    ];     
                    $array_data['status'] = 200;
                } else {
                    $array_data['message'] = [
                        "alert" => 'Transaction failed. Please try again later.',
                    ];
                    $array_data['status'] = 500;
                }
            } else {
                $array_data['message'] = [
                    "alert" => 'Failed to update wallet balance.',
                ];
                $array_data['status'] = 500;
            }
        }
    }

    header("Content-type: application/json");
    echo json_encode($array_data);
    exit(); 
}
     if ($s == 'transaction' && boomLogged() === true) {
         $trans_content= get_translations();
         if (!empty($trans_content)) {
          $array_data['content'] = $trans_content; 
         }else{
               $array_data['content'] = emptyZone($lang['empty']);
            }
        header("Content-type: application/json");
       echo json_encode($array_data);
       exit();        
     }
     if ($s == 'my_points' && boomLogged() === true) {
        $res['content']= boomTemplate('wallet/my_points', $data);
         header("Content-type: application/json");
        echo json_encode($res);
        exit();
       
     }     
}
?>
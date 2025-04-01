<?php
$array_data = array();
if ($f == 'one_signal') {
         if ($s == 'mass_notifications') {
            $message = $_POST['mass_message'];
             $array_data['content']= sendNotificationToAll($message);
             header("Content-type: application/json");
             echo json_encode($array_data);
             exit();             
         
    }
}
?>
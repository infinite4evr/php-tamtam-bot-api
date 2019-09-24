<?php

/*
$data = array(
    'url' => 'https://timezoneret.000webhostapp.com/tamtam.php'
);
 
$payload = json_encode($data);
 
// Prepare new cURL resource
$ch = curl_init('https://botapi.tamtam.chat/subscriptions?access_token=i32uy6gZMD2NCt_xfBT-bDCC5NXmUyb05Wh6Hp1hkJw');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
 
// Set HTTP Header for POST request 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload))
);

var_dump(curl_exec($ch));
*/

$button = array(
    array('type' => 'callback', 'text' => 'hello', 'payload' => ' i am back','intent' => 'positive'),
    array('type' => 'callback', 'text' => 'hello1', 'payload' => ' i am back1','intent' => 'negative')
);


$buttons = ['buttons' => array($button)];

$data = array(
    'text' => 'hello',
    'attachments' => [
        ['type' => 'inline_keyboard', 'payload' => $buttons]
    ]
);
 
$payload = json_encode($data);
print_r($payload);
 
// Prepare new cURL resource
$ch = curl_init('https://botapi.tamtam.chat/messages?access_token=i32uy6gZMD2NCt_xfBT-bDCC5NXmUyb05Wh6Hp1hkJw&user_id=574207791461');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
 
// Set HTTP Header for POST request 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload))
);

var_dump(curl_exec($ch));

?>
 
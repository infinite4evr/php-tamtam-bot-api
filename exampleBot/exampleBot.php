<?php

require_once(dirname(__FILE__).'/../TamTam.php');
$bot = new TamTam('Bot token');

$text = $bot->getMessageText();
$user_id = $bot->getSenderUserId();
$userName = $bot->getSenderName();


if($text == '/start'){    
    $content = ['user_id' => $user_id, 'text' => "Hi $userName, I am an example bot for PHP TamTam library <https://github.com/infinite4evr/php-tamtam-bot-api>, click /show to see a keybaord, Thank you"];
    $bot->sendMessage($content);
    return;
}
if($text == '/show'){    
    $content = ['user_id' => $user_id, 'text' => "Hi $userName \n\nHere are my example commands you can test \n\n /imageAttachment \n /videoAttachment \n /fileAttachment \n /inlinekeyboard"];
    $bot->sendMessage($content);
    return;
}
if($text == '/imageAttachment'){
    $content = ['user_id' => $user_id, 'photo' => 'exampleBot/media/upload.jpeg', 'text' => 'imageAttachment'];
    $bot->sendPhoto($content);
}
if($text == '/videoAttachment'){
    $content = ['user_id' => $user_id, 'token' => 'BzMCDrcpTULSLdWeB4kfQXRqxci3QsQqmRWbNgGkg50', 'text' => 'videoAttachment'];
    $bot->sendVideo($content);
}
if($text == '/fileAttachment'){
    $content = ['user_id' => $user_id, 'file' => 'exampleBot/media/upload.json', 'text' => 'fileAttachment'];
    $bot->sendFile($content);
}
if($text == '/audioAttachment'){
    $content = ['user_id' => $user_id, 'audio' => 'exampleBot/media/upload.mp3', 'text' => 'audioAttachment'];
    $bot->sendAudio($content);
}
if($text == '/inlineKeyboard'){
    $callbackButton = $bot->buildCallbackButton('I am Callback Button', 'callback_data', 'positive');
    $linkButton =  $bot->buildLinkButton('I am link', 'https://infinite4evr.com');
    $contactButton = $bot->buildRequestContactButton('I am requesting Contact');
    $geoLocation = $bot->buildRequestGeoLocationButton('I am geo Request');
    $keyboard = [
                  [$callbackButton],         // each row, array of buttons
                  [$linkButton],
                  [$contactButton,$geoLocation]  // two buttons in same row
    ];
    $inlineKeyboard = $bot->buildInlineKeyboard($keyboard);  // making the final keyboard
    $content = ['user_id' => $user_id,'text' => 'All Inline Buttons', 'attachments' => [$inlineKeyboard]];
    $bot->sendMessage($content);
}









?>
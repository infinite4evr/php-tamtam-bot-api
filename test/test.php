<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(dirname(__FILE__).'/../TamTam.php');
$bot = new TamTam('ZpW8TbSL3d_kU-Yh50LQn-45zrqY0JiiCPWnODVD1KY');
/*
$icon = ['url' => 'https://image.shutterstock.com/image-photo/large-beautiful-drops-transparent-rain-260nw-668593321.jpg'];
$content = ['chatId' => '-71448008960869', 'icon' => $icon];
$bot->editChat($content);


$content = ['chatId' => '-71448008960869', 'action' => 'sending_audio'];
$bot->sendAction($content);

*/

$button = $bot->buildCallbackButton('hello', 'heelo1', 'positive');
$buttons = [$button];
$inline_keyboard = $bot->buildInlineKeyboard($buttons);
print_r($inline_keyboard);
$content = ['chat_id' => '-71448008960869', 'text' => "I am okThen a bot", 'attachments' => [$inline_keyboard]];
//$bot->setWebhook('https://timezoneret.000webhostapp.com/tamtam.php');
$result = $bot->sendMessage($content);

$result = $bot->getUpdates();
$callbackId = $result['updates'][0]['callback']['callback_id'];


$content = ['callback_id' => $callbackId ];
$newMessage = ['text' => 'hello'];
$content = ['callback_id' => $callbackId, 'message' => $newMessage];
var_dump($content);
var_dump($bot->answerOnCallback($content));






?>
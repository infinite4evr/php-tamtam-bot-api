<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(dirname(__FILE__).'/../TamTam.php');
$bot = new TamTam('Bot Token');
$bot->setWebhook('https://yourwebhook.com');

?>
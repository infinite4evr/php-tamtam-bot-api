<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(dirname(__FILE__).'/../TamTam.php');
$bot = new TamTam('ZpW8TbSL3d_kU-Yh50LQn-45zrqY0JiiCPWnODVD1KY');
$bot->deleteWebhook('https://api.freeroid.com/bots/tamExample/exampleBot/example.php');



?>
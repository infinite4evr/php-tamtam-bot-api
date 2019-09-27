# PHP-TamTam-Bot-Api
[![CodeFactor](https://www.codefactor.io/repository/github/infinite4evr/php-tamtam-bot-api/badge)](https://www.codefactor.io/repository/github/infinite4evr/php-tamtam-bot-api)
![PHP](https://img.shields.io/badge/php-%3E%3D5.3-8892bf.svg)
![CURL](https://img.shields.io/badge/cURL-required-green.svg)

A very simple PHP [TamTam Bot API](https://dev.tamtam.chat).    
Compliant with the Sep,2019 tamtam Bot API update.


A working example bot is available to test at [@ExampleBot](https://tt.me/ExampleBot)
Code available at /exampleBot/exampleBot.php

Requirements
---------

* PHP >= 5.3
* Curl extension for PHP5 must be enabled.
* tamtam API key, you can get one simply with [@primeBot](https://dev.tamtam.chat/#section/About/@PrimeBot) with simple commands right after creating your bot.

For the WebHook:
* TamTam does not require ssl as per my knowledge, so just put up your library on a server and use the setWebhook() command

Download
---------

#### Using Composer
 
* Uploading soon to packagist

#### Using Git

From a project directory, run:
```
git clone https://github.com/infinite4evr/php-tamtam-bot-api.git
```

Installation
---------

#### Via tamtamBotPHP class

Copy tamtam.php into your server and include it in your new bot script:
```php
require_once('tamtam.php');

$tamtam = new tamtam('YOUR bot TOKEN HERE');
```

Note: To enable error log file, also copy TamTamErrorLogger.php in the same directory of TamTam.php file.

Configuration (WebHook)
---------
Use the tamtam class setWebhook('url) method

Examples
---------

```php
$tamtam = new tamtam('YOUR tamtam TOKEN HERE');
$tamtam->setWebhook('https://mywebhook.com/mywebhook.php');
//to delete webhook 
$tamtam->deleteWebhook('https://mywebhook.com/mywebhook.php');
//to check subscriptions 
$tamtam->getSubscriptions());

```


```php
$tamtam = new tamtam('YOUR tamtam TOKEN HERE');

$chatId = $tamtam->getRecipientChatId();
$content = array('chat_id' => $chatId, 'text' => 'Test');
$tamtam->sendMessage($content);
```

If you want to get some specific parameter from the tamtam response:
```php
$tamtam = new tamtam('YOUR tamtam TOKEN HERE');

$result = $tamtam->getData();
$text = $result['message']['body']['text'];
$userId = $result['message'] ['sender']['user_id'];
$content = array('user_id' => $userId, 'text' => 'Test');  // it can be any of user_id or chat_id
$tamtam->sendMessage($content);
```

To send a Photo :
```php
// Load a local file to upload. If is already on tamtam's Servers just pass the resource id
$img = '/path/to/file' ;  // relative path, if you're passing abolsolute path then set $absolutePath parameter = true;
$content = array('chat_id' => $chat_id, 'photo' => $img );
$tamtam->sendPhoto($content);
```
See update.php or update cowsay.php for the complete example.
If you wanna see the CowSay Bot in action [add it](https://tamtam.me/cowmooobot).

If you want to use getUpdates instead of the WebHook you need to call the the  getUpdates() function inside a loop for cycle.
```php
$tamtam = new tamtam('YOUR tamtam TOKEN HERE');

$req = $tamtam->getUpdates();

for ($i = 0; $i < $tamtam-> UpdateCount(); $i++) {
	// You NEED to call serveUpdate before accessing the values of message in tamtam Class
    $data = $req[$i];
    $tamtam->setData($data);
	$text = $tamtam->getText();
	$chat_id = $tamtam->getChatID();

	if ($text == '/start') {
		$reply = 'Working';
		$content = array('chat_id' => $chat_id, 'text' => $reply);
		$tamtam->sendMessage($content);
	}
	// DO OTHER STUFF
}
```

Functions
------------

For a complete and up-to-date functions documentation check http://infinite4evr.github.io/php-tamtam-bot-api/

Build keyboards
------------

tamtam's bots can have only one kind of keyboard : inline_keyboard 
The InlineKeyboard is linked to a particular message

![ReplyKeabordExample](https://imgur.com/6cwM5VX)
using this code:
```php
    // 4 kinds of buttons possible, please refer to documentation
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
```


More example coming soon, check /exampleBot/exampleBot.php for an example

License
------------

This open-source software is distributed under the MIT License. See LICENSE.md

Contributing
------------

All kinds of contributions are welcome - code, tests, documentation, bug reports, new features, etc...

* Send feedbacks.
* Submit bug reports.
* Write/Edit the documents.
* Fix bugs or add new features.

Contact me
------------

You can contact me [via tamtam](https://tt.me/infinite4evr/) but if you have an issue please [open](https://github.com/infinite4evr/php-tamtam-bot-api/issues) one.


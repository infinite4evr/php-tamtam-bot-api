<?php

require_once(dirname(__FILE__).'/vendor/autoload.php');
use monolog\monolog;


class Tamtam
{

  private $apiDomain = 'botapi.tamtam.chat';
  private $bot_token = '';
  private $updates = [];
  private $data = [];
  private $errorLogging;

  /*
  parameters 
  $bot_token  -> Bot Token
  $errorLogging -> Bool[T/F] ( have to log error or not ) Default = true
  */
  public function __construct($bot_token, $errorLogging = true)
  {
      $this->bot_token = $bot_token;
      $this->$errorLogging = $errorLogging;

  }
  
  /* 
  API Endpoint
  parmeters 
  $apiMethod -> api method to call in request
  $content -> user content for message
  $method -> request method get/post/patch, default == post
  */
  public function endpoint($apiMethod, $content, $method = 'POST')
  {
      $url = 'https://'.$this->$apiDomain.'/'.$apiMethod.'?access_token='.$bot_token.'?';
      $reply = $this->callAPI($method,$url,$content);
      return json_decode($reply, true);
  }
  /*
  API Endpoint
  parmeters 
  $method -> request method get/post/patch, default == post
  $data -> user content for message
  $url -> full url with endpoint concatenated
  */

  private function callAPI($method, $url, $data)
  {
    $curl = curl_init();
 
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "GET":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
          if ($data)
              curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;       
       case 'DELETE':
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       case 'PATCH':
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;    
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
 
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
 }

## MOST OF THE METHODS OF THE LIBRARY BEGINS FROM HERE ##

 /*
 Description : Returns info about current bot. Current bot can be identified by access token. Method returns bot identifier, name and avatar (if any)
 parameters : null
 For full info Visit : https://dev.tamtam.chat/#operation/getMyInfo
 */
 public function getMyInfo()
 {
     return $this->endpoint('me', [], 'GET');
 }
 /*
 Description : Edits current bot info. Fill only the fields you want to update. All remaining fields will stay untouched
 parameters : $content
        Name         |   Type     | Required/Optional
        name         |   string   |    optional
        username     |   string   |    optional
        description  |   string   |    optional
        commands     |   array    |    optional        // Array of command objects for bot
        photo        |   array    |    optional        // photo object
 For full info : https://dev.tamtam.chat/#operation/editMyInfo
 */

 public function editMyInfo($content)
 {
     return $this->endpoint('me', $content, 'PATCH');
 }
 /*
 Description : eturns info about chats that bot participated in: a result list and marker points to the next page
 parameters : $content
        Name         |   Type     | Required/Optional
        count        |   int      |    optional
        marker       |   int      |    optional
 For full info : https://dev.tamtam.chat/#operation/getChats
 */
 public function getChats($content)
 {
      return $this->endpoint('chats', $content, 'GET');
 }
 /*
 Description : Returns info about chat
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    required
 For full info : https://dev.tamtam.chat/#operation/getChat
 */
 public function getChat($content)
 {
     return $this->endpoint('chats', $content, 'GET');
 }
 /*
 Description : Edits chat info: title, icon, etc…
 paramters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    required    
        icon         |   object   |    optional
        title        |   string   |    optional
 For full info : https://dev.tamtam.chat/#operation/editChat
 */
 public function editChat($content)
 {
     return $this->endpoint('chats', $content, 'PATCH');
 }
 /* 
 Description : Send bot action to chat
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    required    
        action       |   enum     |    required
 For full info : https://dev.tamtam.chat/#operation/sendAction
 */
 public function sendAction($content)
 {
     return $this->endpoint('chats', $content, 'POST');
 }
 /*
 Description : Returns chat membership info for current bot
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    required    
 For full info : https://dev.tamtam.chat/#operation/getMembership
 */
 public function getMembership($content)
 {
     return $this->endpoint('chats', $content, 'GET');
 }
 /*
 Description : Returns chat membership info for current bot
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    required    
 For full info : https://dev.tamtam.chat/#operation/getMembership
 */
 public function leaveChat($content)
 {
     return $this->endpoint('chats', $content);
 }
 /*
 Description : Removes bot from chat members.
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    required    
 For full info : https://dev.tamtam.chat/#operation/leaveChat
 */
 public function getAdmins($content)
 {
     return $this->endpoint('chats', $content, 'GET');
 }
 /*
 Description : Returns users participated in chat.
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    optional
        user_ids     |   array    |    optional
        marker       |   int      |    optional   
 For full info :https://dev.tamtam.chat/#operation/getMembers
 */ 
 public function getMembers($content)
 {
     return $this->endpoint('chats', $content, 'GET');
 }
 /*
 Description : Adds members to chat. Additional permissions may require.
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    optional
        user_ids     |   array    |    required 
 For full info :https://dev.tamtam.chat/#operation/addMembers
 */
 public function addMembers($content)
 {
     return $this->endpoint('chats', $content, 'POST');
 }
 /*
 Description : Removes member from chat. Additional permissions may require.
 parameters : $content
        Name         |   Type     | Required/Optional
        chatId       |   int      |    optional
        user_id      |   int      |    required 
 For full info :https://dev.tamtam.chat/#operation/removeMember
 */
 public function removeMember($content)
 {
     return $this->endpoint('chats', $content, 'DELETE');
 }
 /*
 Description : In case your bot gets data via WebHook, the method returns list of all subscriptions
 parameters : $content
        Name         |   Type     | Required/Optional
 For full info :https://dev.tamtam.chat/#operation/getSubscriptions
 */
 public function getSubscription()
 {
     return $this->endpoint('subscriptions', [], 'GET');
 }
 /*
 Description : Subscribes bot to receive updates via WebHook. After calling this method, the bot will receive notifications about new events in chat rooms at the specified URL.\n\nYour server **must** be listening on one of the following ports: **80, 8080, 443, 8443, 16384-32383**
 parameters : $content
        Name         |   Type     | Required/Optional
        url          |   string   | required
        update_types |   array    | optional
        version      |   string   | optional
 For full info :https://dev.tamtam.chat/#operation/subscribe
 */
 public function subscribe($content)
 {
     return $this->endpoint('subscriptions', $content, 'POST');
 }
 /*
 Description : Unsubscribes bot from receiving updates via WebHook. After calling the method, the bot stops receiving notifications about new events. Notification via the long-poll API becomes available for the bot
 parameters : $content
        Name         |   Type     | Required/Optional
        url          |   string   | required
 For full info :https://dev.tamtam.chat/#operation/unsubscribe
 */ 
 public function usubscribe($content)
 {
     return $this->endpoint('subscription', $content, 'DELETE');
 }
 /*
 Description : Returns the URL for the subsequent file upload.\n\nFor example, you can upload it via curl:\n\n```curl -i -X POST\n  -H \"Content-Type: multipart/form-data\"\n  -F \"data=@movie.mp4\" \"%UPLOAD_URL%\"```\n\nTwo types of an upload are supported:\n- single request upload (multipart request)\n- and resumable upload.\n\n##### Multipart upload\nThis type of upload is a simpler one but it is less\nreliable and agile. If a `Content-Type`: multipart/form-data header is passed in a request our service indicates\nupload type as a simple single request upload.\n\nThis type of an upload has some restrictions:\n\n- Max. file size - 2 Gb\n- Only one file per request can be uploaded\n- No possibility to restart stopped / failed upload\n\n##### Resumable upload\nIf `Content-Type` header value is not equal to `multipart/form-data` our service indicated upload type\nas a resumable upload.\nWith a `Content-Range` header current file chunk range and complete file size\ncan be passed. If a network error has happened or upload was stopped you can continue to upload a file from\nthe last successfully uploaded file chunk. You can request the last known byte of uploaded file from server\nand continue to upload a file.\n\n##### Get upload status\nTo GET an upload status you simply need to perform HTTP-GET request to a file upload URL.\nOur service will respond with current upload status,\ncomplete file size and last known uploaded byte. This data can be used to complete stopped upload\nif something went wrong. If `REQUESTED_RANGE_NOT_SATISFIABLE` or `INTERNAL_SERVER_ERROR` status was returned\nit is a good point to try to restart an upload
 parameters : $content
        Name         |   Type     | Required/Optional
        type         |   Enum     | required
 For full info :https://dev.tamtam.chat/#operation/getUploadUrl
 */ 
 public function getUploadUrl($content)
 {
     return $this->endpoint('uploads', $content, 'POST');
 }
 /*
 Description :Returns messages in chat: result page and marker referencing to the next page. Messages traversed in reverse direction so the latest message in chat will be first in result array. Therefore if you use `from` and `to` parameters, `to` must be **less than** `from`"
 parameters : $content
        Name         |   Type     | Required/Optional
        chat_id      |   int      | optional
        message_ids  |   array    | optional 
        from         |   int      | optional
        to           |   int      | optional
        count        |   int      | required

 For full info :https://dev.tamtam.chat/#operation/getUploadUrl
 */ 





       
            

    


  

   







}


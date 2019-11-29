<?php
/**
 * TamTam Main Api Class*
 * @author Ashu <ggs.sudhanshu@gmail.com> (github.com/infinite4evr) 
 */
if (file_exists('TamTamErrorLogger.php')) {
    require_once 'TamTamErrorLogger.php';
}

class Tamtam
{

  public $apiDomain = 'botapi.tamtam.chat';
  private $bot_token = '';
  private $data = [];
  private $errorLogging;

  /*
  * parameters :
  *  $bot_token  -> Bot Token
  *  $errorLogging -> Bool[T/F] ( have to log error or not ) Default = true
  */
  public function __construct($bot_token, $autoFetchData = true, $errorLogging = true)
  {
      $this->bot_token = $bot_token;
      $this->errorLogging = $errorLogging;
      if($autoFetchData == true){
          $data = file_get_contents('php://input');
          $this->data = json_decode($data, true);
        
      }

  }
  /**
   *  return the HTTP 200 to TamTam
  */
  public function respondSuccess()
  {
      http_response_code(200);
      return json_encode(['status' => 'success']);
  }
  
  /**
  * API Endpoint
  * parmeters : 
  *   apiMethod -> api method to call in request
  *   content -> user content for message
  *   method -> request method get/post/patch, default == post
  * query parameter is array of parameters which are to sent in header
  * rest other parameters in content will be encoded as json and sent as request body
  *
  */
  public function endpoint($apiMethod, $content, $method = 'POST', array $queryParameters = null)
  {
      $url = 'https://'.$this->apiDomain.'/'.$apiMethod.'?access_token='.$this->bot_token;
      if($queryParameters!=null){
          foreach($queryParameters as $parameter){
        
              if(isset($content[$parameter])){
                $url = $url."&$parameter=".$content[$parameter];
                unset($content[$parameter]);
              }             
          }
      }
      $reply = $this->callAPI($method,$url,$content);
      return json_decode($reply, true);
  }
  /*
  API Endpoint
  parmeters :
    $method -> request method get/post/patch, default == post
    $data -> user content for message
    $url -> full url with endpoint concatenated
  */

  private function callAPI($method, $url, $content)
  {
    $curl = $this->configCurl($method, $content, $url);     
    // EXECUTE:
    $result = curl_exec($curl);
    if ($result === false) {
        $result = json_encode(['ok'=>false, 'curl_error_code' => curl_errno($curl), 'curl_error' => curl_error($curl)]);
    }
    echo $result;
    curl_close($curl);
    if ($this->errorLogging) {   
        var_dump(class_exists('TamTamErrorLogger'));
        if (class_exists('TamTamErrorLogger')) {
            $content = json_decode($content,true);
            $loggerArray = ($this->getData() == null) ? [$content] : [$this->getData(), $content];
            TamTamErrorLogger::log(json_decode($result, true), $loggerArray);
        }
    }
    return $result;
 }
 /**
  * configures curl for proper content and headers
  *
  * @param string $method
  * @param array $content
  * @return object curl object
  */
 public function configCurl($method, $content, $url)
 {
    $curl = curl_init();
    $content = json_encode($content);
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($content)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
          break;
       case "GET":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
          if ($content)
              curl_setopt($curl, CURLOPT_POSTFIELDS, $content);			 					
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($content)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $content);			 					
          break;       
       case 'DELETE':
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
          if ($content)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $content);			 					
          break;
       case 'PATCH':
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
          if ($content)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $content);			 					
          break;    
       default:
          if ($content)
             $url = sprintf("%s?%s", $url, http_build_query($content));
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($content))
    );
 
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    return $curl;
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
 parameters of :
    $content (array)
        Name         |   Type     | Required/Optional
        name         |   string   |    optional
        username     |   string   |    optional
        description  |   string   |    optional
        commands     |   array    |    optional        // Array of command objects for bot
        photo        |   array    |    optional        // photo object
 For full info : https://dev.tamtam.chat/#operation/editMyInfo
 */

 public function editMyInfo(array $content)
 {
     $queryParameters = null ;
     return $this->endpoint('me', $content, 'PATCH');
 }
 /*
 Description : returns info about chats that bot participated in: a result list and marker points to the next page
 parameters of :
   $content (array)
        Name         |   Type     | Required/Optional
        count        |   int      |    optional
        marker       |   int      |    optional
 For full info : https://dev.tamtam.chat/#operation/getChats
 */
 public function getChats(array $content = null)
 {
      $queryParameters = ['count', 'marker'];
      return $this->endpoint('chats', $content, 'GET', $queryParameters);
 }
 /*
 Description : Returns info about chat
 parameters of : 
    $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    required
 For full info : https://dev.tamtam.chat/#operation/getChat
 */
 public function getChat(array $content)
 {
     return $this->endpoint('chats/'.$content['chat_id'], $content, 'GET', null);
 }
 /*
 Description : Edits chat info: title, icon, etc…
 paramters of : 
    $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    required    
        icon         |   object   |    optional  // new icon
        title        |   string   |    optional  // new title 
 For full info : https://dev.tamtam.chat/#operation/editChat
 */
 public function editChat(array $content)
 {   
     return $this->endpoint('chats/'.$content['chat_id'], $content, 'PATCH', null);
 }
 /* 
 Description : Send bot action to chat
 parameters of : 
    $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    required    
        action       |   enum     |    required
 For full info : https://dev.tamtam.chat/#operation/sendAction
 */
 public function sendAction(array $content)
 {
     return $this->endpoint('chats/'.$content['chat_id'].'/actions', $content, 'POST', null);
 }
 /*
 Description : Returns chat membership info for current bot
 parameters of :
   $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    required    
 For full info : https://dev.tamtam.chat/#operation/getMembership
 */
 public function getMembership(array $content)
 {
     return $this->endpoint('chats/'.$content['chat_id'].'/members/me', $content, 'GET');
 }
 /*
 Description : Returns chat membership info for current bot
 parameters of :
    $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    required    
 For full info : https://dev.tamtam.chat/#operation/getMembership
 */
 public function leaveChat(array $content)
 {
     return $this->endpoint('chats/'.$content['chat_id'].'/members/me', $content, 'DELETE');
 }
 /*
 Description : Removes bot from chat members.
 parameters of :
   $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    required    
 For full info : https://dev.tamtam.chat/#operation/leaveChat
 */
 public function getAdmins(array $content)
 {
     return $this->endpoint('chats/'.$content['chat_id'].'/members/admins', $content, 'GET');
 }
 /*
 Description : Returns users participated in chat.
 parameters of :
   $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    optional
        user_ids     |   array    |    optional
        marker       |   int      |    optional   
 For full info :https://dev.tamtam.chat/#operation/getMembers
 */ 
 public function getMembers(array $content)
 {
     $queryParameters = ['user_ids', 'marker', 'count'];
     return $this->endpoint('chats/'.$content['chat_id'].'/members', $content, 'GET', $queryParameters);
 }
 /*
 Description : Adds members to chat. Additional permissions may require.
 parameters of :
   $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    optional
        user_ids     |   array    |    required 
 For full info :https://dev.tamtam.chat/#operation/addMembers
 */
 public function addMembers(array $content)
 {
     return $this->endpoint('chats/'.$content['chat_id'].'/members', $content, 'POST');
 }
 /*
 Description : Removes member from chat. Additional permissions may require.
 parameters of : 
   $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      |    optional
        user_id      |   int      |    required 
 For full info :https://dev.tamtam.chat/#operation/removeMember
 */
 public function removeMember(array $content)
 {
     $queryParameters = ['user_id'];
     return $this->endpoint('chats/'.$content['chat_id'].'/members', $content, 'DELETE', $queryParameters);
 }
 /*
 Description : In case your bot gets data via WebHook, the method returns list of all subscriptions
 parameters of : 
    $content(array)
        Name         |   Type     | Required/Optional
 For full info :https://dev.tamtam.chat/#operation/getSubscriptions
 */
 public function getSubscription()
 {
     return $this->endpoint('subscriptions', [], 'GET');
 }
 /*
 Description : Subscribes bot to receive updates via WebHook. After calling this method, the bot will receive notifications about new events in chat rooms at the specified URL.\n\nYour server **must** be listening on one of the following ports: **80, 8080, 443, 8443, 16384-32383**
 parameters of :
   $content(array)
        Name         |   Type     | Required/Optional
        url          |   string   | required
        update_types |   array    | optional
        version      |   string   | optional
 For full info :https://dev.tamtam.chat/#operation/subscribe
 */
 public function subscribe(array $content)
 {
     return $this->endpoint('subscriptions', $content, 'POST');
 }
 /*
 Description : Unsubscribes bot from receiving updates via WebHook. After calling the method, the bot stops receiving notifications about new events. Notification via the long-poll API becomes available for the bot
 parameters of : 
   $content(array)
        Name         |   Type     | Required/Optional
        url          |   string   | required
 For full info :https://dev.tamtam.chat/#operation/unsubscribe
 */ 
 public function unsubscribe(array $content)
 {
     $queryParameters = ['url'];
     return $this->endpoint('subscriptions', $content, 'DELETE', $queryParameters);
 }
 /*
 Description : Returns the URL for the subsequent file upload.\n\nFor example, you can upload it via curl:\n\n```curl -i -X POST\n  -H \"Content-Type: multipart/form-data\"\n  -F \"data=@movie.mp4\" \"%UPLOAD_URL%\"```\n\nTwo types of an upload are supported:\n- single request upload (multipart request)\n- and resumable upload.\n\n##### Multipart upload\nThis type of upload is a simpler one but it is less\nreliable and agile. If a `Content-Type`: multipart/form-data header is passed in a request our service indicates\nupload type as a simple single request upload.\n\nThis type of an upload has some restrictions:\n\n- Max. file size - 2 Gb\n- Only one file per request can be uploaded\n- No possibility to restart stopped / failed upload\n\n##### Resumable upload\nIf `Content-Type` header value is not equal to `multipart/form-data` our service indicated upload type\nas a resumable upload.\nWith a `Content-Range` header current file chunk range and complete file size\ncan be passed. If a network error has happened or upload was stopped you can continue to upload a file from\nthe last successfully uploaded file chunk. You can request the last known byte of uploaded file from server\nand continue to upload a file.\n\n##### Get upload status\nTo GET an upload status you simply need to perform HTTP-GET request to a file upload URL.\nOur service will respond with current upload status,\ncomplete file size and last known uploaded byte. This data can be used to complete stopped upload\nif something went wrong. If `REQUESTED_RANGE_NOT_SATISFIABLE` or `INTERNAL_SERVER_ERROR` status was returned\nit is a good point to try to restart an upload
 parameters of : 
    $content(array)
        Name         |   Type     | Required/Optional
        type         |   Enum     | required
 For full info :https://dev.tamtam.chat/#operation/getUploadUrl
 */ 
 public function getUploadUrl(array $content)
 {
     $queryParameters = ['type'];
     $result =  $this->endpoint('uploads', $content, 'POST', $queryParameters);
     return $result['url'];
 }
 /*
 Description :Returns messages in chat: result page and marker referencing to the next page. Messages traversed in reverse direction so the latest message in chat will be first in result array. Therefore if you use `from` and `to` parameters, `to` must be **less than** `from`"
 parameters of : 
   $content(array)
        Name         |   Type     | Required/Optional
        chat_id      |   int      | optional
        message_ids  |   array    | optional 
        from         |   int      | optional
        to           |   int      | optional
        count        |   int      | required

 For full info :https://dev.tamtam.chat/#operation/getMessages
 */ 
 public function getMessages(array $content)
 {
     $queryParameters = ['chat_id', 'message_ids', 'from', 'to', 'count'];
     return $this->endpoint('messages', $content, 'GET', $queryParameters);
 }
 /*
 Description :Sends a message to a chat.\nAs a result for this method new message identifier returns.\n### Attaching media\nAttaching media to messages is a three-step process.\n\nAt first step, you should [obtain a URL to upload](#operation/getUploadUrl) your media files.\n\nAt the second, you should upload binary of appropriate format to URL you obtained at the previous step. See [upload](https://dev.tamtam.chat/#operation/getUploadUrl) section for details.\n\nFinally, if the upload process was successful, you will receive JSON-object in a response body.  Use this object to create attachment. Construct an object with two properties:\n- `type` with the value set to appropriate media type\n- and `payload` filled with the JSON you've got.\n\nFor example, you can attach a video to message this way:\n\n1. Get URL to upload. Execute following:\n```shell\ncurl -X POST 'https://botapi.tamtam.chat/uploads?access_token=%access_token%&type=video'\n```\nAs the result it will return URL for the next step.\n```json\n{\n    \"url\": \"http://vu.mycdn.me/upload.do…\"\n}\n```\n\n2. Use this url to upload your binary:\n```shell\ncurl -i -X POST\n  -H \"Content-Type: multipart/form-data\"\n  -F \"data=@movie.mp4\" \"http://vu.mycdn.me/upload.do…\"\n```\nAs the result it will return JSON you can attach to message:\n```json\n  {\n    \"token\": \"_3Rarhcf1PtlMXy8jpgie8Ai_KARnVFYNQTtmIRWNh4\"\n  }\n```\n3. Send message with attach:\n```json\n{\n    \"text\": \"Message with video\",\n    \"attachments\": [\n        {\n            \"type\": \"video\",\n            \"payload\": {\n                \"token\": \"_3Rarhcf1PtlMXy8jpgie8Ai_KARnVFYNQTtmIRWNh4\"\n            }\n        }\n    ]\n}\n```\n\n**Important notice**:\n\nIt may take time for the server to process your file (audio/video or any binary).\nWhile a file is not processed you can't attach it. It means the last step will fail with `400` error.\nTry to send a message again until you'll get a successful result.",
 parameters of : 
   $content(array)
        Name         |   Type     | Required/Optional
        user_id      |   int      | optional
        chat_id      |   int      | optional 
        text         |   int      | optional
        attachments  |   array    | optional
        link         |   object   | optional
        notify       |   boolean  | required

 For full info :https://dev.tamtam.chat/#operation/sendMessage
 */
 public function sendMessage(array $content)
 {
     $queryParameters = ['user_id', 'chat_id'];
     return $this->endpoint('messages', $content, 'POST', $queryParameters);
 }
 /*
 Description :Updated message should be sent as `NewMessageBody` in a request body. In case `attachments` field is `null`, the current message attachments won’t be changed. In case of sending an empty list in this field, all attachments will be deleted.
 parameters  of: 
   $content(array)
        Name         |   Type     | Required/Optional
        message_id   |   int      | required
        text         |   int      | optional 
        attachment   |   array    | optional
        link         |   object   | optional
        notify       |   boolean  | required

 For full info :https://dev.tamtam.chat/#operation/editMessage
 */
 public function editMessage(array $content)
 {
     $queryParameters = ['message_id'];
     return $this->endpoint('messages', $content, 'PUT', $queryParameters);
 }
 /*
 Description :Deletes message in a dialog or in a chat if bot has permission to delete messages.
 parameters of :
    $content
        Name         |   Type     | Required/Optional
        message_id   |   int      | required

 For full info :https://dev.tamtam.chat/#operation/editMessage
 */
 public function deleteMessage(array $content)
 {
     $queryParameters = ['message_id'];
     return $this->endpoint('messages', $content, 'DELETE', $queryParameters);
 }
 /*
 Description :This method should be called to send an answer after a user has clicked the button. The answer may be an updated message or/and a one-time user notification.",
 parameters of :
    $content
        Name         |   Type     | Required/Optional
        callback_id  |   int      | required
        message      |   object   | optional
        notification |   string   | optional

 For full info :https://dev.tamtam.chat/#operation/answerOnCallback
 */
 public function answerOnCallback(array $content)
 {
     $queryParameters = ['callback_id'];
     return $this->endpoint('answers', $content, 'POST', $queryParameters);
 }
 /*
 Description :You can use this method for getting updates in case your bot is not subscribed to WebHook. The method is based on long polling.\n\nEvery update has its own sequence number. `marker` property in response points to the next upcoming update.\n\nAll previous updates are considered as *committed* after passing `marker` parameter.\nIf `marker` parameter is **not passed**, your bot will get all updates happened after the last commitment.
 parameters of :
    $content
        Name         |   Type     | Required/Optional
        limit        |   int      | required
        timeout      |   int      | optional
        marker       |   int      | optional
        types        |   string   | optional

 For full info :https://dev.tamtam.chat/#operation/answerOnCallback
 */
 public function getUpdates(array $content = null)
 {
     $queryParameters = ['limit', 'timeout', 'marker', 'types'];
     return $this->endpoint('updates', $content, 'GET', $queryParameters);
 }

 /*
 parameters of $content :
 url -> url of an image you want to upload
 token -> any already available image token you have 
 return attachment object of image you can add to attachments array to send
 */
 public function getImageAttachment($content)
 {
     $image_object['type'] = 'image';
     if(isset($content['url'])){
         $image_ = ['url' => $content['url']];
         $image_object['payload'] = $image_;
     }else if(isset($content['token'])){
         $image_ = ['token' => $content['token']];
         $image_object['payload'] = $image_;
     } else if(isset($content['photos'])){
         $image_ = $content['photos'];
         $image_object['payload'] = $image_;
     }
     return $image_object;
 }
  /*
 parameters of $content :
 token -> any already available image token you have 
 return attachment object of video you can add to attachments array to send
 */
 public function getVideoAttachment($content)
 {
     $video_object['type'] = 'video';
     $video['token'] = $content['token'];
     $video_object['payload'] = $video;
     return $video_object;
 }
   /*
 parameters of $content :
 token -> any already available audio token you have 
 return attachment object of audio you can add to attachments array to send
 */
 public function getAudioAttachment($content)
 {
    $audio_object['type'] = 'audio';
    $audio['token'] = $content['token'];
    $audio_object['payload'] = $audio;
    return $audio_object;
 }
  /*
 parameters of $content :
 token -> any already available file token you have 
 return attachment object of file you can add to attachments array to send
 */
 public function getFileAttachment($content)
 {
    $file_object['type'] = 'file';
    $file['token'] = $content['token'];
    $file_object['payload'] = $file;
    return $file_object;
 }
 /* Parameters of $content
 name	   | Sring Nullable   | Contact name
 contactId | integer <int64>  | Contact identifier
 vcfInfo   | string Nullable  | Full information about contact in VCF format
 vcfPhone  | string Nullable  | Contact phone in VCF format
*/

 public function getContactAttachment($content)
 {
     $contact_object['type'] = 'contact';
     $contact_object['payload'] = $content;
    
     return $contact_object;
 }
  /* Parameters of $content
 code	   | Sring   | sticker code
 */

 public function getStickerAttachment($content)
 {
     $sticker_object['type'] = 'sticker';
     $sticker_object['payload'] = $content;
     return $contact_object;
 }
 /* Parameters of $content
 latitude  | double  
 longitute | double
 */
 
 public function getLocationAttachment($content)
 {
     $location_object['type'] = 'location';
     $location_object['payload'] = $content;
     return $location_object;
 }

/*
 Description : Upload attachments using these functions , sendPhoto, sendVideo, sendAudio, sendFile
 parameters of :
    $content 
        Name         |   Type     | Required/Optional
        photo        |   string   | optional 
        token        |   string   | optional

    function  
        absolutePath |   bool     | optiona     // default = false //pass when you are sending abolsute path from root 

 Each of the below give functions will return the token of the uploaded file if upload was succesfull or return false
  
 Returns either the token of the file uploaded if it is not processed yet at the time of request or returns the reponse
 */
 
 public function sendPhoto(array $content, bool $absolutePath = false)
 {
     $_content['type'] = 'photo';
     $_content['file'] = $content['photo'];
     $image_attach ;
     if(isset($content['photo'])){
        $uploadUrl = $this->getUploadUrl($_content);
        $token = $this->upload($uploadUrl, $_content, $absolutePath);
        $image_attach['photos'] = $token;
     }
     $image_attach['photo'] = true;
     $image_attach = $this->getImageAttachment($image_attach); 
     unset($content['photo']);
     $content['attachments'] = [$image_attach];
     return $this->checkForError($this->sendMessage($content), $token);
 }

 public function sendVideo(array $content, bool $absolutePath = false)
 {
     $_content['type'] = 'video'; 
     $videoAttach ;
     if(isset($content['video'])){
        $_content['file'] = $content['video'];
        $uploadUrl = $this->getUploadUrl($_content);
        $token = $this->upload($uploadUrl, $_content, $absolutePath);
        $videoAttach['token'] = $token;
     } else if(isset($content['token'])){
         $videoAttach['token'] = $content['token'];
     }
     $videoAttach = $this->getVideoAttachment($videoAttach); 
     unset($content['video']);
     $content['attachments'] = [$videoAttach];
     return $this->checkForError($this->sendMessage($content), $token);

 }
 public function sendAudio(array $content, bool $absolutePath = false)
 {
    $_content['type'] = 'audio'; 
    $audioAttach ;
    if(isset($content['audio'])){
       $_content['file'] = $content['audio'];
       $uploadUrl = $this->getUploadUrl($_content);
       $token = $this->upload($uploadUrl, $_content, $absolutePath);
       $audioAttach['token'] = $token;
    } else if(isset($content['token'])){
        $audioAttach['token'] = $content['token'];
    }
    $audioAttach = $this->getAudioAttachment($audioAttach); 
    unset($content['audio']);
    $content['attachments'] = [$audioAttach];
    return $this->checkForError($this->sendMessage($content), $token);

 }
 public function sendFile(array $content, bool $absolutePath = false)
 {
    $_content['type'] = 'file'; 
    $fileAttach ;
    if(isset($content['file'])){
       $_content['file'] = $content['file'];
       $uploadUrl = $this->getUploadUrl($_content);
       $token = $this->upload($uploadUrl, $_content, $absolutePath);
       $fileAttach['token'] = $token;
    } else if(isset($content['token'])){
        $fileAttach['token'] = $content['token'];
    }
    $fileAttach = $this->getFileAttachment($fileAttach); 
    unset($content['file']);
    $content['attachments'] = [$fileAttach];
    return $this->checkForError($this->sendMessage($content), $token);
 }

 /*
 Description : Upload main
 parameters of :
    $content
        Name         |   Type     | Required/Optional
        file         |   string   | required
        type         |   string   | required

    function  
        absolutePath |   bool     | optiona     // default = true //pass when you are sending abolsute path from root 
        uploadUrl    |   string   | required 

 Each of the below give functions will return the token of the uploaded file if upload was succesfull or return false
 */

 public function upload($uploadUrl, array $content, bool $absolutePath = false)
 {
    if($absolutePath != false){    
        if($absolutePath == true){
            $path = $content['file'];            
        }else{
            $this->logError('$absolutePath only accepts bool value in class method upload(), pass either true or false');
        }        
    }else {
        $path = dirname(__FILE__).'/'.$content['file'];
    }
    $cfile = new CURLFile($path);
    $cfile->mime = $cfile->getMimeType();
    $post = array(
        'file' => $cfile,
    );
    var_dump($post);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);
    $result = json_decode($result, true);
    
    if(isset($result['token'])){
        return $result['token'];
    }else{
        return $result;
    } 
 }
 /*
  downloads a file to the directory provided
  Params  : 
    Url : url of the file
    local_file_path : path where to download the file
*/ 

 public function downloadFile($url, $local_file_path)
 {
     $in = fopen($url, 'rb');
     $out = fopen($local_file_path, 'wb');
     while ($chunk = fread($in, 8192)) {
         fwrite($out, $chunk, 8192);
     }
     fclose($in);
     fclose($out);
 }
  /*
  return a callbackButton object
  Params  : 
    text : text visible on button
    payload : the callback data that will be sent back
    intent : background color shown on button, default = glass, postive = green, negative = red
 */ 

 public function buildCallbackButton($text, $payload, $intent = 'default')
 {
     $button = [
         'type' => 'callback',
         'text' => $text,
         'payload' => $payload,
         'intent' => $intent
        ];
    return $button;
 }
   /*
  return a linkbutton object
  Params  : 
    text : text visible on button
    url : url of the link
 */
 public function buildLinkButton($text, $url)
 {
     $button = [
         'type' => 'link',
         'text' => $text,
         'url' => $url
        ];
    return $button;
 }
 /*
  return a request_contact object
  Params  : 
    text : text visible on button
 */
 public function buildRequestContactButton($text)
 {
    $button = [
        'type' => 'request_contact',
        'text' => $text
       ];
   return $button;
 }
  /*
  return a request_gio_location object
  Params  : 
    text : text visible on button
    quick : If true, sends location without asking user's confirmation
 */
 public function buildRequestGeoLocationButton($text, $quick = false)
 {
    $button = [
        'type' => 'request_geo_location',
        'text' => $text,
        'quick' => $quick
       ];
   return $button;
 }
 /*
  return a inline_keyboard object
  Params  : 
    buttons: array of button objects returned by above functions 
    
 */

 public function buildInlineKeyboard(array $buttons)
 {
    $buttons = ['buttons' => $buttons];
     $inlineKeyboard = [
         'type' => 'inline_keyboard',
         'payload' => $buttons
     ];
     return $inlineKeyboard;

 }
 //pass url to setWebhook
 public function setWebhook($url, array $update_types = null, $version = null )
 {
     $requestBody['url'] = $url;
     if($update_types!=null){
         $requestBody['update_types'] = $update_types;
     }
     if($version!=null){
         $requestBody['version'] = $version;
     }
    
    return $this->subscribe($requestBody);
 }

 // pass the url to delete the webhook
 public function deleteWebhook($url)
 {
     $requestBody['url'] = $url;
     return $this->unsubscribe($requestBody);
 }
 // returns the data fetched ( webhook )
 public function getData()
 {
    if (empty($this->data)) {
        $rawData = file_get_contents('php://input');
        return json_decode($rawData, true);
    } else {
        return $this->data;
    }
}
 // set your own data
 public function setData(array $data)
 {
    $this->data = $data;
 }
 // returns user_id if any
 public function getSenderUserId()
 {
     return $this->data['message']['sender']['user_id'];
 }
 //return message text if any exists
 public function getMessageText()
 {
    return $this->data['message']['body']['text'];
 }
  // return getRecipientId if any exists
 
 public function getRecipientId()
 {
     if(isset($this->data['message']['recipient']['chat_id'])){
         return $this->data['message']['recipient']['chat_id'];
     }else{
        return $this->data['message']['recipient']['user_id'];
     }
 }
 // return callback button id
 public function getCallbackId()
 {
     return $this->data['callback']['callback_id'];
 }
 // returns callback data i.e payload as they say
 public function getCallbackPayload()
 {
     return $this->data['callback']['payload'];
 }
 /*
  return update_type any one of these : 
  "message_created","message_callback", "message_edited", "message_removed", "bot_added", "bot_removed", "user_added", "user_removed", "bot_started", "chat_title_changed"
*/
 public function getUpdateType()
 {
     return $this->data['update_type'];
 }
 public function UpdateCount()
 {
     return count($this->data['updates']);
 }
 public function getUserName()
 {
     return $this->data['message']['sender']['username'];
 }
 public function getSenderName()
 {
     return $this->data['message']['sender']['name'];
 }
 public function getChatId()
 {
     return $this->data['message']['recipient']['chat_id'];
 }

 public function getFile()
 {
     
 }

 /**
  * checks for error in response for the functions sendPhoto, sendFile, sendVideo, sendAudio
  * Server takes time to process uploads hence it may return a not yet processed error
  * Returns the token of file uploaded but not yet processed
  * file here means photo, video, audio, file
  * if response is successfull the returns the reponse itself 
 */

 public function checkForError($response, $token){
     if(isset($response['code']) && isset($response['message'])){
         return $token;
     } else {
         return $response;
     }     
 } 

}

//class end

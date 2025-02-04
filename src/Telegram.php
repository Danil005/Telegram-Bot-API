<?php

require 'Exception.php';

class telegramBot
{
    const BASE_URL = 'https://api.telegram.org';
    const BOT_URL  = '/bot';
    const FILE_URL = '/file';

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
        if(is_null($this->token)) {
            throw new TelegramException('Required "token" key not supplied');
        }
        $this->baseURL = self::BASE_URL . self::BOT_URL . $this->token . '/';
        $this->baseFileURL = self::BASE_URL . self::FILE_URL . self::BOT_URL . $this->token . '/';
    }

    /**
     * Use this method to receive incoming updates using long polling.
     *
     * @link https://core.telegram.org/bots/api#getupdates
     *
     * @param int $offset
     * @param int $limit
     * @param int $timeout
     *
     * @return Array
     */
    public function pollUpdates($offset = null, $timeout = null, $limit = null)
    {
        $params = compact('offset', 'limit', 'timeout');
        return $this->sendRequest('getUpdates', $params);
    }

    /**
     * A simple method for testing your bot's auth token.
     * Returns basic information about the bot in form of a User object.
     *
     * @link https://core.telegram.org/bots/api#getme
     *
     * @return Array
     */
    public function getMe()
    {
        return $this->sendRequest('getMe', []);
    }

    /**
     * Send text messages.
     *
     * @link https://core.telegram.org/bots/api#sendmessage
     *
     * @param int                        $chat_id
     * @param string                     $text
     * @param string                     $parse_mode
     * @param bool                       $disable_web_page_preview
     * @param int                        $reply_to_message_id
     * @param string|null|KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendMessage($chat_id, $text, $parse_mode = null, $disable_web_page_preview = false, $reply_to_message_id = null, $reply_markup = null)
    {
        $params = compact('chat_id', 'text', 'parse_mode', 'disable_web_page_preview', 'reply_to_message_id', 'reply_markup');
        return $this->sendRequest('sendMessage', $params);
    }

    /**
     * Delete Message
     *
     * @param $chat_id
     * @param $message_id
     *
     * @return array
     */
    public function deleteMessage($chat_id, $message_id)
    {
        $params = compact('chat_id', 'message_id');
        return $this->sendRequest('deleteMessage', $params);
    }

    /**
     * @param int                        $chat_id
     * @param int                        $message_id
     * @param string                     $text
     * @param int|null                   $inline_message_id
     * @param bool|null                  $parse_mode
     * @param mixed|null                 $entities
     * @param bool|null                  $disable_web_page_preview
     * @param string|KeyboardMarkup|null $reply_markup
     *
     * @return mixed
     */
    public function editMessageText($chat_id, $message_id, $text, $inline_message_id = null, $parse_mode = null, $entities = null, $disable_web_page_preview = null, $reply_markup = null)
    {
        $params = compact('chat_id', 'message_id', 'text', 'inline_message_id', 'parse_mode', 'entities', 'disable_web_page_preview', 'reply_markup');
        return $this->sendRequest('editMessageText', $params);
    }

    /**
     * @param int                        $chat_id
     * @param int                        $message_id
     * @param string|KeyboardMarkup|null $reply_markup
     *
     * @param int|null                   $inline_message_id
     *
     * @return mixed
     */
    public function editMessageReplyMarkup($chat_id, $message_id, $reply_markup, $inline_message_id = null)
    {
        $params = compact('chat_id', 'message_id', 'inline_message_id', 'reply_markup');
        return $this->sendRequest('editMessageReplyMarkup', $params);
    }

    /**
     * Forward messages of any kind.
     *
     * @link https://core.telegram.org/bots/api#forwardmessage
     *
     * @param int $chat_id
     * @param int $from_chat_id
     * @param int $message_id
     *
     * @return Array
     */
    public function forwardMessage($chat_id, $from_chat_id, $message_id)
    {
        $params = compact('chat_id', 'from_chat_id', 'message_id');
        return $this->sendRequest('forwardMessage', $params);
    }

    /**
     * Send Photos.
     *
     * @link https://core.telegram.org/bots/api#sendphoto
     *
     * @param int            $chat_id
     * @param string         $photo
     * @param string         $caption
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendPhoto($chat_id, $photo, $caption = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = compact('chat_id', 'photo', 'caption', 'reply_to_message_id', 'reply_markup');
        if(((!is_dir($photo)) && (filter_var($photo, FILTER_VALIDATE_URL) === false))) {
            return $this->sendRequest('sendPhoto', $data);
        }
        return $this->uploadFile('sendPhoto', $data);
    }

    /**
     * Send Audio.
     *
     * @link https://core.telegram.org/bots/api#sendaudio
     *
     * @param int            $chat_id
     * @param string         $audio
     * @param int            $duration
     * @param string         $performer
     * @param string         $title
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendAudio($chat_id, $audio, $duration = null, $performer = null, $title = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = compact('chat_id', 'audio', 'duration', 'performer', 'title', 'reply_to_message_id', 'reply_markup');
        if(((!is_dir($audio)) && (filter_var($audio, FILTER_VALIDATE_URL) === false))) {
            return $this->sendRequest('sendAudio', $data);
        }
        return $this->uploadFile('sendAudio', $data);
    }

    /**
     * Send Document.
     *
     * @link https://core.telegram.org/bots/api#senddocument
     *
     * @param int            $chat_id
     * @param string         $document
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendDocument($chat_id, $document, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = compact('chat_id', 'document', 'reply_to_message_id', 'reply_markup');
        if(((!is_dir($document)) && (filter_var($document, FILTER_VALIDATE_URL) === false))) {
            return $this->sendRequest('sendDocument', $data);
        }
        return $this->uploadFile('sendDocument', $data);
    }

    /**
     * Send Sticker.
     *
     * @link https://core.telegram.org/bots/api#sendsticker
     *
     * @param int            $chat_id
     * @param string         $sticker
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendSticker($chat_id, $sticker, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = compact('chat_id', 'sticker', 'reply_to_message_id', 'reply_markup');
        if(((!is_dir($sticker)) && (filter_var($sticker, FILTER_VALIDATE_URL) === false))) {
            return $this->sendRequest('sendSticker', $data);
        }
        return $this->uploadFile('sendSticker', $data);
    }

    /**
     * Send Video.
     *
     * @link https://core.telegram.org/bots/api#sendvideo
     *
     * @param int            $chat_id
     * @param string         $video
     * @param int            $duration
     * @param string         $caption
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendVideo($chat_id, $video, $duration = null, $caption = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = compact('chat_id', 'video', 'duration', 'caption', 'reply_to_message_id', 'reply_markup');

        if(((!is_dir($video)) && (filter_var($video, FILTER_VALIDATE_URL) === false))) {
            return $this->sendRequest('sendVideo', $data);
        }
        return $this->uploadFile('sendVideo', $data);
    }

    /**
     * Send Voice.
     *
     * @link https://core.telegram.org/bots/api#sendvoice
     *
     * @param int            $chat_id
     * @param string         $audio
     * @param int            $duration
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendVoice($chat_id, $audio, $duration = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = compact('chat_id', 'audio', 'duration', 'reply_to_message_id', 'reply_markup');
        if(((!is_dir($audio)) && (filter_var($audio, FILTER_VALIDATE_URL) === false))) {
            return $this->sendRequest('sendVoice', $data);
        }
        return $this->uploadFile('sendVoice', $data);
    }

    /**
     * Send Location.
     *
     * @link https://core.telegram.org/bots/api#sendlocation
     *
     * @param int            $chat_id
     * @param float          $latitude
     * @param float          $longitude
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendLocation($chat_id, $latitude, $longitude, $reply_to_message_id = null, $reply_markup = null)
    {
        $params = compact('chat_id', 'latitude', 'longitude', 'reply_to_message_id', 'reply_markup');
        return $this->sendRequest('sendLocation', $params);
    }

    /**
     * Send Venue.
     *
     * @link https://core.telegram.org/bots/api#sendvenue
     *
     * @param int            $chat_id
     * @param float          $latitude
     * @param float          $longitude
     * @param string         $title
     * @param string         $address
     * @param string         $foursquare_id
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendVenue($chat_id, $latitude, $longitude, $title, $address, $foursquare_id = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $params = compact('chat_id', 'latitude', 'longitude', 'title', 'address', 'foursquare_id', 'reply_to_message_id', 'reply_markup');
        return $this->sendRequest('sendVenue', $params);
    }

    /**
     * Send Contact.
     *
     * @link https://core.telegram.org/bots/api#sendcontact
     *
     * @param int            $chat_id
     * @param string         $phonenumber
     * @param string         $first_name
     * @param string         $last_name
     * @param bool           $disable_notification
     * @param int            $reply_to_message_id
     * @param KeyboardMarkup $reply_markup
     *
     * @return Array
     */
    public function sendContact($chat_id, $phone_number, $first_name, $last_name = null, $reply_to_message_id = null, $reply_markup = null)
    {
        $params = compact('chat_id', 'phone_number', 'first_name', 'last_name', 'reply_to_message_id', 'reply_markup');
        return $this->sendRequest('sendContact', $params);
    }

    /**
     * Send Chat Action.
     *
     * @link https://core.telegram.org/bots/api#sendchataction
     *
     * @param int    $chat_id
     * @param string $action
     *
     * @return Array
     */
    public function sendChatAction($chat_id, $action)
    {
        $actions = ['typing',
                    'upload_photo',
                    'record_video',
                    'upload_video',
                    'record_audio',
                    'upload_audio',
                    'upload_document',
                    'find_location',
        ];
        if(isset($action) && in_array($action, $actions)) {
            $params = compact('chat_id', 'action');
            return $this->sendRequest('sendChatAction', $params);
        }
        throw new TelegramException('Invalid Action! Accepted value: ' . implode(', ', $actions));
    }

    /**
     * Get user profile photos.
     *
     * @link https://core.telegram.org/bots/api#getsserprofilephotos
     *
     * @param int $chat_id
     * @param int $offset
     * @param int $limit
     *
     * @return Array
     */
    public function getUserProfilePhotos($user_id, $offset = null, $limit = null)
    {
        $params = compact('user_id', 'offset', 'limit');
        return $this->sendRequest('getUserProfilePhotos', $params);
    }

    /**
     * Use this method to get basic info about a file and prepare it for downloading.
     *
     * @link https://core.telegram.org/bots/api#getfile
     *
     * @param String $file_id
     *
     * @return On success, a File object is returned
     */
    public function getFile($file_id)
    {
        return $this->sendRequest('getFile', compact('file_id'));
    }

    /**
     * Use this method for your bot to leave a group, supergroup or channel.
     * Returns True on success.
     *
     * @link https://core.telegram.org/bots/api#leavechat
     *
     * @param int|string id or username of the supergroup or channel
     *
     * @return True on success
     */
    public function leaveChat($chat_id)
    {
        $params = compact('chat_id');
        return $this->sendRequest('leaveChat', $params);
    }

    /**
     * Use this method to get up to date information about the chat.
     * Returns a Chat object on success.
     *
     * @link https://core.telegram.org/bots/api#getchat
     *
     * @param int|string id or username of the supergroup or channel
     *
     * @return Chat object
     */
    public function getChat($chat_id)
    {
        $params = compact('chat_id');
        return $this->sendRequest('getChat', $params);
    }

    /**
     * Use this method to get a list of administrators in a chat.
     * Returns an Array of ChatMember objects on success
     *
     * @link https://core.telegram.org/bots/api#getchatadministrators
     *
     * @param int|string id or username of the supergroup or channel
     *
     * @return Array of ChatMember objects
     */
    public function getChatAdministrators($chat_id)
    {
        $params = compact('chat_id');
        return $this->sendRequest('getChatAdministrators', $params);
    }

    /**
     * Use this method to get the number of members in a chat.
     * Returns Int on success.
     *
     * @link https://core.telegram.org/bots/api#getchatmemberscount
     *
     * @param int|string id or username of the supergroup or channel
     *
     * @return int amount of chat members
     */
    public function getChatMembersCount($chat_id)
    {
        $params = compact('chat_id');
        return $this->sendRequest('getChatMembersCount', $params);
    }

    /**
     * Use this method to get information about a member of a chat.
     * Returns a ChatMember object on success.
     *
     * @link https://core.telegram.org/bots/api#getchatmember
     *
     * @param int|string id or username of the supergroup or channel
     * @param int        Unique identifier of the target user
     *
     * @return ChatMember object
     */
    public function getChatMember($chat_id, $user_id)
    {
        $params = compact('chat_id', 'user_id');
        return $this->sendRequest('getChatMember', $params);
    }

    /**
     * Use this method to get file Data.
     *
     * @link https://core.telegram.org/bots/api#getfile
     *
     * @see  getFile
     *
     * @param string $file_id
     * @param string $file_path Is taken from the getFile response
     *
     * @return On success, a File Data is returned
     */
    public function getFileData($file_id, $file_path)
    {
        try {
            return file_get_contents($this->baseFileURL . $file_path . '?' . http_build_query(compact('file_id')));
        } catch(Exception $exception) {
            print_r($exception);
        }
    }

    /**
     * Set a Webhook to receive incoming updates via an outgoing webhook.
     *
     * @param string    $url        HTTPS url to send updates to. Use an empty string to remove webhook integration
     * @param InputFile $cerificate Upload your public key certificate so that the root certificate in use can be
     *                              checked
     *
     * @return Array
     *
     */
    public function setWebhook($url, $certificate = null)
    {
        if(filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new TelegramException('Invalid URL provided');
        }
        if(parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw new TelegramException('Invalid URL, it should be a HTTPS url.');
        }
        if(is_null($certificate)) {
            return $this->sendRequest('setWebhook', compact('url', 'certificate'));
        } else {
            return $this->uploadFile('setWebhook', compact('url', 'certificate'));
        }
    }

    /**
     * Returns webhook updates sent by Telegram.
     * Works only if you set a webhook.
     *
     * @return Array
     * @see setWebhook
     *
     */
    public function getWebhookUpdates()
    {
        $body = json_decode(file_get_contents('php://input'), true);
        return $body;
    }

    /**
     * Builds a custom keyboard markup.
     *
     * @link https://core.telegram.org/bots/api#replykeyboardmarkup
     *
     * @param array $keyboard
     * @param bool  $resize_keyboard
     * @param bool  $one_time_keyboard
     * @param bool  $selective
     *
     * @return string
     */
    public function replyKeyboardMarkup($keyboard, $resize_keyboard = false, $one_time_keyboard = false, $selective = false)
    {
        return json_encode(compact('keyboard', 'resize_keyboard', 'one_time_keyboard', 'selective'));
    }

    /**
     * Hide the current custom keyboard and display the default letter-keyboard.
     *
     * @link https://core.telegram.org/bots/api#replykeyboardhide
     *
     * @param bool $selective
     *
     * @return string
     */
    public static function replyKeyboardHide($selective = false)
    {
        $hide_keyboard = true;
        return json_encode(compact('hide_keyboard', 'selective'));
    }

    /**
     * Display a reply interface to the user (act as if the user has selected the bots message and tapped 'Reply').
     *
     * @link https://core.telegram.org/bots/api#forcereply
     *
     * @param bool $selective
     *
     * @return string
     */
    public static function forceReply($selective = false)
    {
        $force_reply = true;
        return json_encode(compact('force_reply', 'selective'));
    }

    private function sendRequest($method, $params)
    {
        return json_decode(file_get_contents($this->baseURL . $method . '?' . http_build_query($params)), true);
    }

    private function uploadFile($method, $data)
    {
        $key = ['sendPhoto'    => 'photo',
                'sendAudio'    => 'audio',
                'sendDocument' => 'document',
                'sendSticker'  => 'sticker',
                'sendVideo'    => 'video',
                'setWebhook'   => 'certificate'
        ];
        if(filter_var($data[$key[$method]], FILTER_VALIDATE_URL)) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . mt_rand(0, 9999);
            $url = true;
            file_put_contents($file, file_get_contents($data[$key[$method]]));
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file);
            $extensions = ['image/jpeg' => '.jpg',
                           'image/png'  => '.png',
                           'image/gif'  => '.gif',
                           'image/bmp'  => '.bmp',
                           'image/tiff' => '.tif',
                           'audio/ogg'  => '.ogg',
                           'audio/mpeg' => '.mp3',
                           'video/mp4'  => '.mp4',
                           'image/webp' => '.webp'
            ];
            if($method != 'sendDocument') {
                if(!array_key_exists($mime_type, $extensions)) {
                    unlink($file);
                    throw new TelegramException('Bad file type/extension');
                }
            }
            $newFile = $file . $extensions[$mime_type];
            rename($file, $newFile);
            $data[$key[$method]] = new CurlFile($newFile, $mime_type, $newFile);
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $data[$key[$method]]);
            $data[$key[$method]] = new CurlFile($data[$key[$method]], $mime_type, $data[$key[$method]]);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseURL . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = json_decode(curl_exec($ch), true);
        if($url) {
            unlink($newFile);
        }
        return $response;
    }

}

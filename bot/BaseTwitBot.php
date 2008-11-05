<?php

class BaseTwitBot {
    
    protected $aOptions = null;
    
    public function __construct( $aOptions = array() ) {
        $this->aOptions = $aOptions;
    }
    
    /**
     * @return true if everything completed successfully
     **/
    public function run() {
        echo "bot ran - {$this->aOptions['username']}<br>";
    }
    
    public function getLastDataTime() {
        if ( !isset($this->aOptions['last_data_time']) ) {
            $this->aOptions['last_data_time'] = 0;
        }
        return $this->aOptions['last_data_time'];
    }
    
}

/*
$username = 'twitbotcom';
$password = 'sillypassword';
$fileName = 'last_checked.txt';

$last = file_exists( $fileName ) ? file_get_contents( $fileName ) : null;

$bot = new Twitter( $username, $password );
$messages = $bot->directMessages->direct_messages();
echo "<pre>";
print_r($messages);
echo "</pre>";
// now sort the messages so that the oldest is first
$messages = array_reverse( $messages );

foreach( $messages as $message ) {
  $sender = $message->sender->screen_name;
  $status = trim( $message->text );
  $last   = $message->created_at;
  
  $bot->status->update( "@$sender says \"$status\"" );
}

file_put_contents( $fileName, $last );
*/

?>
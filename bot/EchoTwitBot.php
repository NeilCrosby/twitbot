<?php

require_once("BaseTwitBot.php");

/**
 * A basic "Channel Bot".  Users direct message the bot, and the bot echos
 * that message out to eveyone that's following it.  Allows messages to 
 * easily be sent out to a subset of your friends on Twitter.
 *
 * @author Neil Crosby, neil@twitbot.com
 **/
class EchoTwitBot extends BaseTwitBot {
    
    public function __construct( $aOptions = array() ) {
        parent::__construct( $aOptions );
    }
    
    public function run() {
        $username = $this->aOptions['username'];
        $password = $this->aOptions['password'];
        $lastDataTimeAsStr = date("D M j H:i:s +0000 Y", $this->aOptions['last_data_time']);
        
        $bot = new Twitter( $username, $password );
        $messages = $bot->directMessages->direct_messages( array( 'since' => $lastDataTimeAsStr ) );

        if ( !is_array($messages) ) {
            return;
        }

        // Now sort the messages so that the oldest is first
        // We do this because we want messages to be echoed back out to 
        // Twitter in the same order as they were received.
        $messages = array_reverse( $messages );

        foreach( $messages as $message ) {
          $sender = $message->sender->screen_name;
          $status = trim( $message->text );
          $this->aOptions['last_data_time'] = strtotime($message->created_at);

//          $output = ( isset($this->aOptions['sender_is_private']) && 1 == $this->aOptions['sender_is_private'] )
//                  ? $status
//                  : "@$sender says \"$status\"";
          $output = ( 'kapowatch' == $username || 'neilisannoyedby' == $username )
                  ? $status
                  : "@$sender says \"$status\"";
//          $output = "@$sender says \"$status\"";
          
          $bot->status->update( $output );
        }
        
    }
    
}

?>
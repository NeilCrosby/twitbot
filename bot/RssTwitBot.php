<?php

require_once("BaseTwitBot.php");

class RssTwitBot extends BaseTwitBot {
    
    public function __construct( $aOptions = array() ) {
        parent::__construct( $aOptions );
    }
    
    public function run() {
        $feed = simplexml_load_file( $this->aOptions['url'] );
    
        if ( !$feed ) {
            return;
        }
        
        $feedItems = array();
        foreach ( $feed->channel->item as $item ) {
            array_push( $feedItems, $item );
        }
        
        // now sort the messages so that the oldest is first
        $feedItems = array_reverse( $feedItems );
        
        $username = $this->aOptions['username'];
        $password = $this->aOptions['password'];
        $bot = new Twitter( $username, $password );

        foreach( $feedItems as $item ) {
            $itemTime = strtotime($item->pubDate);
            
            if ( $itemTime <= $this->aOptions['last_data_time'] ) {
                continue;
            }

            $this->aOptions['last_data_time'] = $itemTime;

            echo "<p>{$item->title}: {$item->link}</p>";
            
            $bot->status->update( "{$item->title}: {$item->link}" );
        }

    }
    
}

?>
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
            
            $titleLength = mb_strlen($item->title);
            $urlLength = mb_strlen('http://tinyurl.com/6dvl5n'); // a short url created by twitter - TODO switch to bit.ly (it's shorter)
            $shortDescAllowedLength = 140 - $urlLength - $titleLength - 3;
            
            $shortDesc = html_entity_decode(strip_tags($item->description));
            
            if ( mb_strlen($shortDesc) > $shortDescAllowedLength ) {
                $shortDesc = mb_substr($shortDesc, 0, $shortDescAllowedLength - 3).'...';
            }
            
            $bot->status->update( "{$item->title}: $shortDesc {$item->link}" );
        }

    }
    
}

?>
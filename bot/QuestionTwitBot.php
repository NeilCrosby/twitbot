<?php

require_once("BaseTwitBot.php");

class QuestionTwitBot extends BaseTwitBot {
    
    public function __construct( $aOptions = array() ) {
        parent::__construct( $aOptions );
    }
    
    public function run() {
        $url    = $this->aOptions['url'];
        $method = $this->aOptions['method'];
        $params = $this->aOptions['params'];
        
        $username = $this->aOptions['username'];
        $password = $this->aOptions['password'];
        $lastDataTimeAsStr = date("D M j H:i:s +0000 Y", $this->aOptions['last_data_time']);
        
        $bot = new Twitter( $username, $password );
        $messages = $bot->directMessages->direct_messages( array( 'since' => $lastDataTimeAsStr ) );
//        $messages = $bot->status->user_timeline( array( 'since' => $lastDataTimeAsStr ) );

        if ( !is_array($messages) ) {
            echo "<p>No messages</p>";
            return;
        }

        // now sort the messages so that the oldest is first
        $messages = array_reverse( $messages );

//        echo "<pre>";
//        print_r($messages);
//        echo "</pre>";

        foreach( $messages as $message ) {
            echo "<pre>";
            print_r($message);
            echo "</pre>";
            $sender = $message->sender->screen_name;
            $status = trim( $message->text );
            $this->aOptions['last_data_time'] = strtotime($message->created_at);

            $thisParams = $params;
            $thisParams = str_replace( '!!TWITBOT_TEXT!!', urlencode($status), $thisParams );
            $thisParams = str_replace( '!!TWITBOT_USER!!', urlencode($sender), $thisParams );
            echo "<p>$thisParams</p>";

            $session = curl_init();

            curl_setopt( $session, CURLOPT_HEADER, false );
            curl_setopt( $session, CURLOPT_RETURNTRANSFER, 1 );    

            if ( 'POST' == $method ) {
                curl_setopt( $session, CURLOPT_POST, 1 );
                curl_setopt( $session, CURLOPT_POSTFIELDS, $thisParams );            
            } else {
                $url .= '?'.$thisParams;
            }

            curl_setopt( $session, CURLOPT_URL, $url );

            $result = curl_exec( $session );

            curl_close( $session );

			echo "<p>returned</p>";

            if ( !isset($result) ) {
//                echo "<p>No result</p>";
              continue;
            }

			echo "<pre>".print_r($result)."</pre>";

			if ( $json = json_decode($result) ) {
				if ( $json['error'] ) {
		            //$bot->directMessages->new_message( $json['error'] );
				}
			}
			
			echo "<p>still here</p>";
        
//            echo "<p>Result: $result</p>";
            //$bot->directMessages->new_message( $result );
        }
        
    }
    
}

?>
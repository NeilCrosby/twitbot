<?php

/** 
 * A PHP implementation of the Twitter API.  This implementation is based on
 * the documentation found at 
 * <http://groups.google.com/group/twitter-development-talk/web/api-documentation>
 * This implementation is _not_ fully tested, but it does definitely work
 * for the functionality required by the EchoTwitBot and RssTwitBot.
 *
 * @author Neil Crosby, neil@twitbot.com
 **/
class Twitter {
    
    const OK = 200;
    const NOT_MODIFIED = 304;
    const BAD_REQUEST = 400;
    const NOT_AUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INTERNAL_SERVER_ERROR = 500;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;    
    
    var $status = null;
    var $user = null;
    var $directMessages = null;
    var $friendship = null;
    var $account = null;
    var $favorite = null;
    var $notification = null;

    function __construct( $username=null, $password=null, $apiPath="http://twitter.com/" ) {
        if ( !$username || !$password || !$apiPath ) {
            return;
        }

        $this->status         = new TwitterStatus( $username, $password, $apiPath );
        $this->user           = new TwitterUser( $username, $password, $apiPath );
        $this->directMessages = new TwitterDirectMessages( $username, $password, $apiPath );
        $this->friendship     = new TwitterFriendship( $username, $password, $apiPath );
        $this->account        = new TwitterAccount( $username, $password, $apiPath );
        $this->favorite       = new TwitterFavorite( $username, $password, $apiPath );
        $this->notification   = new TwitterNotification( $username, $password, $apiPath );
    }
}

class TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        if ( !$username || !$password || !$apiPath ) {
          return;
        }
        
        $this->username = $username;
        $this->password = $password;
        $this->apiPath  = $apiPath;
    }
    
    protected function getAsQueryString( $array = array() ) {
        
        if ( !is_array($array) ) {
            return '';
        }

        $items = array();
        
        foreach ( $array as $key => $value ) {
            array_push( $items, urlencode( $key ).'='.urlencode( $value ) );
        }
        
        $return = implode( '&', $items );        
        return $return;
    }

    protected function curl( $url, $aOptions = array() ) {
      $session = curl_init();

      curl_setopt( $session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt( $session, CURLOPT_USERPWD, $this->username . ":" . $this->password);
      curl_setopt( $session, CURLOPT_URL, $this->apiPath . $url );
      curl_setopt( $session, CURLOPT_HEADER, false );
      curl_setopt( $session, CURLOPT_RETURNTRANSFER, 1 );    

      foreach ( $aOptions as $key => $value ) {
        curl_setopt( $session, $key, $value );
      }

      $result = curl_exec( $session );

      curl_close( $session );

      if ( !isset($result) ) {
        $result = false;
      }
      
      error_log($this->apiPath . $url);
      error_log("HTTP CODE: ".curl_getinfo($session, CURLINFO_HTTP_CODE));

      return $result;
    }
}

class TwitterStatus extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function public_timeline( $aOptions = array() ) {
        // since_id
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("statuses/public_timeline.json?$queryString");
        return json_decode($messages);
    }
    
    public function friends_timeline( $aOptions = array() ) {
        // id
        // since
        // page
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("statuses/friends_timeline.json?$queryString");
        return json_decode($messages);
    }
    
    public function user_timeline( $aOptions = array() ) {
        // id
        // count
        // since
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("statuses/user_timeline.json?$queryString");
        return json_decode($messages);
    }
    
    public function show( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("statuses/show/$id.json");
        return json_decode($messages);
    }
    
    public function update( $status ) {
        $status = urlencode( $status );

        $this->curl(
          "statuses/update.xml",
          array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "status=" . $status,
            CURLOPT_TIMEOUT => 1,
          )
        );
    }
    
    public function replies( $aOptions = array() ) {
        // page
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("statuses/replies.json?$queryString");
        return json_decode($messages);
    }
    
    public function destroy( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("statuses/destroy/$id.json");
        return json_decode($messages);
    }
    
}

class TwitterUser extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function friends( $aOptions = array() ) {
        // id
        // page
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("statuses/friends.json?$queryString");
        return json_decode($messages);
    }
    
    public function followers( $aOptions = array() ) {
        // lite
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("statuses/followers.json?$queryString");
        return json_decode($messages);
    }
    
    public function featured() {
        $messages = $this->curl("statuses/featured.json");
        return json_decode($messages);
    }
    
    public function show( $id, $aOptions = array() ) {
        // email
        $id = urlencode($id);
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("users/show/$id.json?$queryString");
        return json_decode($messages);
    }
    
}

class TwitterDirectMessages extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function direct_messages( $aOptions = array() ) {
        // since
        // since_id
        // page
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("direct_messages.json?$queryString");
        return json_decode($messages);
    }
    
    public function sent( $aOptions = array() ) {
        // since
        // since_id
        // page
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("direct_messages/sent.json?$queryString");
        return json_decode($messages);
    }
    
    public function new_message( $user, $text ) {
        $user = urlencode($user);
        $text = urlencode($text);
        echo "about to curl new message";
		$this->curl(
          "direct_messages/new.json",
          array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "user=$user&text=$text",
            CURLOPT_TIMEOUT => 1,
          )
        );
    }
    
    public function destroy( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("direct_messages/destroy/$id.json");
        return json_decode($messages);
    }
    
}

class TwitterFriendship extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function create( $id ) {
        $id = urlencode($id);
        $friend = $this->curl("friendships/create/$id.json");
        return json_decode($friend);
    }
    
    public function destroy( $id ) {
        $id = urlencode($id);
        $friend = $this->curl("friendships/destroy/$id.json");
        return json_decode($friend);
    }
    
}

class TwitterAccount extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function verify_credentials() {
        $messages = $this->curl("account/verify_creditials.json");
        return json_decode($messages);
        // TODO: need to do something a bit more special to see if this returned okay
    }
    
    public function end_session() {
        $this->curl("account/end_session");
    }
    
    public function archive( $aOptions = array() ) {
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("account/archive.json?$queryString");
        return json_decode($messages);
    }
    
}

class TwitterFavorite extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function favorites( $aOptions = array() ) {
        // The API definition of this function is weird=ass
        
        // id
        // page
        $user = '';
        if ( isset( $aOptions['id'] ) ) {
            $user = '/'.urlencode($aOptions['id']);
            $aOptions['id'] = null;
        }
        
        $queryString = $this->getAsQueryString( $aOptions );
        $messages = $this->curl("favorites$user.json?$queryString");
        return json_decode($messages);
        
    }
    
    public function create( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("favorites/create/$id.json");
        return json_decode($messages);
    }
    
    public function destroy( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("favourites/destroy/$id.json");
        return json_decode($messages);
    }
    
}

class TwitterNotification extends TwitterBase {
    function __construct( $username=null, $password=null, $apiPath=null ) {
        parent::__construct( $username, $password, $apiPath );
    }

    public function follow( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("notifications/follow/$id.json");
        return json_decode($messages);
    }
    
    public function leave( $id ) {
        $id = urlencode($id);
        $messages = $this->curl("notifications/leave/$id.json");
        return json_decode($messages);
    }
    
}

?>
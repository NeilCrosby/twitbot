<?php

function __autoload($class) {
    $aLocations = array('classes', 'api', 'bot');
    
    foreach( $aLocations as $location ) {
        $file = "$location/$class.php";
        if ( file_exists( $file ) ) {
            include_once( $file );
            return;
        }
    }

    // Check to see if we managed to declare the class
    if (!class_exists($class, false)) {
        trigger_error("Unable to load class: $class", E_USER_WARNING);
    }
}


$aValidTypes = array(
    'echo' => array('class' => 'EchoTwitBot', 'table' => 'tbl_bot_echo'), 
    'rss' => array('class' => 'RssTwitBot', 'table' => 'tbl_bot_rss'), 
    'question' => array('class' => 'QuestionTwitBot', 'table' => 'tbl_bot_question'),
    'werewolf' => array('class' => 'WerewolfTwitBot', 'table' => 'tbl_bot_werewolf'),
    'friendly' => array('class' => 'FriendlyTwitBot', 'table' => 'tbl_bot_friendly'),
);

if ( !isset($_GET['type']) || !array_key_exists( $_GET['type'], $aValidTypes ) ) {
    echo "<p>Invalid type</p>";
    exit();
}

// Set up DB uname/pword, error reporting etc.
new Config();

$botType = $aValidTypes[$_GET['type']];
$botClass = $botType['class'];
$botTable = $botType['table'];

$safeBotTable = SQL::makeSafe($botTable);
$sql = "SELECT *
        FROM tbl_bot
        INNER JOIN $safeBotTable ON $safeBotTable.uid = tbl_bot.uid";
$aBots = SQL::doReadQuery($sql);

foreach ( $aBots as $aBotVars ) {
    $aBotVars['last_data_time'] = strtotime($aBotVars['last_data_time']);
    $lastDataTime = $aBotVars['last_data_time'];
    
    $bot = new $botClass( $aBotVars );
    $bot->run();
    $newLastDataTime = $bot->getLastDataTime();
    
    echo "<p>{$aBotVars['username']} $lastDataTime $newLastDataTime</p>";
    echo '<p>'.date("D M j H:i:s +0000 Y", $newLastDataTime).'</p>';
    
    if ( $newLastDataTime != $lastDataTime ) {
        $safeUid = SQL::makeSafe($aBotVars['uid']);
        $safeNewLastDataTime = SQL::makeSafe(date('Y-m-d H:i:s', $newLastDataTime));
        
        $sql = "UPDATE $safeBotTable
                SET last_data_time = '$safeNewLastDataTime'
                WHERE uid = '$safeUid'";
        SQL::doWriteQuery($sql);
    }
}

?>
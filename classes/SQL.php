<?php

class SQL {
  
  private static $readConnection = null;
  private static $writeConnection = null;
  const CONNECTION_ERROR = -1000;
  
  private static function getReadConnection() {
    if ( !isset(SQL::$readConnection) ) {
      SQL::$readConnection = mysql_connect(Config::DB_SERVER, Config::DB_USERNAME, Config::DB_PASSWORD);
      if ( !SQL::$readConnection || mysql_error(SQL::$readConnection) ) {
        self::$readConnection = self::CONNECTION_ERROR;
        return self::$readConnection;
      }

      mysql_select_db(Config::DB_NAME, SQL::$readConnection);
      mysql_query("SET NAMES utf8", SQL::$readConnection);
    }
    
    return SQL::$readConnection;
  }
  
  private static function getWriteConnection() {
    if ( !isset(SQL::$writeConnection) ) {
      SQL::$writeConnection = mysql_connect(Config::DB_SERVER, Config::DB_USERNAME, Config::DB_PASSWORD);
      if ( !self::$readConnection || mysql_error(self::$writeConnection) ) {
        self::$writeConnection = self::CONNECTION_ERROR;
        return self::$writeConnection;
      }

      mysql_select_db(Config::DB_NAME, SQL::$writeConnection);
      mysql_query("SET NAMES utf8", SQL::$writeConnection);
    }
    
    return SQL::$writeConnection;
  }
  
  public static function getCallingFunction() {
    $trace = debug_backtrace();
    
    foreach ( $trace as $item ) {
      if ( 'SQL' != $item['class'] ) {
        return $item['class'].$item['type'].$item['function'];
      }
    }
    
    return '';
  }
  
  public static function doReadQuery($sql, $cacheTime=60) {
    $cacheExists = class_exists('PhpCache');
    $logExists = class_exists('Log');
    
    $callingFunc = self::getCallingFunction();
    $sql = "-- $callingFunc;\n".$sql;
    
    if ( $cacheExists ) {
        $cache = new PhpCache($sql, $cacheTime);
        if ( $cacheTime && $cache->check() ) {
          $cached = $cache->get();
          return $cached['data'];
        }
    }
    
    $conn = SQL::getReadConnection();
    if ( self::CONNECTION_ERROR == $conn ) {
        return null;
    }
    
    $startTime = microtime( true );
    $result = mysql_query($sql, $conn);
    $endTime = microtime( true );
    $totalTime = $endTime - $startTime;
    
    if ( $logExists && $totalTime > 1 ) {
      Log::logSQL( SQL::getCallingFunction(), $endTime - $startTime, $sql );
    }
   
    if ( $logExists && (!$result || mysql_error($conn)) ) {
      Log::error("SQL performed: $sql\n\nError: ".mysql_error($conn));
      return null;
    }
    
    $rows = array();
    while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
      array_push( $rows, $row );
    }
    
    if ( $cacheExists ) {
        $cached = array();
        $cached['function'] = $callingFunc;
        $cached['data'] = $rows;
    
        $cache->set($cached);
    }
    
    return $rows;
  }
  
  public static function doWriteQuery($sql) {
    $conn = SQL::getWriteConnection();
    if ( self::CONNECTION_ERROR == $conn ) {
        return null;
    }
        
    $result = mysql_query($sql, $conn);
   
    return $result;
  }
  
  public static function getLastInsertId() {
    $conn = SQL::getWriteConnection();
    return mysql_insert_id($conn);
  }
  
  public static function makeSafe($val) {
    $conn = SQL::getReadConnection();
    if ( self::CONNECTION_ERROR == $conn ) {
        return null;
    }
        
    return mysql_real_escape_string($val, $conn);
  }
  
}

?>
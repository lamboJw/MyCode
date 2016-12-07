<?php
/**
 *
 */
class Conn{
    public function redis($db=0) {
        $redis = new Redis ();
        $result = $redis->connect ( '127.0.0.1', 6379 );
        if ($result) {
            $redis->auth ( '' );
            //return $redis;
        } else {
            log_message ( 'error', "connect local redis error!" );
        }
        $redis->select($db);
        return $redis;
    }
}
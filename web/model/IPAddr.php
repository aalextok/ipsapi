<?php

/**
 * Description of IPAddr
 *
 * @author aalextok@gmail.com
 */
class IPAddr {
    var $id;
    var $ip;
    var $comment;
    
    public function IPAddr(){
    }
    public static function constructByIP($ip) {
        $ipAddr = new IPAddr();
        $ipAddr->ip = $ip;
        return $ipAddr;
    }   
    public static function  constructByAll($id, $ip, $comment){
        $ipAddr = new IPAddr();  
        $ipAddr->id = $id;
        $ipAddr->ip = $ip;
        $ipAddr->comment = $comment;
        return $ipAddr;
    }
    public static function constructByJson($json){
        $ipAddr = new IPAddr();
        $jd = json_decode($json);
        foreach ($ipAddr as $key => $val){
            $ipAddr->{$key} = $jd->{$key};
        }
        return $ipAddr;
    }
}

?>

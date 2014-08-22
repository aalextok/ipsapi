<?php

/**
 * Description of IPAddrList
 *
 * @author aalextok@gmail.com
 */
Composer\Autoload\includeFile('../config.php');
Composer\Autoload\includeFile('IPAddr.php');

class IPAddrList {
    var $currentList; 
    var $dbList;
    
    public function IPAddrList(){
        $this->getCurrentIPList();
        $this->getIPListFromDB();
    }
    /**
	* Gets all IPs from DB, 
        * converts them to an array of IPAddr objects,
        * sets $dbList property of $this as array of objects or FALSE in case of DB error
	* @return void
     */
    public function getIPListFromDB(){
        $dbList = array();
        try{
            global $connection; 
            global $connectionParams;
            $sql = 'SELECT * FROM '.$connectionParams['tbl_name'];
            $res = $connection->query($sql);
            
            while ($row = $res->fetch()){
                $dbList[] = IPAddr::constructByAll($row['id'], $row['ip'], $row['comment']);
            }
        }
        catch (Exception $e){
            $this->dbList = false;
        }
        $this->dbList = $dbList;
    }
    /**
	* Gets all IPs of current host, 
        * converts them to an array of IPAddr objects,
        * sets $currentList property of $this as array of objects or FALSE in case of error
	* @return void
     */
    public function getCurrentIPList(){
        $ipsArr = gethostbynamel(gethostname());
        if($ipsArr === false)
            $this->currentList = false;
        else{
            $ipsObjArr = array();
            for($i = 0; $i < sizeof($ipsArr); $i++){
                $ipsObjArr[$i] = IPAddr::constructByIP($ipsArr[$i]);
            }
        }
        $this->currentList = $ipsObjArr;    
    }
    
    /**
	* Inserts to DB all IPs of current host ($currentList), 
        * that had not yet been recorded. 
        * Returns number of inserted records (0 if nothing to insert) or FALSE in case of DB error
	* @return int|false
     */
    public function insertIPsToDB(){
        if($this->currentList === false || $this->dbList === false)
            return false;
        $unsavedIPs = array_udiff($this->currentList, $this->dbList, function($ip1, $ip2){
            return $ip1->ip - $ip2->ip;
        });
        if(sizeof($unsavedIPs) === 0)
            return 0;
        global $connection;
        global $connectionParams;
        $count;
        foreach($unsavedIPs as $uip){
            try{
                $arr = (array)$uip;
                $count += $connection->insert($connectionParams['tbl_name'], (array)$uip);
            }
            catch(Exception $e){
            }
        }
        if(!isset($count))
            return false;
        
        return $count;
    }
    /**
	* Deletes from DB all IPs, 
        * that do not correspond to the current host anymore. 
        * Returns number of deleted records (0 if nothing to delete) or FALSE in case of DB error
	* @return int|false
     */
    public function deleteRedundantIPsFromDB(){
        if($this->currentList === false || $this->dbList === false)
            return false;
        $redundantIPs = array_udiff($this->dbList, $this->currentList, function($ip1, $ip2){
            return $ip1->ip - $ip2->ip;
        });
        if(sizeof($redundantIPs) === 0)
            return 0;
        global $connection;
        global $connectionParams;
        $count;
        foreach($redundantIPs as $rip){
            try{
                $count += $connection->delete($connectionParams['tbl_name'], array('id' => $rip->id));
            }
            catch(Exception $e){
            }
        }
        if(!isset($count))
            return false;
        
        return $count;
    }
    // not used 
    public static function isException($obj){
        return in_array('Exception', class_parents($obj));
    }
    /**
	* Updates 'comment' cell of IP record in DB.
        * @param IPAddr $ipAddr - takes object of IPAddr class
        * Returns true if row updated or false in case of DB error.
	* @return boolean
     */
    public static function updateIPAddr($ipAddr){
        global $connection;
        global $connectionParams;
        try{
            $res = $connection->update($connectionParams['tbl_name'], array('comment' => $ipAddr->comment), array('id' => $ipAddr->id));
        }
        catch(Exception $e){
            return false;
        }
        return (boolean)$res;
    }
}

?>

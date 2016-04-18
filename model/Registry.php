<?php
class Registry {
    private $memCache;
    private $Installed;
    private $winCache = array();

    public function __construct(){
        if($this->Installed = class_exists("Memcache")) {
            $this->memCache = new Memcache();
            $this->memCache->addserver("127.0.0.1", 11211);
        }
        //else = Windows support :(
    }

    public function set($key,$value,$expire = 0){
        if($this->Installed){
            return $this->memCache->set("stats_".$key,$value,false,$expire);
        }else{
            $this->winCache["stats_".$key] = $value;
            return true;
        }
    }

    public function get($key){
        if($this->Installed){
            return $this->memCache->get("stats_".$key);
        }else{
            return $this->winCache["stats_".$key];
        }
    }

    public function getAddon($Addon,$key){
        if($this->Installed) {
            return $this->memCache->get($Addon . "_" . $key);
        }else{
            return false;
        }
    }
}
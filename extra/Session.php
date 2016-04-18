<?php

/**
 * Class Session
 *
 * Session
 */
Class Session{
    private $GroupID;
    private $UserID;
    private $Level;
    private $Username;

    private $xfUser;
    private $xfArray;

    /**
     * Class Construction
     */
    public function __construct(){
        /** @noinspection PhpIncludeInspection */
        require("../forum.justforfun-gaming.com/library/XenForo/Autoloader.php");
        XenForo_Autoloader::getInstance()->setupAutoloader("../forum.justforfun-gaming.com/library");
        XenForo_Application::initialize("../forum.justforfun-gaming.com/library", "../forum.justforfun-gaming.com");
        XenForo_Session::startPublicSession();
        $this->xfUser = XenForo_Visitor::getInstance();
        $this->xfArray = $this->xfUser->toArray();
        if((bool)$this->xfUser->getUserId()){
            $this->UserID = $this->xfUser->getUserId();
            $this->Username = $this->xfArray["username"];
            $this->GroupID = $this->xfArray["user_group_id"];
            $this->Level = $this->getLevelid($this->UserID);
            $this->setSession();
        }else{
            $this->UserID = 0;
            $this->Username = "Guest";
            $this->GroupID = 0;
            $this->Level = -1;
        }
    }

    /**
     * Get Username
     * @param $UserID int UserID
     * @return mixed String Username
     */
    public function getUsername($UserID = 0){
        if($UserID == 0) {
            return $this->Username;
        }else{
            return $this->getUserData($UserID)["username"];
        }
    }

    /**
     * Get Group ID
     * @param $UserID int UserID
     * @return mixed int GroupID
     */
    public function getUsergroupId($UserID){
        return $this->getUserData($UserID)["user_group_id"];
    }

    /**
     * Get UserData from database
     * @param $UserID int UserID
     * @return array|string Array Data
     */
    private function getUserData($UserID){
        global $MySQL,$Cache;

        $UserData = $Cache->get("UserData_".$UserID);
        if($UserData["username"] == ""){
            $Data = $MySQL->select("xenforum","xf_user",array("username","user_group_id"),"WHERE `user_id` = ?",array($UserID));
            $Cache->set("UserData_".$UserID,$Data,86400);

            return $Data;
        }

        return $UserData;
    }

    /**
     * Get Level from UserID
     * @param $UserID int UserID
     * @return int int Level
     */
    public function getLevelid($UserID = 0){
        if($UserID == 0){
            return $this->Level;
        }else {
            return $this->getLevel($this->getUsergroupId($UserID));
        }
    }

    /**
     * Get Level from GroupID
     * @param $GroupID int GroupID
     * @return int int Level
     */
    private function getLevel($GroupID){
        switch($GroupID){
            case 8:
                return 1;
            case 12:
                return 2;
            case 11:
                return 3;
            case 6:
                return 4;
            case 10:
                return 5;
            case 7:
                return 6;
            case 2:
                return 0;
            default:
                return -1;
        }
    }

    /**
     * Get Colored Name
     * @param $UserID int UserID
     * @return mixed String HTML Colored text
     */
    public function getColoredName($UserID){
        $Username = $this->getUsername($UserID);
        $Level = $this->getLevelid($UserID);

        switch($Level){
            case 1:
                $styleLvl = "<span style='color: #FF6633;'><b>{username}</b></span>";
                break;
            case 2:
                $styleLvl = "<span style='color: #55CCCC;'><b>{username}</b></span>";
                break;
            case 3:
                $styleLvl = "<span style='color: #4D89F0;'><b>{username}</b></span>";
                break;
            case 4:
                $styleLvl = "<span style='color: #33FF00;'><b>{username}</b></span>";
                break;
            case 5:
                $styleLvl = "<span style='color: #D4A017;'><b>{username}</b></span>";
                break;
            case 6:
                $styleLvl = "<span style='color: #FF0000;'><b>{username}</b></span>";
                break;
            default:
                $styleLvl = "{username}";
                break;
        }

        return str_replace("{username}",$Username,$styleLvl);
    }

    /**
     * Get Salt
     * @return string Salt
     */
    public function getSalt(){
        return $this->getSaltUid($this->UserID);
    }

    /**
     * Get Salt from UserID
     * @param $UserID Int UserID
     * @return string Salt
     */
    public function getSaltUid($UserID){
        global $MySQL;

        $data = $MySQL->select("master","jff2_users",array("salt"),"WHERE `user_id` = ?",array($UserID));
        if($data[0]["salt"] == "" || $data[0]["salt"] == "NULL" ){
            $salt = substr(md5(rand().microtime()), 0, 6);
            $MySQL->update("master","jff2_users",array("salt"),array($salt),"WHERE `user_id` = ?",array($UserID));
            return $salt;
        }else{
            return $data["salt"];
        }
    }

    /**
     * Get UserID
     * @return int UserID
     */
    public function getUserID(){
        return $this->UserID;
    }
}
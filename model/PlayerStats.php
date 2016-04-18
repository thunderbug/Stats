<?php
include_once 'TableBuilder.php';
include_once 'TimeConverter.php';

class PlayerStats{
	
	public $sql;
	public $time;
	public $parser;
	
	function __construct($game){
		$this->sql = new MySQL($game);
		$this->time= new TimeConverter();
	}
	
	public function getPlayerStats($player){
		if(!is_numeric($player))
			return false;
		
		$data = $this->getPlayerInfo($player);
		$res = $data[0];
		$wpn = $this->getPlayerWeapon($player);
		
		$res['kd_ratio'] = number_format((float)$res['kd_ratio'], 2, '.', '');
		$res['last_seen'] = $this->time->getDateFormat($res['last_seen']);
		$res['time'] = $this->time->getHour($res['time'], false, false);
		$res['flag'] = $this->getPlayerFlag($res['ip']);
		$res['country_name'] = $this->getPlayerCountry($res['ip']);
		$res['weapon'] = $wpn;
		$res['hits'] = $this->getPlayerHits($player);
		
		return $res;
	}
	
	public function getPlayerInfo($id){
		$res = $this->sql->select("	SELECT name_id, name, kills, deaths, suicides, score, time, last_seen,(kills/deaths) as kd_ratio, ip, connections 
									FROM stats_name 
									WHERE name_id=".$id."");
		return $res;
	}
	
	public function getPlayerFlag($ip){
		if(function_exists("geoip_country_code_by_name") && $ip != "")
			return strtolower(geoip_country_code_by_name($ip));
		else
			return "";
	}
	
	public function getPlayerCountry($ip){
		if(function_exists("geoip_country_name_by_name") && $ip != "")
			return geoip_country_name_by_name($ip);
		else
			return "";
	}
	
	public function getPlayerWeapon($id){
		$data = $this->sql->select("SELECT w.weapon_name, w.weapon_nicename, p.kills, p.deaths, p.suicides
									FROM stats_playerweapons p, stats_weaponstats w 
									WHERE w.weapon_id=p.weapon_id and p.name_id=".$id." 
									ORDER BY p.kills DESC
									LIMIT 1");
		if ($data)
			return $data[0];
		else
			return false;
	}
	
	public function getPlayerHits($id){
		if(!is_numeric($id))
			return json_encode(false);
		
		$data = $this->sql->select("SELECT distinct b.body_name, b.body_id, h.kills 
									FROM stats_body b LEFT JOIN stats_bodyhits h ON ( b.body_id = h.bodypart_id and h.name_id=".$id.")
									ORDER BY b.body_id");
		//print_r($data);
		$res = array(
			'head'  => $data[0]['kills'] + $data[3]['kills'],
			'torso' => $data[1]['kills'] + $data[2]['kills'],
			'left_arm' => $data[8]['kills'] + $data[12]['kills'] + $data[15]['kills'],
			'right_arm'=> $data[4]['kills'] + $data[6]['kills'] + $data[10]['kills'],
			'left_leg' => $data[11]['kills'] + $data[7]['kills'] + $data[13]['kills'],
			'right_leg'=> $data[14]['kills'] + $data[9]['kills'] + $data[5]['kills']	
		);
		
		return json_encode($res);
	}

    public function getForumID($id){
        if(!is_numeric($id))
            return json_encode(false);

        $data = $this->sql->select("SELECT ForumID FROM  stats_name WHERE name_id = ".$id."");
        if(is_numeric($data[0]["ForumID"])) {
            return $data[0]["ForumID"];
        }else{
            return false;
        }
    }

    public function getOpponents($id,$Kills){
        if(!is_numeric($id))
            return json_encode(false);

        if($Kills) {
            $data = $this->sql->select("SELECT stats_opponents.*,
            (SELECT name FROM stats_name WHERE stats_name.name_id = stats_opponents.name_id_1) as name_1,
            (SELECT name FROM stats_name WHERE stats_name.name_id = stats_opponents.name_id_2) as name_2
            FROM `stats_opponents` WHERE (name_id_1  = " . $id . " AND kills_1 > 0) OR (name_id_2  = " . $id . " AND kills_2 > 0) ");
        }else{
            $data = $this->sql->select("SELECT stats_opponents.*,
            (SELECT name FROM stats_name WHERE stats_name.name_id = stats_opponents.name_id_1) as name_1,
            (SELECT name FROM stats_name WHERE stats_name.name_id = stats_opponents.name_id_2) as name_2
            FROM `stats_opponents` WHERE (name_id_1  = " . $id . " AND kills_1 < 0) OR (name_id_2  = " . $id . " AND kills_2 < 0) ");
        }

        $res = array();

        foreach($data as $row){
            if($row["name_id_2"] == $id){
                $res[] = array(
                    "DeathsID" => $row["name_id_1"],
                    "DeathsA" => $row["kills_1"],
                    "Name" => $row["name_1"]
                );
            }else{
                $res[] = array(
                    "DeathsID" => $row["name_id_2"],
                    "DeathsA" => $row["kills_2"],
                    "Name" => $row["name_2"]
                );
            }
        }

        $res = $this->sortOpponents($res);

        return $res;
    }

    private function sortOpponents($res){
        $Changed = true;

        while($Changed){
            $Changed = false;

            for($i = 0; $i < count($res) - 1; $i++){
                $a = $i + 1;
                if($res[$i]["DeathsA"] < $res[$a]["DeathsA"]){
                    $tValue1 = $res[$i];
                    $tValue2 = $res[$a];

                    $res[$i] = $tValue2;
                    $res[$a] = $tValue1;

                    $Changed = true;
                }
            }
        }

        $data = array();

        for($i = 0; $i < count($res); $i++){
            $data[$i] = $res[$i];

            if($i == 10){
                $i = count($res);
            }
        }

        return $data;
    }
}
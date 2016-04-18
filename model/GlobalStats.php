<?php
include_once 'MySQL.php';
include_once 'TableBuilder.php';
include_once 'TimeConverter.php';

class GlobalStats {
	public $sql;
	public $builder;
	public $converter;
	public $parser;
	public static $cache;
	
	public function __construct($game){
		$this->sql = new MySQL($game);
		$this->builder = new TableBuilder();
		$this->converter = new TimeConverter();
	}
	
	public function getPlayersDefault(){
		return $this->getPlayers("name", "ASC", "", 100, 1, false);
	}
	
	public function getPlayers($order, $dir, $search, $limit, $page, $ajax){
		$search =$this->sql->escape($search);
		$order = $this->sql->escape($order);
		$dir =  $this->sql->escape($dir);
		if (!is_numeric($limit) || !is_numeric($page))
			return json_encode(false);
		$start = $limit*($page-1);
		
		$count = $this->sql->select(" SELECT COUNT(*) as total FROM stats_name WHERE name LIKE '%".$search."%'");
		$pagenum = $count[0]["total"];
	
        if($search == "") {
            $res = $this->sql->select("	SELECT name_id, name, kills, deaths, suicides, (kills/deaths) as kd_ratio, score, time, connections, last_seen, ip
									FROM stats_name
									WHERE last_seen > DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
									ORDER BY " . $order . " " . $dir . "
									LIMIT ".$start.", ".$limit);
        }else{
            $res = $this->sql->select("	SELECT name_id, name, kills, deaths, suicides, (kills/deaths) as kd_ratio, score, time, connections, last_seen, ip
									FROM stats_name
									WHERE name LIKE '%" . $search . "%'
									ORDER BY " . $order . " " . $dir . "
									LIMIT ".$start.", ".$limit);
        }
		
		foreach($res as $key=>$value){
			$res[$key]['kd_ratio'] = number_format((float)$res[$key]['kd_ratio'], 2, '.', '');
			$res[$key]['time'] = $this->converter->getHour($res[$key]['time'], true, true);
			$res[$key]['last_seen'] = $this->converter->getDate($res[$key]['last_seen']);
            if(function_exists("geoip_country_code_by_name"))
                $res[$key]['Flag'] = strtolower(@geoip_country_code_by_name($res[$key]["ip"]));
		}
		
		if ($res){
			$res['total'] = $pagenum;
			$res['pages'] = round($pagenum/$limit);
			if (!$ajax)
				return $res;
			else
				return json_encode($res);
		}
		return json_encode(false);
	}
	
	public function getServers(){
		$res = $this->sql->select("	SELECT server_id, ip, port, kills, deaths, suicides, hs
									FROM stats_servers 
									ORDER BY ip");
		if ($res){
			return $res;
		}
		return false;
	}
	
	public function getWeapons(){
		$res = $this->sql->select("	SELECT weapon_id, weapon_name, weapon_nicename, kills, suicides 
									FROM stats_weaponstats 
									ORDER BY kills DESC");
		if ($res){
			return $res;
		}
		return false;
	}
	
	public function getMaps(){
		$res = $this->sql->select("	SELECT map_id, map_name, rounds, kills, suicides
									FROM stats_map 
									ORDER BY map_name");
		if ($res){
			$new_res = array();
			foreach ($res as $map) {
				$location = 'css/images/maps/' . $map['map_name'] . '.jpg';
				if (file_exists($location)) {
					$map['image'] = $location;
				} else {
					$map['image'] = 'css/images/not_found.png';
				}
				$new_res[] = $map;
			}
			return $new_res;
		}
		return false;
	}
}
?>
<?php
require_once "model/View.php";
require_once "model/GlobalStats.php";
require_once "model/PlayerStats.php";

class ControllerWAW {
	public $section;
	public $title;
	public $global;
	public $player;
    public $game;
	
	public function __construct(){
		$this->section = "World at War";
		$this->title = "World at War";

        $this->game = "waw";

		$this->global = new GlobalStats($this->game);
		$this->player = new PlayerStats($this->game);
	}
	
	public function invoke(){
		if (!isset($_GET['action'])){
			require_once "model/pages/Players.php";
            new Players($this->title);
		}
		
		else {
			$action = $_GET['action'];
			
			//handle players' page request
			if ($action == "plr"){
				if(!isset($_GET['order'])){
					require_once "model/pages/Players.php";
                    new Players($this->title);
				}
				else{
					echo $this->global->getPlayers($_GET['order'], $_GET['sort'], $_GET['src'],$_GET['lim'], $_GET['page'], true);
				}
			}
			
			//handle servers' page request
			else if ($action =="srv"){
				require_once "model/pages/Servers.php";
                new Servers($this->title);
			}
			
			//handle weapons' page request
			else if ($action =="wpn"){
				require_once "model/pages/Weapons.php";
                new Weapons($this->title);
			}
			
			//handle maps' page request
			else if ($action =="map"){
				require_once "model/pages/Maps.php";
	            new Maps($this->title);
			}
			
			//handle user's page request
			else if($action == "usr"){
				require_once 'model/pages/User.php';
				new User($this->title);
			}
			
			else if($action == "graph"){
				echo $this->player->getPlayerHits($_GET['user']);
			}
		}
	}
}
?>
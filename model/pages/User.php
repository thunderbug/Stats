<?php
require_once 'model/PlayerStats.php';

class User extends View {
	protected function renderBody(){
		//Render Navbar first
		$this->renderNavbar();
	
		//Render rest of site
		$user = new PlayerStats($_GET["section"]);
		$res = $user->getPlayerStats($_GET['user']);

        $ForumID = $user->getForumID($_GET['user']);

        $BestKilled = false;
        $BestDeath = false;
        
        if($ForumID != false){
            $BestKilled = $user->getOpponents($_GET['user'], true);
            $BestDeath = $user->getOpponents($_GET['user'], false);
        }

	
		print($this->Twig->render("User.twig.html",
            array(
                "URL" => URL,
                "Player" => $res,
                "Game" => $_GET["section"],
                "ForumID" => $ForumID,
                "BestKilled" => $BestKilled,
                "BestDeath" => $BestDeath,
            )
        ));
	}
}
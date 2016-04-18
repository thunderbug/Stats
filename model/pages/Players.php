<?php
class Players extends View{
    protected function renderBody(){
        //Render Navbar first
        $this->renderNavbar();

        //Render rest of site
        $global = new GlobalStats($_GET["section"]);
        $res = $global->getPlayersDefault();

        print($this->Twig->render("Players.twig.html", array("Data" => $res, "URL" => URL, "Game" => $_GET["section"])));
    }
}
?>
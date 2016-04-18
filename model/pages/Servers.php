<?php
class Servers extends View{
    protected function renderBody(){
        //Render Navbar first
        $this->renderNavbar();

        //Render rest of site
        $global = new GlobalStats($_GET["section"]);
        $res = $global->getServers();

        print($this->Twig->render("Servers.twig.html", array("Data" => $res)));
    }
}
?>
<?php
class Maps extends View{
    protected function renderBody(){
//Render Navbar first
        $this->renderNavbar();

//Render rest of site
        $global = new GlobalStats($_GET["section"]);
        $res = $global->getMaps();

        print($this->Twig->render("Maps.twig.html", array("Data" => $res)));
    }
}
?>
<?php
class Weapons extends View{
    protected function renderBody(){
        //Render Navbar first
        $this->renderNavbar();

        //Render rest of site
        $global = new GlobalStats($_GET["section"]);
        $res = $global->getWeapons();

        print($this->Twig->render("Weapons.twig.html", array("Data" => $res)));
    }
}
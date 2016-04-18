<?php
class Home extends View{
    protected function renderBody(){
        print($this->Twig->render("Home.twig.html",array("URL" => URL)));
    }
}
?>
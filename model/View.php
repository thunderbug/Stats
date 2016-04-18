<?php
class View{
    protected $Loader;
    protected $Twig;

    protected $Title;

    public function __construct($Title){
        require_once("library/Twig/Autoloader.php");
        Twig_Autoloader::register();

        $this->Loader = new Twig_Loader_Filesystem("view");
        $this->Twig = new Twig_Environment($this->Loader, array(
            "debug" => true,
        ));

        $this->Title = $Title;

        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    protected function renderNavbar(){
        print($this->Twig->render("Navbar.twig.html", array("Title" => $this->Title, "URL" => URL, "Game" => $_GET["section"])));
    }

    private function renderHeader(){
        print($this->Twig->render("Header.twig.html", array("Title" => $this->Title, "URL" => URL, "Game" => $_GET["section"])));
    }

    private function renderFooter(){
        include("./build.php");
        print($this->Twig->render("Footer.twig.html", array("Year" => date("Y"), "Build" => $build, "Commit" => $commit)));
    }
}
?>
<?php
require_once("model/View.php");
require_once("ControllerMW1.php");
require_once("ControllerMW2.php");
require_once("ControllerUO.php");
require_once("ControllerCOD2.php");
require_once("ControllerWAW.php");
require_once("ControllerSignature.php");

class MainController {
	public $controllerMW2;
	public $controllerMW1;
	public $controllerWAW;
    public $controllerUO;
    public $controllerCOD2;
    public $controllerSignature;
	public $section;
	
	public function __construct(){
		$this->section = "";
	}
	
	public function invoke(){
		if (!isset($_GET['section'])){
			require_once("model/pages/Home.php");
            new Home("");
		}
		
		else {
			$section = $_GET['section'];
			if ($section == "mw1"){
				$this->controllerMW1 = new ControllerMW1();
				$this->controllerMW1->invoke();
			}
			else if ($section =="mw2"){
				$this->controllerMW2 = new ControllerMW2();
				$this->controllerMW2->invoke();
			}
            else if ($section =="uo"){
                $this->controllerUO = new ControllerUO();
                $this->controllerUO->invoke();
            }
            else if ($section =="cod2"){
                $this->controllerCOD2 = new ControllerCOD2();
                $this->controllerCOD2->invoke();
            }
            else if ($section =="waw"){
                $this->controllerWAW = new ControllerWAW();
                $this->controllerWAW->invoke();
            }
            else if ($section == "signature"){
                $this->controllerSignature = new ControllerSignature();
                $this->controllerSignature->invoke();
            }
		}
	}
}
?>
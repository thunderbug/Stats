<?php
require_once("view/Signature.php");

class ControllerSignature {
    public function __construct(){

    }

    public function invoke(){
        new Signature($_GET["game"],$_GET["user"]);
    }
}
<?php
require_once("model/PlayerStats.php");

class Signature {
    private $PlayerStats;

    public function __construct($Section,$UserID){
        $this->PlayerStats = new PlayerStats($_GET["game"]);
        $Stats = $this->PlayerStats->getPlayerStats($UserID);

        $image = imagecreatefrompng("css/images/sig/mw2/background.png");

        $imgX = imagesx($image);
        $imgY = imagesy($image);

        $GrayAlpha = imagecolorallocatealpha($image,0,0,0,70);
        $White = imagecolorallocate($image,255,255,255);

        //Draw Above gray text box
        imagefilledrectangle($image,0,0,$imgX,15,$GrayAlpha);

        //Draw Flag
        if($Stats["flag"] != ""){
            $country = imagecreatefrompng("https://cp.justforfun-gaming.com/template/jff/img/flags/".$Stats["flag"].".png");
            imagecopy($image, $country, 2, 3, 0, 0, 16, 11 );
            imagedestroy($country);
        }

        //Draw Name
        imagettftext($image, 11, 0, 20, 14, $White, "css/fonts/lucon.ttf", $Stats["name"]);

        //Draw Stats Background
        imagefilledrectangle($image,8,20,200,90,$GrayAlpha);

        //Draw Stats
        imagettftext($image, 10, 0, 30, 36, $White, "css/fonts/lucon.ttf", "Score: " . $Stats["score"]);
        imagettftext($image, 10, 0, 30, 48, $White, "css/fonts/lucon.ttf", "Kills: " . $Stats["kills"]);
        imagettftext($image, 10, 0, 30, 60, $White, "css/fonts/lucon.ttf", "Deaths: " . $Stats["deaths"]);
        imagettftext($image, 10, 0, 30, 72, $White, "css/fonts/lucon.ttf", "Suicides: " . $Stats["suicides"]);
        imagettftext($image, 10, 0, 30, 84, $White, "css/fonts/lucon.ttf", "Ratio: " . round($Stats["kills"] / ($Stats["deaths"] + $Stats["suicides"]),2));

        //Hosted By
        imagettftext($image, 11, 0, 260, 95, $White, "css/fonts/gunplay.ttf", "Hosted By JustForFun");

        header("Content-Type: image/png");
        imagepng($image);
        imagedestroy($image);
    }
}
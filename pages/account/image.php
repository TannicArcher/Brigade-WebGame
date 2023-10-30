<?php
if (isset($_GET['back']) && $_GET['back'] != 0) {
    $background = imagecreatefrompng("../../files/background/".$_GET['back'].".png");
    $background_sx = imagesy($background);
    $background_sy = imagesx($background);
} else {
    $background = imagecreatefrompng("../../files/background/1.png");
    $background_sx = imagesy($background);
    $background_sy = imagesx($background);
}
$player = imagecreatefrompng("../../files/dev/player.png");
$playerX = imagesx($player);
$playerY = imagesy($player);
imagecopy($background, $player, 0, 0, 0, 0, $playerX, $playerY);

if (isset($_GET['boot']) && is_numeric($_GET['boot']) && $_GET['boot'] != 0) {
    if (file_exists("../../files/dev/".$_GET['boot'].".png")){
        $boot = imagecreatefrompng("../../files/dev/".$_GET['boot'].".png");
        $bootX = imagesx($boot);
        $bootY = imagesy($boot);
        imagecopy($background, $boot, 0, 0 ,0, 0, $bootX, $bootY);
    }
}

if (isset($_GET['body']) && is_numeric($_GET['body']) && $_GET['body'] != 0) {
    if (file_exists("../../files/dev/".$_GET['body'].".png")){
        $body = imagecreatefrompng("../../files/dev/".$_GET['body'].".png");
        $bodyX = imagesx($body);
        $bodyY = imagesy($body);
        imagecopy($background, $body, 0, 0 ,0, 0, $bodyX, $bodyY);
    }
}

if (isset($_GET['top']) && is_numeric($_GET['top']) && $_GET['top'] != 0) {
    if (file_exists("../../files/dev/".$_GET['top'].".png")){
        $top = imagecreatefrompng("../../files/dev/".$_GET['top'].".png");
        $topX = imagesx($top);
        $topY = imagesy($top);
        imagecopy($background, $top, 0, 0 ,0, 0, $topX, $topY);
    }
}

if (isset($_GET['accessory']) && is_numeric($_GET['accessory']) && $_GET['accessory'] != 0) {
    if (file_exists("../../files/dev/".$_GET['accessory'].".png")){
        $accessory = imagecreatefrompng("../../files/dev/".$_GET['accessory'].".png");
        $accessoryX = imagesx($accessory);
        $accessoryY = imagesy($accessory);
        imagecopy($background, $accessory, 0, 0 ,0, 0, $accessoryX, $accessoryY);
    }
}

if (isset($_GET['head']) && is_numeric($_GET['head']) && $_GET['head'] != 0) {
    if (file_exists("../../files/dev/".$_GET['head'].".png")){
        $head = imagecreatefrompng("../../files/dev/".$_GET['head'].".png");
        $headX = imagesx($head);
        $headY = imagesy($head);
        imagecopy($background, $head, 0, 0 ,0, 0, $headX, $headY);
    }
}
header('content-type:image/png');
imagepng($background);
imagedestroy($background);
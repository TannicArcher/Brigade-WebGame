<?php
require '../../main/main.php';
$title = 'Выход из аккаунта';
require '../../main/head.php';

setcookie('uid', null, -1, '/');
setcookie('password', null, -1, '/');
die(header('Location: /'));

require '../../main/foot.php';
?>
<?php
date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ob_start();
session_start();
require_once 'autoload.php';

if (isset($_COOKIE['uid']) and isset($_COOKIE['password'])) {
    $user = $u->isAuth((int)$_COOKIE['uid'], $_COOKIE['password']);
    if (!$user) {
        setcookie('uid', '', time() - 60 * 60 * 24 * 30);
        setcookie('password', '', time() - 60 * 60 * 24 * 30);
        header('Location: /');
    }
}
?>
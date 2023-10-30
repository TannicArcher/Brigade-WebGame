<?php
// Автоподгрузка классов
require 'classes/Database.class.php';
require 'classes/Debug.class.php';
require 'classes/Groups.class.php';
require 'classes/Main.class.php';
require 'classes/Npc.class.php';
require 'classes/Objects.class.php';
require 'classes/Worldkassa.class.php';
require 'classes/Paginations.class.php';
require 'classes/Users.class.php';
require 'classes/Weapons.class.php';
require 'classes/Maps.class.php';
require 'classes/Emoji.class.php';
require 'classes/Fights.class.php';

$db = new Database();
$u = new Users($db);
$obj = new Objects($db);
$wpn = new Weapons($db, $obj);
$npc = new Npc($db, $wpn, $obj);
$m = new Main($db);
$wk = new Worldkassa($db, $u);
$map = new Maps($db, $u);
$clan = new Groups($db, $u);
$debug = new Debug();
$emoji = new Emoji();
$fights = new Fights($db, $u);

require 'data.php';
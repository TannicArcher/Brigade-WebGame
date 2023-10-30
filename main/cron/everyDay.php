<?php
include '../main.php';
$db->query('UPDATE `users` SET `changeBolts` = ? WHERE `changeBolts` != ?', [50000, 50000]);
$db->query('UPDATE `users` SET `everyDay` = ? WHERE `everyDay` = ?', [0, 1]);
$db->query('UPDATE `users` SET `free_energy` = ? WHERE `free_energy` != ?', [0, 0]);
$db->query('UPDATE `districts_users` SET `repute_today` = ? WHERE `repute_today` != ?', [0, 0]);
$db->query('UPDATE `groups_users` SET `exp_today` = ? WHERE `exp_today` != ?', [0, 0]);
$db->query('TRUNCATE `everyDay`', []);
?>
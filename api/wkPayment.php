<?php
require '../main/main.php';

if (isset($_POST['id_shop']) && is_numeric($_POST['id_shop']) && isset($_POST['id_bill']) && is_numeric($_POST['id_bill']) && isset($_POST['summa']) && is_numeric($_POST['summa']) && isset($_POST['hash'])) {
    if ($check = $db->get('SELECT * FROM `merchant` WHERE `id_billing` = ?', [$_POST['id_bill']])) {
        if ($_POST['summa'] < $check['amount']) {
            //
        } else {
            $db->query('UPDATE `merchant` SET `time_pay` = ? WHERE `id` = ?', [time(), $check['id']]);
            $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [2, $check['id_user'], time(), 'Покупка прошла успешно. Вот твои '.$check['give'].' руб.']);
            $lastIDuser = $db->lastInsertId();
            $db->query('INSERT INTO `phone_sms_attach` (`id_sms`, `attachType`, `attachAmount`) VALUES (?, ?, ?)', [$lastIDuser, 'rubles', $check['give']]);
            $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [2, 1, time(), 'Игрок @id'.$check['id_user'].' пополнил счет на '.$check['give'].' руб.']);
            if ($ref = $db->get('SELECT `id_user` FROM `refferals_in` WHERE `id_ref` = ?', [$check['id_user']])){
                $giveRef = floor(intval($check['give']) * .05); // 5%
                $db->query('UPDATE `users` SET `rubles` = `rubles` + ? WHERE `id` = ?', [($giveRef < 1 ? 1 : $giveRef), $ref['id_user']]);
                $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [2, $ref['id_user'], time(), 'Твой браток @id'.$check['id_user'].' совершил покупку. Держи свои положенные 5% с продажи.']);
                $lastID = $db->lastInsertId();
                $db->query('INSERT INTO `phone_sms_attach` (`id_sms`, `attachType`, `attachAmount`) VALUES (?, ?, ?)', [$lastID, 'rubles', ($giveRef < 1 ? 1 : $giveRef)]);
            }
        }
    }
}
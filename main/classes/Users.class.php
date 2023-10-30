<?php

/**
 * Users
 */

class Users
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function isAuth($uid, $hash)
    {
        if($sth = $this->db->get('SELECT `id`, `password` FROM `users` WHERE `id` = ? and `password` = ?', [$uid, $hash])) {
            return $this->getInfo($sth['id']);
        }
        return false;
    }

    public function getInfo($uid)
    {
        $sth = $this->db->get("SELECT * FROM users WHERE id = ?", [$uid]);
        return $sth;
    }

    // Только для авторизированных
    public function forUser($uid = [], $check = false)
    {
        if (!$check) {
            if (empty($uid)) return header('Location: /profile/signin');
        } else {
            if (empty($uid))
                return false; // вернет, если человек не авторизирован
            else
                return true; // вернет, если человек авторизирован
        }
    }

    public function checkBan($uid) {
        return $this->db->get('SELECT * FROM `banList` WHERE (`endBan` > ? or `forever` = ?) and `id_user` = ? ORDER BY `id` DESC LIMIT 1', [time(), 1, $uid]);
    }

    // Только для не авторизированных
    public function forGuest($uid = [], $check = false)
    {
        if (!$check) {
            if (!empty($uid)) return header('Location: /');
        } else {
            if (!empty($uid))
                return false; // вернет, если человек авторизирован
            else
                return true;  // вернет, если человек не авторизирован
        }
    }

    public function getLogin($uid, $link = false)
    {
        $sth = $this->db->get("SELECT id, login, access, updDate FROM users WHERE id = ?", [$uid]);
        if (!$sth) return '[неизвестно]';
        elseif ($link) {
            return (time() < ($sth['updDate'] + 900) ? "<img width='5px' src='/files/icons/online.png' /> " : null) ." <a class='access-{$sth['access']}' href='/id{$sth['id']}'>{$sth['login']}</a>";
        } else {
            return (time() < ($sth['updDate'] + 900) ? "<img width='5px' src='/files/icons/online.png' /> " : null) . " <span class='access-{$sth['access']}'>{$sth['login']}</span>";
        }
    }

    public function getAccess($uid, $level)
    {
        $sth = $this->db->get("SELECT `access` FROM `users` WHERE `id` = ?", [$uid]);
        if (!$sth) return header('Location: /');
        else {
            if ($sth['access'] < $level) header('Location: /');
        }
    }

    public function updateDayQuest($uid, $column = 'quest_1', $amount = 1)
    {
        $sql = 'UPDATE `everyDay` SET `'.$column.'` = `'.$column.'` + ? WHERE `id_user` = ?';
        return $this->db->query($sql, [$amount, $uid]);
    }

    public function giveExp($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET exp = exp + ? WHERE id = ?', [$amount, $uid]);
    }

    public function giveRepute($uid, $amount)
    {
        $this->updateDayQuest($uid, 'quest_1', $amount);
        return $this->db->query('UPDATE `users` SET `repute` = `repute` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function giveBolts($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `bolts` = `bolts` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function takeBolts($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `bolts` = `bolts` - ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function giveRubles($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `rubles` = `rubles` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function takeRubles($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `rubles` = `rubles` - ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function giveKnife($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `knife` = `knife` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function takeKnife($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `knife` = `knife` - ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function givePistol($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `pistol` = `pistol` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function takePistol($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `pistol` = `pistol` - ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function giveEnergy($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `energy` = `energy` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function takeEnergy($uid, $amount)
    {
        $us = $this->db->get('SELECT `energy`, `max_energy` FROM `users` WHERE `id` = ?', [$uid]);
        $this->updateDayQuest($uid, 'quest_2', $amount);
        if ($us['max_energy'] == $us['energy']) {
            // Чтобы не засчитывалось старое накопленное за время простоя время
            return $this->db->query('UPDATE `users` SET `energy` = `energy` - ?, `updateEnergy` = ? WHERE `id` = ?', [$amount, time(), $uid]);
        } else {
            return $this->db->query('UPDATE `users` SET `energy` = `energy` - ? WHERE `id` = ?', [$amount, $uid]);
        }
    }

    public function giveHealth($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `hp` = `hp` + ? WHERE `id` = ?', [$amount, $uid]);
    }

    public function takeHealth($uid, $amount)
    {
        return $this->db->query('UPDATE `users` SET `hp` = `hp` - ? WHERE `id` = ?', [$amount, $uid]);
    }
}
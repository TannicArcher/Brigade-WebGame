<?php

class Fights
{
    public function __construct($db, $u)
    {
        $this->db = $db;
        $this->u = $u;
    }

    public function getUserInfo($uid) {
        return $this->db->get('SELECT `bf`.*, `bm`.`takeDrop` FROM `boss_fight` as `bf` JOIN `boss_members` as `bm` ON (`bf`.`id` = `bm`.`id_fight`) WHERE `bm`.`id_user` = ? and `bm`.`kick` = ? and `bm`.`invite` = ?', [$uid, 0, 1]);
    }

    public function getTimeout($uid, $boss) {
        $sql = 'SELECT `boss_'.$boss.'_timeout` as `timeout` FROM `boss_users` WHERE `id_user` = ?';
        $re = $this->db->get($sql, [$uid]);
        return $re['timeout'];
    }

    public function getBoss($id) {
        $re = $this->db->get('SELECT * FROM `boss` WHERE `id` = ?', [$id]);
        if ($drop = $this->db->getAll('SELECT * FROM `boss_drop` WHERE `id_boss` = ?', [$id])) $re['drop'] = $drop;
        return $re;
    }

    public function createRoom($boss, $uid, $type = 'all') {
        $gets = $this->db->get('SELECT `health` FROM `boss` WHERE `id` = ?', [$boss]);
        $this->db->query('INSERT INTO `boss_fight` (`id_boss`, `id_lider`, `fightType`, `created_at`, `health`) VALUES (?, ?, ?, ?, ?)', [$boss, $uid, $type, time(), $gets['health']]);
        $last = $this->db->lastInsertId();
        $this->db->query('INSERT INTO `boss_members` (`id_fight`, `id_user`, `created_at`) VALUES (?, ?, ?)', [$last, $uid, time()]);
        return $last;
    }
    
    public function getRoomInfo ($id) {
        $re = $this->db->get('SELECT * FROM `boss_fight` WHERE `id` = ? ', [$id]);
        if ($check['members'] = $this->db->getAll('SELECT * FROM `boss_members` WHERE `id_fight` = ? and `kick` = ? ORDER BY `created_at` ASC', [$id, 0])) $re['members'] = $check['members'];
        if ($check['chat'] = $this->db->getCount('SELECT COUNT(`id`) FROM `boss_chat` WHERE `id_fight` = ?', [$id]))  $re['chat'] = $check['chat'];
        if ($check['log'] = $this->db->getCount('SELECT COUNT(`id`) FROM `boss_logs` WHERE `id_fight` = ?', [$id]))  $re['log'] = $check['log'];
        return $re;
    }

    public function getRoomUser ($uid) {
        return $this->db->get('SELECT * FROM `boss_members` WHERE `id_user` = ? and `kick` = ? and `takeDrop` = ?', [$uid, 0, 0]);
    }

    public function inFight($id, $uid) {
        return $this->db->get('SELECT * FROM `boss_members` WHERE `id_fight` = ? and `id_user` = ?', [$id, $uid]);
    }

    public function inFights($uid) {
        return $this->db->get('SELECT `id` FROM `boss_members` WHERE `id_user` = ? and `kick` = ? and `takeDrop` = ?', [$uid, 0, 0]);
    }

    public function startFight($room) {
        $this->db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$room, 2, 'Битва началась.', time()]);
        return $this->db->query('UPDATE `boss_fight` SET `started_at` = ? WHERE `id` = ?', [time(), $room]);
    }

    public function getImageBoss ($hp, $max_hp) {
        $percent = 100 * $hp / $max_hp;
        if ($percent <= 33) $image = 3;
        elseif ($percent <= 66) $image = 2;
        else $image = 1;
        return $image;
    }

    public function getDamage ($fight, $uid){
        $damage = $this->db->get('SELECT SUM(`damage`) as `dmg` FROM `boss_logs` WHERE `id_fight` = ? and `id_user` = ?', [$fight, $uid]);
        return $damage['dmg'] ?? 0;
    }
}
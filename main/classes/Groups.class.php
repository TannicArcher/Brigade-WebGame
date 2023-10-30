<?php

class Groups
{
    public function __construct($db, $u)
    {
        $this->db = $db;
        $this->u = $u;
    }

    public function getInfo($id)
    {
        $sth = $this->db->get("SELECT * FROM `groups` WHERE `id` = ?", [$id]);
        if ($sth) $sth['count'] = $this->db->getCount('SELECT count(id) FROM `groups_users` WHERE `id_group` = ? and `accept` = ?', [$sth['id'], 1]);
        return $sth;
    }

    public function have($id)
    {
        $sth = $this->db->get('SELECT 
      `gu`.`id`, `gu`.`id_group`, `gu`.`id_user`, `gu`.`rank`, `gu`.`exp_today`, `gu`.`exp_all`, `gu`.`accept`, `gu`.`dateAdd`, `gu`.`donate_bolts`, `gu`.`donate_rubles`,
      `g`.* 
      FROM `groups_users` AS `gu` JOIN `groups` AS `g` ON (`g`.`id` = `gu`.`id_group`) WHERE `gu`.`id_user` = ? and `gu`.`accept` = ? LIMIT 1', [$id, 1]);
        if ($sth) $sth['count'] = $this->db->getCount('SELECT count(id) FROM `groups_users` WHERE `id_group` = ? and `accept` = ?', [$sth['id_group'], 1]);
        return $sth;
    }

    public function create($name, $about, $uid)
    {
        $this->db->query('INSERT INTO `groups` (`id_lider`, `name`, `about`, `dateCreate`) VALUES (?, ?, ?, ?)', [$uid, $name, $about, time()]);
        $gid = $this->db->lastInsertId();
        $this->db->query('INSERT INTO `groups_users` (`id_group`, `id_user`, `rank`, `accept`, `dateAdd`) VALUES (?, ?, ?, ?, ?)', [$gid, $uid, 4, 1, time()]);
        $this->u->takeRubles($uid, 100);
        $log = 'ВНИМАНИЕ: в городе замечено новое ОПГ @clan'.$gid.'. Ходите осторожнее, никто пока не знает, на что они способны.';
        $this->db->query('INSERT INTO `chat` (`uid`, `message`, `timeAdd`) VALUES (?, ?, ?)', [2, $log, time()]);
        return $gid;
    }

    public function giveRepute($amount, $gid, $uid) {
        $this->db->query('UPDATE `groups` SET `exp` = `exp` + ? WHERE `id` = ?', [$amount, $gid]);
        $this->db->query('UPDATE `groups_users` SET `exp_today` = `exp_today` + ?, `exp_all` = `exp_all` + ? WHERE `id_group` = ? and `id_user` = ?', [$amount, $amount, $gid, $uid]);
    }
}

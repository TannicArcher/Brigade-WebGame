<?php

/**
 *
 */

class Maps
{
    public function __construct($db, $u)
    {
        $this->db = $db;
        $this->u = $u;
    }

    public function getAllDistrict()
    {
        return $this->db->getAll('SELECT id FROM `districts`', []);
    }

    public function getInfoDistrict($id)
    {
        $d = $this->db->get('SELECT * FROM districts WHERE id = ?', [$id]);
        if ($d) $d['max'] = $this->db->get('SELECT id_user, repute FROM districts_users WHERE id_district = ? ORDER BY repute DESC LIMIT 1', [$d['id']]);
        if ($d) $d['max_today'] = $this->db->get('SELECT id_user, repute_today FROM districts_users WHERE id_district = ? ORDER BY repute_today DESC LIMIT 1', [$d['id']]);
        return $d;
    }

    public function getUserDistrict($uid, $district)
    {
        return $this->db->get('SELECT * FROM `districts_users` WHERE `id_district` = ? and `id_user` = ?', [$district, $uid]);
    }

    public function reset($uid, $district)
    {
        return $this->db->query('UPDATE `districts_users` 
                            SET `1` = ?,
                                `2` = ?,
                                `3` = ?,
                                `4` = ?,
                                `5` = ?,
                                `6` = ?,
                                `7` = ?,
                                `success` = `success` + 1
                            WHERE `id_user` = ? and 
                                  `id_district` = ?',
            [0, 0, 0, 0, 0, 0, 0, $uid, $district]);
    }

    public function updateQuest($quest, $repute, $uid, $district)
    {
        $sql = 'UPDATE `districts_users` 
                SET `' . $quest . '` = `' . $quest . '` + ?,
                    `repute` = `repute` + ?,
                    `repute_today` = `repute_today` + ?
                WHERE `id_user` = ? and 
                      `id_district` = ?';
        return $this->db->query($sql, [1, $repute, $repute, $uid, $district]);
    }

    public function giveRepute($uid, $district, $amount)
    {
        return $this->db->query('UPDATE `districts_users` SET `repute` = `repute` + ?, `repute_today` = `repute_today` + ? WHERE `id_user` = ? and `id_district` = ?', [$amount, $amount, $uid, $district]);
    }

    public function showQuest($complite, $all)
    {
        $percent = 100 / $all;
        $grey = $all - $complite;

        if ($complite > 0) {
            for ($c = 0; $c < $complite; $c++) {
                echo '<div class="one cols" style="width: ' . $percent . '%"><div class="quest-green"></div></div>';
            }
        }
        if ($grey > 0) {
            for ($g = 0; $g < $grey; $g++) {
                echo '<div class="one cols" style="width: ' . $percent . '%"><div class="quest-grey"></div></div>';
            }
        }
    }

    public function getInfoBusiness($idBiz) {
        return $this->db->get('SELECT * FROM `businessList` WHERE `id` = ?', [$idBiz]);
    }
    
    public function getInfoDot($id) {
        return $this->db->get('SELECT * FROM `dots` WHERE `id` = ?', [$id]);
    }
}

<?php

/**
 *
 */

class Weapons
{
    function __construct($db, $obj)
    {
        $this->db = $db;
        $this->obj = $obj;
    }

    public function get($id)
    {
        $ret = $this->db->get('SELECT * FROM weapons WHERE id = ?', [$id]);
        $cnt = $this->db->getCount('SELECT count(id) FROM weapons_stats WHERE id_weapon = ?', [$id]);
        if ($cnt > 0) {
            $stats = $this->db->getAll('SELECT atrb, bonus FROM weapons_stats WHERE id_weapon = ?', [$id]);
            $ret['stats'] = $stats;
        }
        return $ret;
    }

    public function getUser($id)
    {
        $ret = $this->db->get('SELECT * FROM weapons_users WHERE id = ?', [$id]);

        $equip = $this->get($ret['id_weapon']);
        if ($equip) $ret['info'] = $equip;

        if ($ret['info']['slot'] != 'pistol' and $ret['info']['slot'] != 'gun') {
            unset($ret['wear']);
        }

        $cnt = $this->db->getCount('SELECT count(id) FROM weapons_name WHERE id_weapon = ? and accept = ?', [$id, 1]);
        if ($cnt > 0) {
            $name = $this->db->get('SELECT name FROM weapons_name WHERE id_weapon = ? and accept = ?', [$id, 1]);
            $ret['info']['tag']['use'] = true;
            $ret['info']['tag']['original'] = $ret['info']['name'];
            $ret['info']['name'] = $name['name'];
        }
        return $ret;
    }

    public function have($id, $uid)
    {
        return $this->db->getCount('SELECT count(id) FROM weapons_users WHERE id_weapon = ? and id_user = ?', [$id, $uid]);
    }

    public function give($id, $uid)
    {
        return $this->db->query('INSERT INTO weapons_users (id_weapon, id_user, dateAdd) VALUES (?, ?, ?)', [$id, $uid, time()]);
    }

    public function change($id, $cid = 0)
    {
        if ($cid == 0) {
            $this->db->query('DELETE FROM weapons_users WHERE id = ?', [$id]);
            $this->db->query('DELETE FROM weapons_name WHERE id_weapon = ?', [$id]);
        } else {
            $this->db->query('UPDATE weapons_users SET id_user = ? WHERE id = ?', [$cid, $id]);
        }
    }

    public function getEquipSlot($slot, $uid)
    {
        $sth = $this->db->get('SELECT wu.id FROM weapons_users as wu JOIN weapons as w ON (wu.id_weapon = w.id) WHERE w.slot = ? and wu.id_user = ? and wu.used = ?', [$slot, $uid, 1]);

        if ($sth) return $this->getUser($sth['id']);
        else return false;
    }

    public function getEquip($uid)
    {
        $all = $this->db->getAll('SELECT wu.id FROM weapons_users AS wu JOIN weapons AS w ON (wu.id_weapon = w.id) WHERE wu.id_user = ? and wu.used = ? ORDER BY FIELD (w.slot, "head", "accessory", "top", "body", "boot")', [$uid, 1]);
        foreach ($all as $key) {
            $ret[] = $this->getUser($key['id']);
        }
        return (isset($ret) ? $ret : 0);
    }

    public function equip($id, $uid)
    {
        $gun = $this->db->get('SELECT wu.id, wu.used, w.slot, wu.id_weapon, wu.id_user, w.lvl, u.level, w.name FROM weapons_users AS wu JOIN weapons AS w ON (wu.id_weapon = w.id) JOIN users AS u ON (wu.id_user = u.id) WHERE wu.id = ? and wu.id_user = ?', [$id, $uid]);

        if (!$gun) return ['Такого предмета нет в рюкзаке.'];
        elseif ($gun['lvl'] > $gun['level']) return ['Ваш уровень не позволяет надеть этот предмет.'];
        elseif ($gun['used'] == 1) // Если предмет уже надет.
        {
            $sth = $this->db->getAll('SELECT wu.id FROM weapons_users AS wu JOIN weapons AS w ON (wu.id_weapon = w.id) WHERE wu.id_user = ? and wu.used = ? and w.slot = ?', [$uid, 1, $gun['slot']]);
            foreach ($sth as $key) {
                $bonus = $this->db->getAll('SELECT ws.atrb, ws.bonus, wu.id_user FROM weapons_users AS wu JOIN weapons_stats AS ws ON (wu.id_weapon = ws.id_weapon) WHERE wu.id = ?', [$key['id']]);
                if ($bonus != false) {
                    foreach ($bonus as $b) {
                        $sql = 'UPDATE users SET ' . $b['atrb'] . ' = ' . $b['atrb'] . ' - ? WHERE id = ?';
                        $this->db->query($sql, [$b['bonus'], $uid]);
                    }
                }
                $this->db->query('UPDATE weapons_users SET used = ? WHERE id = ?', [0, $key['id']]);
                return ["Вы сняли с себя \"{$gun['name']}\""];
            }
        } elseif ($gun['used'] == 0) {
            // Если есть уже надетые предметы в этом слоте
            $sth = $this->db->getAll('SELECT wu.id FROM weapons_users AS wu JOIN weapons AS w ON (wu.id_weapon = w.id) WHERE wu.id_user = ? and wu.used = ? and w.slot = ?', [$uid, 1, $gun['slot']]);
            foreach ($sth as $key) {
                $bonus = $this->db->getAll('SELECT ws.atrb, ws.bonus, wu.id_user FROM weapons_users AS wu JOIN weapons_stats AS ws ON (wu.id_weapon = ws.id_weapon) WHERE wu.id = ?', [$key['id']]);
                if ($bonus != false) {
                    foreach ($bonus as $b) {
                        $sql = 'UPDATE users SET ' . $b['atrb'] . ' = ' . $b['atrb'] . ' - ? WHERE id = ?';
                        $this->db->query($sql, [$b['bonus'], $uid]);
                    }
                }
                $this->db->query('UPDATE weapons_users SET used = ? WHERE id = ?', [0, $key['id']]);
            }
            // Надеваем нужный нам предмет
            $bonus = $this->db->getAll('SELECT * FROM weapons_stats WHERE id_weapon = ?', [$gun['id_weapon']]);
            if ($bonus != false) {
                foreach ($bonus as $b) {
                    $sql = 'UPDATE users SET ' . $b['atrb'] . ' = ' . $b['atrb'] . ' + ? WHERE id = ?';
                    $this->db->query($sql, [$b['bonus'], $uid]);
                }
            }
            $this->db->query('UPDATE weapons_users SET used = ? WHERE id = ?', [1, $id]);
            return ["Вы надели на себя \"{$gun['name']}\""];
        }
    }

    public function link($id, $user = false, $onlyhref = false)
    {
        if (!$user) {
            $equip = $this->getUser($id);
            if (!$onlyhref) return "<a class='quality-{$equip['info']['quality']}' href='/view/equipments/{$equip['id']}'>{$equip['info']['name']}</a>";
            else return "/view/equipments/{$equip['id']}";
        } else {
            $equip = $this->get($id);
            if (!$onlyhref) return "<a class='quality-{$equip['quality']}' href='/wiki/equipments/{$equip['id']}'>{$equip['name']}</a>";
            else return "/wiki/equipments/{$equip['id']}";
        }
    }

    public function parse($id, $uid)
    {
        $gun = $this->db->get('SELECT wu.id, wu.used, w.slot, wu.id_weapon, wu.id_user, w.lvl, u.level, w.name, w.quality FROM weapons_users AS wu JOIN weapons AS w ON (wu.id_weapon = w.id) JOIN users AS u ON (wu.id_user = u.id) WHERE wu.id = ? and wu.id_user = ?', [$id, $uid]);

        if (!$gun) return ['Такого предмета нет в рюкзаке.'];
        elseif ($gun['used'] == 1) return ['Нельзя разобрать предмет, который надет на Вас.'];
        elseif ($gun['quality'] == 'souvenir') return ['Сувенирные предметы нельзя разобрать.'];
        else {
            $p = [
                'trash' => ['max' => 1, 'amount_max' => 1],
                'normal' => ['max' => 1, 'amount_max' => 3],
                'rare' => ['max' => 3, 'amount_max' => 5],
                'heroic' => ['max' => 5, 'amount_max' => 10],
            ];

            $rnd = mt_rand(1, $p[$gun['quality']]['max']);
            $item = $this->db->getAll('SELECT id, name FROM objects WHERE types = ? ORDER BY RAND() LIMIT ?', ['craft', $rnd]);

            foreach ($item as $get) {
                $rndA = mt_rand(1, $p[$gun['quality']]['amount_max']);
                $this->obj->give($get['id'], $uid, $rndA);
                $re[] = "Получен предмет \"{$get['name']}\" в количестве {$rndA} ед.";
            }

            $this->change($id, 0);

            return $re;
        }
    }

    public function trash($id, $uid)
    {
        $gun = $this->db->get('SELECT wu.id, wu.used, wu.id_weapon, wu.id_user FROM weapons_users AS wu JOIN weapons AS w ON (wu.id_weapon = w.id) JOIN users AS u ON (wu.id_user = u.id) WHERE wu.id = ? and wu.id_user = ?', [$id, $uid]);

        if (!$gun) return ['Такого предмета нет в рюкзаке.'];
        elseif ($gun['used'] == 1) return ['Нельзя выбросить предмет, который надет на Вас.'];
        else {
            $this->change($id, 0);
            return ['Предмет успешно выброшен.'];
        }
    }
}
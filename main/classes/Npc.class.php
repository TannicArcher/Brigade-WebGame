<?php

class Npc
{
    public function __construct($db, $wpn, $obj)
    {
        $this->db = $db;
        $this->wpn = $wpn;
        $this->obj = $obj;
    }

    public function get($id)
    {
        return $this->db->get('SELECT * FROM npc WHERE id = ?', [$id]);
    }

    public function buyEquip($id, $uid, $price, $amount)
    {
        $get = $this->wpn->get($id);
        $user = $this->db->get('SELECT level, rubles, bolts FROM users WHERE id = ?', [$uid]);

        if ($price == 'rubles' and ($user['rubles'] < $amount)) return ['Недостаточно рублей на балансе для покупки'];
        elseif ($price == 'bolts' and ($user['bolts'] < $amount)) return ['Недостаточно черного нала на балансе для покупки'];
        elseif ($get['lvl'] > $user['level']) return ['Ваш уровень слишком мал для покупки этого предмета'];
        else {
            $sql = "UPDATE users SET {$price} = {$price} - ? WHERE id = ?";
            $this->db->query($sql, [$amount, $uid]);
            $this->wpn->give($id, $uid);
            return 200;
        }
    }

    public function canUse($id, $uid)
    {
        $get['user'] = $this->db->get('SELECT x, y, location FROM users WHERE id = ?', [$uid]);
        $get['npc'] = $this->db->get('SELECT x, y, location FROM npc WHERE id = ?', [$id]);

        if ($get['user']['location'] == $get['npc']['location'] and ($get['npc']['x'] == null and $get['npc']['y'] == null)) return true;
        elseif ($get['user']['location'] == $get['npc']['location'] and ($get['npc']['x'] != null and $get['npc']['y'] != null)) {
            $go = $this->db->get('SELECT id FROM npc WHERE (x BETWEEN ? AND ?) AND (y BETWEEN ? AND ?) AND location = ?', [$get['user']['x'] - 2, $get['user']['x'] + 2, $get['user']['y'] - 2, $get['user']['y'] + 2, $get['user']['location']]);
            if ($go) return true;
            else return false;
        } else return false;
    }

    public function link($id, $link = true)
    {
        $get = $this->get($id);
        if ($link) return "<a href='/npc/{$get['id']}'>{$get['name']}</a>";
        else return $get['name'];
    }
}

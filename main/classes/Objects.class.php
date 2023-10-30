<?php
/**
 * Objects
 */

class Objects
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get($id) // Информация о предмете
    {
        $get = $this->db->get('SELECT * FROM objects WHERE id = ?', [$id]);
        return $get;
    }

    public function getUser($id) // Информация о предмете
    {
        $get = $this->db->get('SELECT * FROM objects_users WHERE id = ?', [$id]);
        $get['info'] = $this->get($get['id_object']);
        return $get;
    }

    public function give($id, $uid, $amount = 1) // Выдать предмет игроку
    {
        $get = $this->db->get('SELECT id FROM objects_users WHERE id_object = ? and id_user = ?', [$id, $uid]);

        if (!$get) {
            $this->db->query('INSERT INTO objects_users (id_object, id_user, count, dateAdd) VALUES (?, ?, ?, ?)', [$id, $uid, $amount, time()]);
            return true;
        } else {
            $this->db->query('UPDATE objects_users SET count = count + ? WHERE id = ?', [$amount, $get['id']]);
            return true;
        }
    }

    public function take($id, $uid, $amount = 1) // Забрать предмет у игрока
    {
        $get = $this->db->get('SELECT id, count FROM objects_users WHERE id_object = ? and id_user = ?', [$id, $uid]);
        if ($get) {
            if ($get['count'] == $amount) {
                $this->db->query('DELETE FROM objects_users WHERE id = ?', [$get['id']]);
                return true;
            } elseif ($get['count'] > $amount) {
                $this->db->query('UPDATE objects_users SET count = count - ? WHERE id = ?', [$amount, $get['id']]);
                return true;
            } else return ['У Вас нет столько единиц этого предмета.'];
        } else return ['У Вас нет нужного предмета.'];
    }

    public function have($id, $uid, $amount = 1) // Проверить есть ли данный предмет у игрока
    {
        $get = $this->db->get('SELECT id, count FROM objects_users WHERE id_object = ? and id_user = ?', [$id, $uid]);
        if ($get and $get['count'] >= $amount) return true;
        else return false;
    }

    public function use($id, $uid, $amount = 1)
    {
        $check = $this->have($id, $uid, $amount);
        if ($check) {
            $info = $this->get($id);
            if ($info['types'] == 'hp') {
                $u = $this->db->get('SELECT hp, max_hp FROM users WHERE id = ?', [$uid]);
                if ($u['hp'] < $u['max_hp']) {
                    $this->db->query('UPDATE users SET hp = hp + ? WHERE id = ?', [($info['whatType'] ? $u['max_hp'] * ($info['what'] / 100) : $info['what']), $uid]);
                    $this->take($id, $uid, $amount);
                    return ["Вы использовали \"{$info['name']}\""];
                } else return ["\"{$info['name']}\" нельзя использовать, так как у Вас полное здоровье."];
            } elseif ($info['types'] == 'energy') {
                $u = $this->db->get('SELECT energy, max_energy FROM users WHERE id = ?', [$uid]);
                if ($u['energy'] < $u['max_energy']) {
                    $this->db->query('UPDATE users SET energy = energy + ? WHERE id = ?', [$info['what'], $uid]);
                    $this->take($id, $uid, $amount);
                    return ["Вы использовали \"{$info['name']}\""];
                } else return ["\"{$info['name']}\" нельзя использовать, так как у Вас полная энергия."];
            } else return ["\"{$info['name']}\" нельзя использовать"];
        } else return ["У вас нет этого предмета"];
    }

    public function link($id, $onlyhref = false)
    {
        $get = $this->db->get('SELECT name FROM objects WHERE id = ?', [$id]);
        if ($get) {
            if (!$onlyhref) return "<a href='/wiki/objects/{$id}'>{$get['name']}</a>";
            else return "/wiki/objects/{$id}";
        } else return '[ошибка]';
    }

    public function getCountObject($user, $object)
    {
        $get = $this->db->get('SELECT `id`, `count` FROM `objects_users` WHERE `id_object` = ? and `id_user` = ?', [$object, $user]);

        if (!isset($get['id'])) return 0;
        else return $get['count'];
    }
}
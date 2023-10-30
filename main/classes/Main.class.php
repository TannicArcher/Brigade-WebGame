<?php
/**
 *
 */

use DateTime;

class Main
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function pda($text = [])
    {
        echo '<div class="container">';
        echo '<div class="pda-message w-100">';
        echo '<div class="pda-message__title">Пейджер</div>';
        foreach ($text as $key) {
            echo "<div class='pda-message__item'>{$key}</div>";
        }
        echo '</div>';
        echo '</div>';
    }

    public function num($num)
    {
        $num = (0 + str_replace(",", "", $num));

        if (!is_numeric($num)) return false;

        if ($num > 1000000000000) return round(($num / 1000000000000), 1) . 't';
        elseif ($num > 1000000000) return round(($num / 1000000000), 1) . 'b';
        elseif ($num > 1000000) return round(($num / 1000000), 1) . 'm';
        elseif ($num > 10000) return round(($num / 1000), 1) . 'k';

        return number_format($num);
    }

    public function declension($digit, $expr, $onlyword = false)
    {
        if (!is_array($expr)) $expr = array_filter(explode(' ', $expr));
        if (empty($expr[2])) $expr[2] = $expr[1];
        $i = preg_replace('/[^0-9]+/s', '', $digit) % 100;
        if ($onlyword) $digit = '';
        if ($i >= 5 && $i <= 20) $res = $digit . ' ' . $expr[2];
        else {
            $i %= 10;
            if ($i == 1) $res = $digit . ' ' . $expr[0];
            elseif ($i >= 2 && $i <= 4) $res = $digit . ' ' . $expr[1];
            else $res = $digit . ' ' . $expr[2];
        }
        return trim($res);
    }

    public function getPage($num = 0, $plus = true)
    {
        $num = $num + ($plus ? 1 : 0);
        if ($num <= 10) return 1;
        else return ceil($num / 10);
    }

    public function downcounter($date)
    {
        $check_time = strtotime($date) - time();
        if ($check_time <= 0) {
            return false;
        }

        $days = floor($check_time / 86400);
        $hours = floor(($check_time % 86400) / 3600);
        $minutes = floor(($check_time % 3600) / 60);
        $seconds = $check_time % 60;

        $str = '';
        if ($days > 0) $str .= $this->declension($days, ['день', 'дня', 'дней']) . ' ';
        if ($hours > 0) $str .= $this->declension($hours, ['час', 'часа', 'часов']) . ' ';
        if ($minutes > 0) $str .= $this->declension($minutes, ['минута', 'минуты', 'минут']) . ' ';
        if ($seconds > 0) $str .= $this->declension($seconds, ['секунда', 'секунды', 'секунд']);

        return $str;
    }

    public function oncounter($date, $after = true)
    {
        $startTime = new Datetime($date);
        $endTime = new DateTime();
        $diff = $endTime->diff($startTime);

        $str = '';


        if ($diff->y > 0) $str .= $this->declension($diff->y, ['год', 'года', 'лет']) . ' ';
        if ($diff->m > 0) $str .= $this->declension($diff->m, ['месяц', 'месяца', 'месяцев']) . ' ';
        if ($diff->d > 0) $str .= $this->declension($diff->d, ['день', 'дня', 'дней']) . ' ';
        if ($diff->h > 0) $str .= $this->declension($diff->h, ['час', 'часа', 'часов']) . ' ';
        if ($diff->i > 0) $str .= $this->declension($diff->i, ['минута', 'минуты', 'минут']) . ' ';
        if ($diff->s > 0) $str .= $this->declension($diff->s, ['секунду', 'секунды', 'секунд']);

        if ($diff->s == 0 and $diff->i == 0 and $diff->h == 0 and $diff->d == 0) $str .= 'только что';
        if ($after and ($diff->s > 0 or ($diff->i > 0 or $diff->h > 0 or $diff->d > 0))) $str .= ' назад';

        return $str;
    }

    public function message($text)
    {
        if (preg_match_all('#@id[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 3);
                    $sth = $this->db->get('SELECT `id`, `login`, `access` FROM `users` WHERE `id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на игрока" class="outline inline access-' . $sth['access'] . '" href="/id' . $sth['id'] . '"><span class="icon-bb">И</span> ' . $sth['login'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@forum[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 6);
                    $sth = $this->db->get('SELECT `id`, `name` FROM `forum_category` WHERE `id` = ? and `access` = ?', [$id, 0]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на раздел форума" class="outline inline" href="/forum/category/' . $sth['id'] . '"><span class="icon-bb">Ф</span> ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@topic[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 6);
                    $sth = $this->db->get('SELECT `ft`.`id`, `ft`.`name` FROM `forum_topics` as `ft` JOIN `forum_category` as `fc` ON (`ft`.`id_category` = `fc`.`id`) WHERE `ft`.`id` = ? and `fc`.`access` = ?', [$id, 0]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на тему форума" class="outline inline" href="/forum/topic/' . $sth['id'] . '"><span class="icon-bb">Т</span> ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@clan[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 5);
                    $sth = $this->db->get('SELECT `id`, `name` FROM `groups` WHERE `id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на ОПГ" class="outline inline" href="/clan/' . $sth['id'] . '"><span class="icon-bb">Г</span> ' . $sth['name'] . ' </a>', $text);
                }
            }
        }

        if (preg_match_all('#@item[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 5);
                    $sth = $this->db->get('SELECT `id`, `name`, `quality` FROM `weapons` WHERE `id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на экипировку" class="outline inline quality-' . $sth['quality'] . '" href="/wiki/equipments/' . $sth['id'] . '"><span class="icon-bb">Э</span> ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@back[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 5);
                    $sth = $this->db->get('SELECT `id`, `name` FROM `background` WHERE `id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на задний план" class="outline inline" href="/wiki/background/' . $sth['id'] . '"><span class="icon-bb">З</span> ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@boss[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 5);
                    $sth = $this->db->get('SELECT `id`, `name` FROM `boss` WHERE `id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на босса" class="outline inline" href="/wiki/boss/' . $sth['id'] . '"><span class="icon-bb">Босс</span> ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@room[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 5);
                    $sth = $this->db->get('SELECT `boss`.`name`, `boss_fight`.`id` FROM `boss_fight` JOIN `boss` ON (`boss_fight`.`id_boss` = `boss`.`id`) WHERE `boss_fight`.`id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на рейд" class="outline inline" href="/boss/room/' . $sth['id'] . '"><span class="icon-bb">Рейд #' . $sth['id'] . '</span> Бой с ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        if (preg_match_all('#@object[0-9]+\b#', $text, $out)) {
            foreach ($out as $get) {
                foreach ($get as $key) {
                    $id = substr($key, 7);
                    $sth = $this->db->get('SELECT `id`, `name` FROM `objects` WHERE `id` = ?', [$id]);
                    if ($sth) $text = str_replace($key, '<a title="Ссылка на предмет" class="outline inline" href="/wiki/objects/' . $sth['id'] . '"><span class="icon-bb">П</span> ' . $sth['name'] . '</a>', $text);
                }
            }
        }

        return nl2br($text);
    }

    public function recursive_array_search($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            if ($needle === $value or (is_array($value) && $this->recursive_array_search($needle, $value))) {
                return $haystack[$key];
            }
        }
        return false;
    }

    public function numberOfDecimals($value)
    {
        if ((int)$value == $value) {
            return 0;
        } else if (!is_numeric($value)) {
            return false;
        }

        return strlen($value) - strrpos($value, '.') - 1;
    }

    public function roulette($items = [])
    {
        $sumOfPercents = 0;
        foreach ($items as $itemsPercent) {
            $sumOfPercents += $itemsPercent;
        }

        $decimals = $this->numberOfDecimals($sumOfPercents);
        $multiplier = 1;
        for ($i = 0; $i < $decimals; $i++) {
            $multiplier *= 10;
        }

        $sumOfPercents *= $multiplier;
        $rand = mt_rand(1, $sumOfPercents);

        $rangeStart = 1;
        foreach ($items as $itemKey => $itemsPercent) {
            $rangeFinish = $rangeStart + ($itemsPercent * $multiplier);
            if ($rand >= $rangeStart && $rand <= $rangeFinish) {
                return $itemKey;
            }

            $rangeStart = $rangeFinish + 1;
        }
    }

    public function generatePassword($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }

    public function to($uri){
        return die(header('Location: '.$uri));
    }

    public function number($num) {
        return abs(intval($num));
    }
}
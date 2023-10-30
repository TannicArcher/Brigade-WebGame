<?php

/**
 * Worldkassa
 */


class Worldkassa
{
    function __construct($db, $user)
    {
        $this->db = $db;
        $this->user = $user;
        $this->bill = '7580';
        $this->key = 'OnxUnyNoeZ6wDcGzheQFOPTUqpaiEyBL';
    }

    public function createPayment($uid, $sum, $give)
    {
        $data = file_get_contents('https://worldkassa.ru/user/oplata.php?id_shop=' . $this->bill . '&summa=' . $sum . '&hash=' . $this->key.'&desc='.urlencode('Покупка '.$give.' рублей'));
        if (is_numeric($data))
        {
            $this->db->query('INSERT INTO `merchant` (`id_user`, `id_billing`, `time_init`, `amount`, `give`) VALUES (?, ?, ?, ?, ?)', [$uid, $data, time(), $sum, $give]);
            $goto = 'https://worldkassa.ru/user/oplata.php?uniq='.$data;
        } else {
            $goto = '/phone/balance';
        }
        return die(header('Location: '.$goto));
    }
}
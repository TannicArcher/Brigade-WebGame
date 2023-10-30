<?php
require '../../main/main.php';
$title = 'Чёрный рынок';
if ($user['repute'] < 500) {
    $_SESSION['notify'][] = 'Тебе пока рано сюда.';
    $m->to($_SERVER['HTTP_REFERER']);
}
require '../../main/head.php';
?>
<h1>Чёрный рынок</h1>
<div class="flex-container">
    <div class="flex-link">
        <div class="card">
            <div class="card__title center">
                Покупка финок
            </div>
            <?php
            if (isset($_POST['buyKnife'])) {
                $post['knife'] = abs(intval($_POST['knife']));
                if (!is_numeric($post['knife'])) $error[] = 'Ошибка при вводе числа';
                if ($post['knife'] > ($user['rubles'] * 4)) $error[] = 'У тебя нет столько рублей, не трать моё время!';

                if (empty($error)) {
                    $db->query('UPDATE `users` SET `knife` = `knife` + ?, `rubles` = `rubles` - ? WHERE `id` = ?', [$post['knife'], ($post['knife'] * 4), $user['id']]);
                    $_SESSION['notify'][] = 'Вы успешно купили '.$post['knife'].' <img src="/files/icons/knife.png" width="16px" alt=""> за '.($post['knife'] * 4).' руб.';
                    $m->to('/blackmarket');
                } else {
                    $m->pda($error);
                }
            }
            ?>
            <form method="post">
                <div class="flex-container" style="align-items: center">
                    <div class="flex-link">
                        <input type="number" name="knife" placeholder="Введите число..." value="1" />
                    </div>
                    <div class="flex-link grow-0 center mh-5">
                        <img src="/files/icons/knife.png" alt="">
                    </div>
                </div>
                <input type="submit" name="buyKnife" value="Купить финки">
            </form>
            <div class="outline center mv-5">
                4 <img src="/files/icons/rubles.png" alt="Руб."> = 1 <img src="/files/icons/knife.png" width="16px" alt=""> / Сейчас у тебя <strong class="access-2"><?php echo $user['knife'];?></strong><img src="/files/icons/knife.png" width="16px" alt="">
            </div>
        </div>
    </div>
    <div class="flex-link">
    <div class="card">
            <div class="card__title center">
                Покупка патронов
            </div>
            <?php
            if (isset($_POST['buyPistol'])) {
                $post['pistol'] = abs(intval($_POST['pistol']));
                if (!is_numeric($post['pistol'])) $error[] = 'Ошибка при вводе числа';
                if ($post['pistol'] > ($user['rubles'] * 9)) $error[] = 'У тебя нет столько рублей, не трать моё время!';

                if (empty($error)) {
                    $db->query('UPDATE `users` SET `pistol` = `pistol` + ?, `rubles` = `rubles` - ? WHERE `id` = ?', [$post['pistol'], ($post['pistol'] * 9), $user['id']]);
                    $_SESSION['notify'][] = 'Вы успешно купили '.$post['pistol'].' <img src="/files/icons/pistol.png" width="16px" alt=""> за '.($post['pistol'] * 9).' рублей.';
                    $m->to('/blackmarket');
                } else {
                    $m->pda($error);
                }
            }
            ?>
            <form method="post">
                <div class="flex-container" style="align-items: center">
                    <div class="flex-link">
                        <input type="number" name="pistol" placeholder="Введите число..." value="1" />
                    </div>
                    <div class="flex-link grow-0 center mh-5">
                        <img src="/files/icons/pistol.png" alt="">
                    </div>
                </div>
                <input type="submit" name="buyPistol" value="Купить патроны">
            </form>
            <div class="outline center mv-5">
                9 <img src="/files/icons/rubles.png" alt="Руб."> = 1 <img src="/files/icons/pistol.png" width="16px" alt=""> / Сейчас у тебя <strong class="access-2"><?php echo $user['pistol'];?></strong><img src="/files/icons/pistol.png" width="16px" alt="">
            </div>
        </div>
    </div>
</div>
<?php
require '../../main/foot.php';
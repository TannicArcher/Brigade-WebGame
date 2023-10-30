<?php
require '../../main/main.php';
$title = 'Прачечная';
require '../../main/head.php';
?>
<h1>Прачечная</h1>
<div class="flex-container">
    <div class="flex-link">
        <div class="card">
            <div class="card__title center">
                Обмен рублей на черный нал.
            </div>
            <?php
            if (isset($_POST['change'])) {
                $post['ruble'] = abs(intval($_POST['ruble']));
                if (!is_numeric($post['ruble'])) $error[] = 'Ошибка при вводе числа';
                if ($post['ruble'] > $user['rubles']) $error[] = 'У тебя нет столько рублей, не трать моё время!';

                if (empty($error)) {
                    $db->query('UPDATE `users` SET `bolts` = `bolts` + ?, `rubles` = `rubles` - ? WHERE `id` = ?', [($post['ruble'] * 500), $post['ruble'], $user['id']]);
                    $_SESSION['notify'][] = 'Вы успешно обменяли '.$post['ruble'].' рублей на '.($post['ruble'] * 500).' черного нала.';
                    $m->to('/washhouse');
                } else {
                    $m->pda($error);
                }
            }
            ?>
            <form method="post">
                <div class="flex-container" style="align-items: center">
                    <div class="flex-link">
                        <input type="number" name="ruble" placeholder="Введите число..." value="1" />
                    </div>
                    <div class="flex-link grow-0 center mh-5">
                        <img src="/files/icons/rubles.png" alt="Руб.">
                    </div>
                </div>
                <input type="submit" name="change" value="Обменять">
            </form>
            <div class="outline center mv-5">
                1 <img src="/files/icons/rubles.png" alt="Руб."> = 500 <img src="/files/icons/bolts.png" alt="Коп.">
            </div>
        </div>
    </div>
    <div class="flex-link">
        <div class="card">
            <div class="card__title center">
                Обмен черного нала на рубли
            </div>
            <?php
            if (isset($_GET['change']) and $_GET['change'] == 2 and isset($_GET['type']) and isset($_GET['yes'])) {
                switch ($_GET['type']) {
                    default:
                        $bolt = 5000;
                        $rub = 1;
                        break;
                    case 2:
                        $bolt = 15000;
                        $rub = 3;
                        break;
                    case 3:
                        $bolt = 30000;
                        $rub = 6;
                        break;
                    case 4:
                        $bolt = 50000;
                        $rub = 10;
                        break;
                }
                if ($user['changeBolts'] < $bolt) $error[] = 'Нельзя столько менять за 1 день. Доступно: '.$user['changeBolts'];
                if ($user['bolts'] < $bolt) $error[] = 'У тебя нет столько черного нала.';

                if (empty($error)) {
                    $db->query('UPDATE `users` SET `bolts` = `bolts` - ?, `changeBolts` = `changeBolts` - ?, `rubles` = `rubles` + ? WHERE `id` = ?', [$bolt, $bolt, $rub, $user['id']]);
                    $_SESSION['notify'][] = 'Вы успешно обменяли '.$bolt.' черного нала на '.$rub.' руб.';
                    $m->to('/washhouse');
                } else {
                    $m->pda($error);
                }
            } elseif (isset($_GET['change']) and $_GET['change'] == 2 and isset($_GET['type'])) {
            ?>
                <div class="question mv-5">
                    <div class="question-answer center access-2">
                        Вы действительно хотите произвести обмен черного нала на рубли?
                    </div>
                    <div class="question-option">
                        <a href="/washhouse?change=2&type=<?php echo $_GET['type'];?>&yes" class="href" style="margin-bottom: 1px;">Да</a>
                        <a href="/forum/topic/<?php echo $id; ?>" class="href">Нет</a>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="flex-container center">
                <div class="flex-link"><a href="?change=2&type=1" class="link">5000</a></div>
                <div class="flex-link"><a href="?change=2&type=2" class="link">15000</a></div>
                <div class="flex-link"><a href="?change=2&type=3" class="link">30000</a></div>
                <div class="flex-link"><a href="?change=2&type=4" class="link">50000</a></div>
            </div>
            <div class="outline center mv-5">
                5000 <img src="/files/icons/bolts.png" alt="Коп."> = 1 <img src="/files/icons/rubles.png" alt="Руб."><br />
                <span class="quality-rare small">Доступно сегодня для обмена: <?php echo $user['changeBolts'];?> черного нала.</span>
            </div>
        </div>
    </div>
</div>
<?php
require '../../main/foot.php';

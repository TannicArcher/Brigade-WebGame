<?php
require '../../main/main.php';
$title = 'Тренажерка';
require '../../main/head.php';
if ($user['training'] <= time() and $user['training'] !== NULL) {
    $sql = 'update users set training = ?, ' . $user['whatTraining'] . ' = ' . $user['whatTraining'] . ' + ? where id = ?';
    $db->query($sql, [NULL, 1, $user['id']]);
    $_SESSION['notify'][] = 'Тренировка окончена.';
    die(header('Location: /gym/'));
} elseif ($user['access'] < 3) {
    $m->pda(['Качалочка закрыта на ремонт.']);
    require '../../main/foot.php';
    die();
}

?>
    <div class="flex-container">
        <div class="gym center">
            <img src="/files/gym/box.png" width="64px" alt=""><br/>
            <div class="gym__title">Бокс</div>
            <div class="gym__about">
                Сила удара
                <div class="gym__count">+ 1</div>
                <hr/>
                Время
                <div class="gym__count"><?php echo $m->downcounter(date('Y-m-j H:i:s', time() + (($user['power'] + 1) * 30))); ?></div>
                <hr/>
                Цена
                <div class="gym__count"><?php echo(($user['power'] + 1) * 30); ?> ЧН.</div>
                <hr>
                <?php
                if (isset($_GET['box'])) {
                    if ($user['training'] > time()) $error[] = 'Ты уже занимаешься';
                    if ($user['bolts'] < (($user['power'] + 1) * 30)) $error[] = 'У тебя нет денег';

                    if (isset($error)) {
                        foreach ($error as $err) {
                            echo '> ' . $err . '<br/>';
                        }
                    } else {
                        $db->query('update users set training = ?, whatTraining = ?, bolts = bolts - ? where id = ?', [time() + (($user['power'] + 1) * 30), 'power', (($user['power'] + 1) * 30), $user['id']]);
                        $_SESSION['notify'][] = 'Тренировка по боксу началась. Окончание тренировки через ' . $m->downcounter(date('Y-m-j H:i:s', time() + (($user['power'] + 1) * 30)));
                        die(header('Location: /gym/'));
                    }
                } elseif ($user['training'] and $user['whatTraining'] == 'power') {
                    echo '<div class="center">До конца занятия<br/><strong>' . $m->downcounter(date('Y-m-j H:i:s', $user['training'])) . '</strong></div>';
                } elseif ($user['training'] and $user['whatTraining'] != 'power') {
                    echo '<div class="center">сейчас недоступно</div>';
                } else {
                    echo '<a class="start center" href="/gym?box">Тренироваться</a>';
                }
                ?>
            </div>
        </div>
        <div class="gym center">
            <img src="/files/gym/sparring.png" width="64px" alt=""><br/>
            <div class="gym__title">Спарринг</div>
            <div class="gym__about">
                Защита
                <div class="gym__count">+ 1</div>
                <hr/>
                Время
                <div class="gym__count"><?php echo $m->downcounter(date('Y-m-j H:i:s', time() + (($user['shield'] + 1) * 30))); ?></div>
                <hr/>
                Цена
                <div class="gym__count"><?php echo(($user['shield'] + 1) * 30); ?> ЧН.</div>
                <hr>
                <?php
                if (isset($_GET['sparring'])) {
                    if ($user['bolts'] < (($user['shield'] + 1) * 30)) $error[] = 'У тебя нет денег';
                    if ($user['training'] > time()) $error[] = 'Ты уже занимаешься';

                    if (isset($error)) {
                        foreach ($error as $err) {
                            echo '> ' . $err . '<br/>';
                        }
                    } else {
                        $db->query('update users set training = ?, whatTraining = ?, bolts = bolts - ? where id = ?', [time() + (($user['shield'] + 1) * 30), 'shield', (($user['shield'] + 1) * 30), $user['id']]);
                        $_SESSION['notify'][] = 'Спарринг начался. Окончание тренировки через ' . $m->downcounter(date('Y-m-j H:i:s', time() + (($user['shield'] + 1) * 30)));
                        die(header('Location: /gym/'));
                    }
                } elseif ($user['training'] and $user['whatTraining'] == 'shield') {
                    echo '<div class="center">До конца занятия<br/><strong>' . $m->downcounter(date('Y-m-j H:i:s', $user['training'])) . '</strong></div>';
                } elseif ($user['training'] and $user['whatTraining'] != 'shield') {
                    echo '<div class="center">сейчас недоступно</div>';
                } else {
                    echo '<a class="start center" href="/gym?sparring">Тренироваться</a>';
                }
                ?>
            </div>
        </div>
        <div class="gym center">
            <img src="/files/gym/rope.png" width="64px" alt=""><br/>
            <div class="gym__title">Скакалка</div>
            <div class="gym__about">
                Макс.энергии
                <div class="gym__count">+ 1</div>
                <hr/>
                Время
                <div class="gym__count"><?php echo $m->downcounter(date('Y-m-j H:i:s', time() + (($user['max_energy'] + 1) * 60))); ?></div>
                <hr/>
                Цена
                <div class="gym__count"><?php echo ceil($user['max_energy'] * 2); ?> Руб.</div>
                <hr>
                <?php
                if (isset($_GET['rope'])) {
                    if ($user['rubles'] < ceil($user['max_energy'] * 2)) $error[] = 'У тебя нет денег';
                    if ($user['training'] > time()) $error[] = 'Ты уже занимаешься';

                    if (isset($error)) {
                        foreach ($error as $err) {
                            echo '> ' . $err . '<br/>';
                        }
                    } else {
                        $db->query('update users set training = ?, whatTraining = ?, rubles = rubles - ? where id = ?', [time() + (($user['max_energy'] + 1) * 60), 'max_energy', ceil($user['max_energy'] * 2), $user['id']]);
                        $_SESSION['notify'][] = 'Тренировка началась. Окончание тренировки через ' . $m->downcounter(date('Y-m-j H:i:s', time() + (($user['max_energy'] + 1) * 60)));
                        die(header('Location: /gym/'));
                    }
                } elseif ($user['training'] and $user['whatTraining'] == 'max_energy') {
                    echo '<div class="center">До конца занятия<br/><strong>' . $m->downcounter(date('Y-m-j H:i:s', $user['training'])) . '</strong></div>';
                } elseif ($user['training'] and $user['whatTraining'] != 'max_energy') {
                    echo '<div class="center">сейчас недоступно</div>';
                } else {
                    echo '<a class="start center" href="/gym?rope">Тренироваться</a>';
                }
                ?>
            </div>
        </div>
    </div>
<?php
require '../../main/foot.php';
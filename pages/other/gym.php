<?php
require '../../main/main.php';
$title = 'Тренажерка';
$include['css'] = 'gym.style.css';
require '../../main/head.php';
if ($user['training'] <= time() and $user['training'] !== NULL) {
    $sql = 'update users set training = ?, ' . $user['whatTraining'] . ' = ' . $user['whatTraining'] . ' + ? where id = ?';
    $db->query($sql, [NULL, 1, $user['id']]);
    $u->updateDayQuest($user['id'], 'quest_5', 1);
    $_SESSION['notify'][] = 'Тренировка окончена.';
    die(header('Location: /gym/'));
}
if ($user['training'] > time() and $user['training'] !== NULL) : ?>
    <div class="main m-5 center access-2">
        Окончание тренировки через <?php echo $m->downcounter(date('Y-m-j H:i:s', $user['training'])); ?>
    </div>
<?php endif; ?>
<div class="flex-conatainer">
    <div class="flex-link">
        <div class="gym-card" onclick="" style="background-image: url('/files/gym/box.png');">
            <div class="content">
                <h6>
                    Бокс<br />
                    <span class="quality-trash">Тренировка силы удара</span>
                </h6>
                <div class="hover_content">
                    <div class="flex-container">
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about"><img src="/files/icons/bolts.png" alt=""> 500</div>
                            <div class="info-title">Стоимость</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about"><?php echo $user['power']; ?></div>
                            <div class="info-title">Текущая сила (с учетом вещей)</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about">1 ед.</div>
                            <div class="info-title">Прибавит</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about">60 минут</div>
                            <div class="info-title">Длительность</div>
                        </div>
                    </div>
                    <?php
                    if (isset($_GET['box'])) {
                        if ($user['training'] > time()) $error[] = 'Ты уже тренируешься';
                        if ($user['bolts'] < 500) $error[] = 'У тебя нет денег, чтобы оплатить тренировку';

                        if (isset($error)) {
                            foreach ($error as $err) {
                                $_SESSION['notify'] = $error;
                            }
                            $m->to('/gym/');
                        } else {
                            $db->query('update users set training = ?, whatTraining = ?, bolts = bolts - ? where id = ?', [time() + 3600, 'power', 500, $user['id']]);
                            $_SESSION['notify'][] = 'Тренировка по боксу началась. Окончание тренировки через ' . $m->downcounter(date('Y-m-j H:i:s', time() + 3600));
                            die(header('Location: /gym/'));
                        }
                    } elseif ($user['training'] and $user['whatTraining'] == 'power') {
                        //
                    } elseif ($user['training'] and $user['whatTraining'] != 'power') {
                        //
                    } else {
                        echo '<a class="href" href="/gym?box">Тренироваться</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Sparring -->
    <div class="flex-link">
        <div class="gym-card" onclick="" style="background-image: url('/files/gym/sparring.png');">
            <div class="content">
                <h6>
                    Спарринг<br />
                    <span class="quality-trash">Тренировка защиты от ударов</span>
                </h6>
                <div class="hover_content">
                    <div class="flex-container">
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about"><img src="/files/icons/bolts.png" alt=""> 500</div>
                            <div class="info-title">Стоимость</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about"><?php echo $user['shield']; ?></div>
                            <div class="info-title">Текущая защита (с учетом вещей)</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about">1 ед.</div>
                            <div class="info-title">Прибавит</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about">60 минут</div>
                            <div class="info-title">Длительность</div>
                        </div>
                    </div>
                    <?php
                    if (isset($_GET['sparring'])) {
                        if ($user['training'] > time()) $error[] = 'Ты уже тренируешься';
                        if ($user['bolts'] < 500) $error[] = 'У тебя нет денег, чтобы оплатить тренировку';

                        if (isset($error)) {
                            foreach ($error as $err) {
                                $_SESSION['notify'] = $error;
                            }
                            $m->to('/gym/');
                        } else {
                            $db->query('update users set training = ?, whatTraining = ?, bolts = bolts - ? where id = ?', [time() + 3600, 'shield', 500, $user['id']]);
                            $_SESSION['notify'][] = 'Спарринг начался. Окончание тренировки через ' . $m->downcounter(date('Y-m-j H:i:s', time() + 3600));
                            die(header('Location: /gym/'));
                        }
                    } elseif ($user['training'] and $user['whatTraining'] == 'shield') {
                        //
                    } elseif ($user['training'] and $user['whatTraining'] != 'shield') {
                        //
                    } else {
                        echo '<a class="href" href="/gym?sparring">Тренироваться</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Rope -->
    <div class="flex-link">
        <div class="gym-card" onclick="" style="background-image: url('/files/gym/rope.png');">
            <div class="content">
                <h6>
                    Скакалка<br />
                    <span class="quality-trash">Тренировка дыхания и бодрости</span>
                </h6>
                <div class="hover_content">
                    <div class="flex-container">
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about"><img src="/files/icons/rubles.png" alt=""> 100</div>
                            <div class="info-title">Стоимость</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about"><?php echo $user['max_energy']; ?></div>
                            <div class="info-title">Текущая энергия (с учетом вещей)</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about">1 ед.</div>
                            <div class="info-title">Прибавит</div>
                        </div>
                        <div class="flex-link m-5 quest-take">
                            <div class="info-about">90 минут</div>
                            <div class="info-title">Длительность</div>
                        </div>
                    </div>
                    <?php
                    if (isset($_GET['rope'])) {
                        if ($user['training'] > time()) $error[] = 'Ты уже тренируешься';
                        if ($user['rubles'] < 100) $error[] = 'У тебя нет денег, чтобы оплатить тренировку';

                        if (isset($error)) {
                            foreach ($error as $err) {
                                $_SESSION['notify'] = $error;
                            }
                            $m->to('/gym/');
                        } else {
                            $db->query('update users set training = ?, whatTraining = ?, rubles = rubles - ? where id = ?', [time() + 5400, 'max_energy', 100, $user['id']]);
                            $_SESSION['notify'][] = 'Тренировка началась. Окончание тренировки через ' . $m->downcounter(date('Y-m-j H:i:s', time() + 5400));
                            die(header('Location: /gym/'));
                        }
                    } elseif ($user['training'] and $user['whatTraining'] == 'max_energy') {
                        //
                    } elseif ($user['training'] and $user['whatTraining'] != 'max_energy') {
                        //
                    } else {
                        echo '<a class="href" href="/gym?rope">Тренироваться</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require '../../main/foot.php';

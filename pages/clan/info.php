<?php
require '../../main/main.php';

switch ($method) {
    default:
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        $cu = $clan->have($user['id']);
        if (!$cl) $m->to('/');
        $title = "Группировка «{$cl['name']}»";
        require '../../main/head.php';
        ?>
        <div class="flexpda main top">
            <div class="flexpda-image">
                <img class="top-img" src="/files/items/knife/1.png" title="" alt="" width="64px"/>
            </div>
            <div class="flexpda-content" style="padding-left: 5px;">
                <div class="forum-id">Штаб-квартира ОПГ <span class="access-2">«<?php echo $cl['name']; ?>»</span></div>
                <div class=" flex-container center">
                    <div class="flex-link outline">
                        <div class="info-about"><?php echo $cl['level']; ?></div>
                        <div class="info-title">Уровень</div>
                    </div>
                    <div class="flex-link outline">
                        <div class="info-about"><?php echo $cl['exp']; ?></div>
                        <div class="info-title">Опыт</div>
                    </div>
                    <div class="flex-link outline">
                        <div class="info-about"><?php echo $cl['count']; ?></div>
                        <div class="info-title">Участники</div>
                    </div>
                    <div class="flex-link outline">
                        <div class="info-about"><?php echo $u->getLogin($cl['id_lider'], true); ?></div>
                        <div class="info-title">Основатель</div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (isset($_GET['acceptInvite']) && !$clan->have($user['id'])) {
            if ($db->getCount('SELECT COUNT(`id`) FROM `groups_users` WHERE `id_group` = ? and `id_user` = ? and `accept` = ?', [$id, $user['id'], 0]) < 1) $m->pda(['Вы не получали приглашение в это ОПГ']);
            elseif ($cl['count'] + 1 > $cl['max_users']) $m->pda(['На данный момент в ОПГ максимальное количество бойцов']);
            else {
                $db->query('UPDATE `groups_users` SET `accept` = ?, `dateAdd` = ? WHERE `id_group` = ? and `id_user` = ?', [1, time(), $id, $user['id']]);
                $invite = $db->get('SELECT * FROM `groups_users` WHERE `id_group` = ? and `id_user` = ?', [$id, $user['id']]);
                $log = 'вступил в ОПГ по приглашению @id'.$invite['invite'];
                $db->query('INSERT INTO `groups_logs` (`id_user`, `id_group`, `text`, `time`, `types`) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, $log, time(), 'invite']);
                $_SESSION['notify'][] = 'Вы успешно вступили в ряды бойцов данного ОПГ';
                $m->to('/clan/'.$id);
            }
        }
        ?>
        <div class="block mv-5">
            <h1>Описание группировки</h1>
            <div class="small"><?= $cl['about']; ?></div>
        </div>
        <div class="flex-container">
            <div class="flex-link">
                <a href="/clan/<?php echo $cl['id']; ?>/members" class="href">Участники <span
                            class='count'><?php echo $cl['count']; ?></span>
                    <div class='clearfix'></div>
                </a>
            </div>
            <div class="flex-link">
                <a href="/clan/<?php echo $cl['id']; ?>/improve" class="href">Улучшения</a>
            </div>
            <?php if ($cu and $cu['id_group'] == $id): ?>
                <div class="flex-link">
                    <a href="/clan/<?php echo $cl['id']; ?>/bank" class="href">Общак</a>
                </div>
                <?php if ($cl['radio'] == 1):?>
                    <div class="flex-link">
                        <a href="/clan/<?php echo $cl['id']; ?>/chat" class="href">
                            Чат ОПГ
                            <span class='count'><?php echo $db->getCount('SELECT COUNT(`id`) FROM `groups_chat` WHERE `id_group` = ?', [$id]);?></span>
                            <div class='clearfix'></div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($cu and $cu['id_group'] == $id and $cu['rank'] > 2): ?>
                <div class="flex-link">
                    <a href="/clan/<?php echo $cl['id']; ?>/settings" class="href">Управление</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        require '../../main/foot.php';
        break;

    case 'improve':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $title = "Улучшения / ОПГ «{$cl['name']}»";
        require '../../main/head.php';
        $cu = $clan->have($user['id']);
        ?>
        <div class="flexpda main top">
            <div class="flexpda-image">
                <img class="top-img" src="/files/other/walkie-talkie.png" title="" alt="" width="64px"/>
                <div class="href center">
                    <?php echo($cl['radio'] ? 'есть' : 'нет'); ?>
                </div>
            </div>
            <div class="flexpda-content" style="padding-left: 5px;">
                <div class="forum-id access-2">Выделенный канал связи</div>
                <div class="block">
                    Добавляет выделенный канал рации, в котором могут общаться только члены ОПГ.<br/>
                    Можно решать вопросы, о которых не стоит никому знать <img src="/files/icons/repute.png" alt="">
                </div>
                <?php
                if ($cu['id_group'] == $id and $cu['rank'] > 2 and !$cl['radio']) {
                    if (isset($_GET['up']) and $_GET['up'] == 'radio' and isset($_GET['yes'])) {
                        if ($cl['rubles'] < 300) echo $m->pda(['Недостаточно рублей в общаке']);
                        else {
                            $db->query('UPDATE `groups` SET `radio` = ?, `rubles` = `rubles` - ? WHERE `id` = ?', [1, 300, $id]);
                            $db->query('INSERT INTO `groups_logs` (`id_user`, `id_group`, `text`, `time`, `types`) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, 'купил выделенный канал связи за 300 рублей из общака ОПГ', time(), 'improve']);
                            $_SESSION['notify'] = ['Успешная покупка'];
                            $m->to('/clan/' . $id . '/improve');
                        }
                    } elseif (isset($_GET['up']) and $_GET['up'] == 'radio') {
                        ?>
                        <div class="question m-5">
                            <div class="question-answer center">
                                <div class="small access-2">Данное действие нельзя будет отменить</div>
                                Вы действительно хотите купить выделенный канал связи для ОПГ?
                            </div>
                            <div class="question-option">
                                <a href="/clan/<?php echo $id; ?>/improve?up=radio&yes"
                                   class="href" style="margin-bottom: 1px;">Да</a>
                                <a href="/clan/<?php echo $id; ?>/improve" class="href">Нет</a>
                            </div>
                        </div>
                        <?php
                    } else {
                        echo '<a class="link" href="?up=radio">Улучшить за 300 <img src="/files/icons/rubles.png" alt=""> из общака ОПГ</a>';
                    }
                }
                ?>
            </div>
        </div>
        <div class="flexpda main top">
            <div class="flexpda-image">
                <img class="top-img" src="/files/other/mattress.png" title="" alt="" width="64px"/>
                <div class="href center">
                    <?php echo $cl['max_users']; ?>
                </div>
            </div>
            <div class="flexpda-content" style="padding-left: 5px;">
                <div class="forum-id access-2">Койко место</div>
                <div class="block">
                    Увеличивает количество спальных мест в штаб-квартире. <br/>
                    Позволяет вовлечь больше братков в свое ОПГ.
                </div>
                <?php
                if ($cu['id_group'] == $id and $cu['rank'] > 2) {
                    if (isset($_GET['up']) and $_GET['up'] == 'users' and isset($_GET['yes'])) {
                        if ($cl['rubles'] < (($cl['max_users'] + 1) * 30)) echo $m->pda(['Недостаточно рублей в общаке']);
                        elseif ($cl['max_users'] >= 20) $m->pda(['Временно нельзя иметь в ОПГ более 20 человек. <div class="quality-rare">Если желаете расширить лимит, то сообщите об этом администрации на форуме проекта</div>']);
                        else {
                            $db->query('update `groups` set `max_users` = `max_users` + ?, `rubles` = `rubles` - ? where `id` = ?', [1, (($cl['max_users'] + 1) * 30), $id]);
                            $db->query('insert into `groups_logs` (`id_user`, `id_group`, `text`, `time`, `types`) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, 'купил дополнительное койко место за ' . (($cl['max_users'] + 1) * 30) . ' рублей из общака ОПГ', time(), 'improve']);
                            $_SESSION['notify'] = ['Успешная покупка'];
                            $m->to('/clan/' . $id . '/improve');
                        }
                    } elseif (isset($_GET['up']) and $_GET['up'] == 'users') {
                        ?>
                        <div class="question m-5">
                            <div class="question-answer center">
                                <div class="small access-2">Данное действие нельзя будет отменить</div>
                                Вы действительно хотите купить дополнительное койко место для ОПГ?
                            </div>
                            <div class="question-option">
                                <a href="/clan/<?php echo $id; ?>/improve?up=users&yes"
                                   class="href" style="margin-bottom: 1px;">Да</a>
                                <a href="/clan/<?php echo $id; ?>/improve" class="href">Нет</a>
                            </div>
                        </div>
                        <?php
                    } else {
                        echo '<a class="link" href="?up=users">Улучшить за ' . (($cl['max_users'] + 1) * 30) . ' <img src="/files/icons/rubles.png" alt=""> из общака ОПГ</a>';
                    }
                }
                ?>
            </div>
        </div>
        <?php
        require '../../main/foot.php';
        break;


    // Список участников
    case 'members':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $title = "Участники группировки «{$cl['name']}»";
        require '../../main/head.php';
        $count = $db->getCount('SELECT count(id) FROM `groups_users` WHERE `id_group` = ? and `accept` = ?', [$id, 1]);

        $pg = new Game\Paginations(10, 'page');
        $pg->setTotal($count);
        $get = $db->getAll('SELECT * FROM `groups_users` WHERE `id_group` = ? and `accept` = ? ' . $pg->getLimit('`rank`', 'desc'), [$id, 1]);
        $cl = $clan->have($user['id']);
        foreach ($get as $key) {
            $player = $u->getInfo($key['id_user']);
            ?>
            <div class="block <?php echo($user['id'] == $key['id_user'] ? 'my-place' : NULL); ?>">
          <span class="pull-right">
            <a title="Профиль" class="link" style="display: inline-block;" href="/id<?php echo $key['id_user']; ?>"><img
                        src="/files/icons/user.png" width="30px"/></a>
          </span>
                <div class="flex-container center">
                    <div class="flex-link">
                        <div class="info-about"><?php echo $u->getLogin($key['id_user'], true); ?></div>
                        <div class="info-title"><?php echo $rankGrp[$key['rank']]; ?></div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about"><?php echo $key['exp_all']; ?></div>
                        <div class="info-title">Вклад влияния</div>
                    </div>
                    <?php
                    if ($cl and $cl['id_group'] == $id):
                        ?>
                        <div class="flex-link w-100"></div>
                        <div class="flex-link outline">
                            <div class="info-about"><?php echo $key['donate_bolts']; ?></div>
                            <div class="info-title">Вклад черного нала</div>
                        </div>
                        <div class="flex-link outline">
                            <div class="info-about"><?php echo $key['donate_rubles']; ?></div>
                            <div class="info-title">Вклад рублей</div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php
        }
        echo $pg->render();
        ?>
        <a href="/clan/<?= $id; ?>" class="href">Вернуться к группировке</a>
        <?php
        require '../../main/foot.php';
        break;

    case 'bank':
        $id = abs((int)$_GET['id']);
        if (isset($_POST['accept'])) {
            $_POST['amount'] = abs(intval($_POST['amount']));
            if (empty($_POST['amount']) or $_POST['amount'] < 1) $error[] = 'Введите сумму взноса';
            if (!is_numeric($_POST['amount'])) $error[] = 'Только цифровое значение суммы';
            if (!empty($_POST['amount']) and (empty($_POST['currency']) or ($_POST['currency'] != 'bolts' and $_POST['currency'] != 'rubles'))) $error[] = 'Неправильно выбрана валюта взноса';
            else {
                if ($user[$_POST['currency']] < $_POST['amount']) $error[] = 'У вас нет такой суммы';
            }

            if (empty($error)) {
                $db->query("UPDATE users SET {$_POST['currency']} = {$_POST['currency']} - ? WHERE id = ?", [$_POST['amount'], $user['id']]);
                $db->query("UPDATE groups SET {$_POST['currency']} = {$_POST['currency']} + ? WHERE id = ?", [$_POST['amount'], $id]);
                $db->query("UPDATE groups_users SET donate_{$_POST['currency']} = donate_{$_POST['currency']} + ? WHERE id_user = ? AND id_group = ?", [$_POST['amount'], $user['id'], $id]);
                $log = 'пополнил общак на ' . $_POST['amount'] . ' <img src="/files/icons/' . $_POST['currency'] . '.png" width="12px" alt="*">';
                $db->query('INSERT INTO groups_logs (id_user, id_group, text, time, types) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, $log, time(), 'bank']);
                echo $m->pda(['Вы успешно пополнили общак на ' . $_POST['amount'] . ' <img src="/files/icons/' . $_POST['currency'] . '.png" width="12px" alt="*">']);
            } else $m->pda($error);
        }
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $cu = $clan->have($user['id']);
        if (isset($cu) and $cu['id_group'] != $id) header('location: /');
        $title = "Общак группировки «{$cl['name']}»";
        require '../../main/head.php';
        ?>
        <div class="center mv-5">
            <img src="/files/icons/bank.png" alt="*">
        </div>
        <div class="flex-container center">
            <div class="flex-link outline">
                <div class="info-about"><?= $cl['bolts']; ?></div>
                <div class="info-title">Черный нал.</div>
            </div>
            <div class="flex-link outline">
                <div class="info-about"><?= $cl['rubles']; ?></div>
                <div class="info-title">Рублей</div>
            </div>
        </div>
        <div class="outline">
            <?php

            ?>
            <form method="post" class="small">
                Сумма взноза:<br/>
                <input type="number" name="amount" value="0" placeholder="Сумма взноса" min="0" class="w-100">
                Валюта взноза:<br/>
                <select name="currency" class="w-100">
                    <option value="bolts">черный нал.</option>
                    <option value="rubles">рубли</option>
                </select>
                <input type="submit" name="accept" value="Сделать взнос" class="w-100">
            </form>
        </div>
        <h1>Ваш взноз за все время</h1>
        <div class="flex-container center">
            <div class="flex-link outline">
                <div class="info-about"><?= $cu['bolts']; ?></div>
                <div class="info-title">Черный нал.</div>
            </div>
            <div class="flex-link outline">
                <div class="info-about"><?= $cu['rubles']; ?></div>
                <div class="info-title">Рублей</div>
            </div>
        </div>
        <a href="/clan/<?= $id; ?>/bank/history" class="href mv-5">Моя история взносов</a>
        <a href="/clan/<?= $id; ?>" class="href mv-5">Вернуться к группировке</a>
        <?php
        require '../../main/foot.php';
        break;
    // Управление группировкой
    case 'settings':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $cu = $clan->have($user['id']);
        if ((isset($cu) and $cu['id_group'] != $id) or (isset($cu) and $cu['rank'] < 3)) header('location: /clan/' . $id);
        $title = "Управление группировкой «{$cl['name']}»";
        require '../../main/head.php';
        if ($cl['id_lider'] == $user['id']) {
            ?>
            <h1>Функции основателя</h1>
            <div class="flex-container">
                <div class="flex-link">
                    <a href="/clan/<?= $id; ?>/settings/edit" class="href">Основные настройки</a>
                </div>
                <div class="flex-link">
                    <a href="/clan/<?= $id; ?>/settings/image" class="href">Эмблема группировки</a>
                </div>
                <div class="flex-link">
                    <a href="/clan/<?= $id; ?>/settings/bank" class="href">Общак группировки</a>
                </div>
                <div class="flex-link">
                    <a href="/clan/<?= $id; ?>/settings/logs" class="href">Просмотр логов</a>
                </div>
            </div>
            <?php
        }
        if ($cu['rank'] > 2) {
            ?>
            <h1>Функции заместителей</h1>
            <div class="flex-container">
                <div class="flex-link">
                    <a href="/clan/<?= $id; ?>/settings/members" class="href">Управление составом</a>
                </div>
                <div class="flex-link">
                    <a href="/clan/<?= $id; ?>/settings/forum" class="href">Управление форумом</a>
                </div>
            </div>
            <?php
        }
        ?>
        <a href="/clan/<?= $id; ?>" class="href m-5">Вернуться к группировке</a>
        <?php
        require '../../main/foot.php';
        break;

    // Основные настройки
    case 'edit':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $cu = $clan->have($user['id']);
        if ((isset($cu) and $cu['id_group'] != $id) or (isset($cu) and $cu['rank'] < 4)) header('location: /clan/' . $id);
        $title = "Основные настройки :: Управление группировкой «{$cl['name']}»";
        require '../../main/head.php';

        if (isset($_POST['aboutChange'])) {
            if (empty($_POST['about'])) $m->pda(['Описание не может быть пустым']);
            else {
                $db->query('UPDATE `groups` SET `about` = ? WHERE `id` = ?', [htmlspecialchars($_POST['about']), $id]);
                $_SESSION['notify'][] = 'Описание группировки успешно изменено';
                $m->to('/clan/'.$id.'/settings/edit');
            }
        }
        ?>
        <form method="post" class="block mv-5">
            <h1>Описание группировки</h1>
            <textarea name="about" rows="1" class="w-100"><?= $cl['about']; ?></textarea><br/>
            <input type="submit" value="Изменить описание" name="aboutChange">
        </form>
        <a href="/clan/<?= $id; ?>/settings" class="href mv-5">Вернуться к управлению</a>
        <a href="/clan/<?= $id; ?>" class="href mv-5">Вернуться к группировке</a>
        <?php
        require '../../main/foot.php';
        break;
    // Чат
    case 'chat':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $cu = $clan->have($user['id']);
        if (!$cu) $m->to('/clan/'.$id);
        if ($cu['id_group'] != $id && $user['access'] < 3) $m->to('/clan/'.$id);
        if ($cl['radio'] == 0) {
            $_SESSION['notify'][] = 'Нужно купить выделенный канал связи, чтобы открыть чат';
            $m->to('/clan/'.$id);
        }
        $title = "Чат ОПГ :: «{$cl['name']}»";
        require '../../main/head.php';
        if (isset($_POST['send'])){
            if (empty($_POST['text'])) $m->pda(['Введите сообщение']);
            else {
                $db->query('INSERT INTO `groups_chat` (`id_group`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, $user['id'], htmlspecialchars($_POST['text']), time()]);
                $_SESSION['notify'][] = 'Сообщение успешно отправлено';
                $m->to('/clan/'.$id.'/chat');
            }
        }
        ?>
        <form method="post" id="chat">
            <textarea class="w-100" id="text" name="text" placeholder="Введите сообщение..."></textarea><br/>
            <div class="flex-container">
                <div class="flex-link">
                    <input class="w-100" type="submit" id="send" name="send" value="Отправить"/>
                </div>
            </div>
        </form>
        <script>
            function bb(tag) {
                let Field = document.querySelector('#text');
                Field.value += tag;
            }
        </script>
        <?php
        $count = $db->getCount('SELECT COUNT(`id`) FROM `groups_chat` WHERE `id_group` = ?', [$id]);
        if ($count > 0) {
            $pg = new Game\Paginations (10, 'page');
            $pg->setTotal($count);
            $get = $db->getAll('SELECT * FROM `groups_chat` WHERE `id_group` = ? '.$pg->getLimit('id'), [$id]);
            foreach ($get as $key)
            {
                ?>
                <div class="block line-height">
                    <span class="pull-right">
                        <a title="Упомянуть" class="link" style="display: inline-block;" onclick="bb('@id<?php echo $key['id_user'];?>, ');"><img src="/files/icons/chat.png" width="16px" /></a> <a title="Профиль" class="link" style="display: inline-block;" href="/id<?php echo $key['id_user'];?>"><img src="/files/icons/user.png" width="16px" /></a>
                    </span>
                    <?php echo $u->getLogin($key['id_user'], true);?> <span class="small">говорит</span><br/>
                    <div><?php echo $m->message($key['message']);?></div>
                    <span class="small"><?php echo date("d.m.Y в H:i:s", $key['created_at']);?></span>
                </div>
                <?php
            }
            echo $pg->render();
        } else {
            $m->pda(['Сообщений нет.']);
        }
        echo '<a href="/clan/'.$id.'" class="href mv-5">Вернуться к группировке</a>';
        require '../../main/foot.php';
        break;
    // Просмотр логов
    case 'logs':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $cu = $clan->have($user['id']);
        if ((isset($cu) and $cu['id_group'] != $id) or (isset($cu) and $cu['rank'] < 4)) header('location: /clan/' . $id);
        $title = "Просмотр логов :: Управление группировкой «{$cl['name']}»";
        require '../../main/head.php';
        $logs = [
            'bank' => 'общак',
            'members' => 'участники',
            'settings' => 'настройки',
            'improve' => 'улучшения',
            'invite' => 'приглашения',
            'radio' => 'канал связи',
        ];

        $count = $db->getCount('SELECT count(id) FROM groups_logs WHERE id_group = ?', [$id]);

        if ($count > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($count);
            $get = $db->getAll('SELECT * FROM groups_logs WHERE id_group = ? ' . $pg->getLimit(), [$id]);
            foreach ($get as $key) {
                ?>
                <div class="block mv-5">
                    <?= $u->getLogin($key['id_user'], true); ?> <?= $m->message($key['text']); ?>
                    <div class="small"><?php echo date("d.m.Y в H:i:s", $key['time']); ?> / <span
                                class="access-3"><?= $logs[$key['types']]; ?></span></div>
                </div>
                <?php
            }
            echo $pg->render();
        } else echo $m->pda(['Логи пусты']);
        ?>
        <a href="/clan/<?= $id; ?>/settings" class="href mv-5">Вернуться к управлению</a>
        <a href="/clan/<?= $id; ?>" class="href mv-5">Вернуться к группировке</a>
        <?php
        require '../../main/foot.php';
        break;

    // Управление участниками
    case 'member':
        $id = abs((int)$_GET['id']);
        $cl = $clan->getInfo($id);
        if (!$cl) header('location: /');
        $cu = $clan->have($user['id']);
        if ((isset($cu) and $cu['id_group'] != $id) or (isset($cu) and $cu['rank'] < 4)) header('location: /clan/' . $id);
        $title = "Управление участниками :: Управление группировкой «{$cl['name']}»";
        require '../../main/head.php';

        if (isset($_GET['up']) and !empty($_GET['up'])) {
            $key = $clan->have($_GET['up']);
            if ($key['id_group'] != $id) $error[] = 'Этот игрок не состоит в Вашей группировке';
            if ($key['rank'] >= 2 and $cl['id_lider'] != $user['id']) $error[] = 'Повысить до заместителя может только основатель группировки';
            if ($key['rank'] == 3) $error[] = 'Игрок уже в максимальном звании';

            if (empty($error)) {
                $db->query('UPDATE `groups_users` SET `rank` = `rank` + 1 WHERE `id_user` = ? and `id_group` = ?', [$key['id_user'], $id]);
                $log = "повышает @id{$key['id_user']} до звания «{$rankGrp[$key['rank'] + 1]}»";
                $db->query('INSERT INTO groups_logs (id_user, id_group, text, time, types) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, $log, time(), 'members']);
                $_SESSION['notify'] = ['Успешно повышен в звании.'];
                die(header('Location: /clan/' . $id . '/settings/members'));
            } else $m->pda($error);
        } elseif (isset($_GET['down']) and !empty($_GET['down'])) {
            $key = $clan->have($_GET['down']);
            if ($key['id_group'] != $id) $error[] = 'Этот игрок не состоит в Вашей группировке';
            if ($key['rank'] >= $cu['rank']) $error[] = 'Нельзя понизить этого игрока';
            if ($key['rank'] == 0) $error[] = 'Игрок уже в минимальном звании';

            if (empty($error)) {
                $db->query('UPDATE `groups_users` SET `rank` = `rank` - 1 WHERE `id_user` = ? and `id_group` = ?', [$key['id_user'], $id]);
                $log = "понижает @id{$key['id_user']} до звания «{$rankGrp[$key['rank'] - 1]}»";
                $db->query('INSERT INTO groups_logs (id_user, id_group, text, time, types) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, $log, time(), 'members']);
                $_SESSION['notify'] = ['Успешно понижен в звании.'];
                die(header('Location: /clan/' . $id . '/settings/members'));
            } else $m->pda($error);
        } elseif (isset($_GET['kick']) and !empty($_GET['kick'])) {
            $key = $clan->have($_GET['kick']);
            if ($key['id_group'] != $id) $error[] = 'Этот игрок не состоит в Вашей группировке';
            if ($key['rank'] >= $cu['rank']) $error[] = 'Нельзя исключить этого игрока';

            if (empty($error)) {
                $db->query('DELETE FROM groups_users WHERE id_user = ? and id_group = ?', [$key['id_user'], $id]);
                $log = "исключает @id{$key['id_user']} из группировки";
                $db->query('INSERT INTO groups_logs (id_user, id_group, text, time, types) VALUES (?, ?, ?, ?, ?)', [$user['id'], $id, $log, time(), 'members']);
                $_SESSION['notify'] = ['Успешно исключен из ОПГ.'];
                die(header('Location: /clan/' . $id . '/settings/members'));
            } else $m->pda($error);
        }

        $count = $db->getCount('SELECT count(id) FROM groups_users WHERE id_group = ? and accept = ?', [$id, 1]);

        $pg = new Game\Paginations(10, 'page');
        $pg->setTotal($count);
        $get = $db->getAll('SELECT * FROM `groups_users` WHERE `id_group` = ? and `accept` = ? ' . $pg->getLimit('`rank`', 'desc'), [$id, 1]);
        foreach ($get as $key) {
            $player = $u->getInfo($key['id']);
            ?>
            <div class="outline m-5">
                <div class="block mh-5">
                    <?= $rankGrp[$key['rank']] ?> <?= $u->getLogin($key['id_user'], true); ?> в группировке с <span
                            class="small"><?php echo date("d.m.Y", $key['dateAdd']); ?></span>
                </div>
                <div class="flex-container">
                    <div class="flex-link">
                        <div class="href">Репутации сегодня <span class="count"><?= $key['exp_today']; ?></span></div>
                    </div>
                    <div class="flex-link">
                        <div class="href">Репутации всего <span class="count"><?= $key['exp_all']; ?></span></div>
                    </div>
                    <div class="flex-link">
                        <div class="href">Вклад черного нала <span class="count"><?= $key['donate_bolts']; ?></span></div>
                    </div>
                    <div class="flex-link">
                        <div class="href">Вклад рублей <span class="count"><?= $key['donate_rubles']; ?></span></div>
                    </div>
                </div>
                <?php if ($key['id_user'] != $cl['id_lider']): ?>
                    <hr/>
                    <div class="flex-container">
                        <?php if ($key['rank'] < 3): ?>
                            <div class="flex-link">
                                <a href="?up=<?= $key['id_user']; ?>" class="href">Повысить до
                                    «<?= $rankGrp[$key['rank'] + 1]; ?>»</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($key['rank'] > 0): ?>
                            <div class="flex-link">
                                <a href="?down=<?= $key['id_user']; ?>" class="href">Понизить до
                                    «<?= $rankGrp[$key['rank'] - 1]; ?>»</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($key['rank'] < $cu['rank']): ?>
                            <div class="flex-link">
                                <a href="?kick=<?= $key['id_user']; ?>" class="href">Исключить</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
        echo $pg->render();
        ?>
        <a href="/clan/<?= $id; ?>/settings" class="href m-5">Вернуться к управлению</a>
        <a href="/clan/<?= $id; ?>" class="href m-5">Вернуться к группировке</a>
        <?php
        require '../../main/foot.php';
        break;
    case 'rating':
        $title = 'Влиятельные ОПГ';
        require '../../main/head.php';
        $count = $db->getCount('SELECT count(`id`) FROM `groups`', []);
        if ($count > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($count);

            $get = $db->getAll('SELECT `id` FROM `groups` ' . $pg->getLimit('`exp`', 'desc'), []);
            foreach ($get as $g) {
                $cl = $clan->getInfo($g['id']);
                ?>
                <div class="block">
                    <a href="/clan/<?php echo $cl['id'];?>" class="flex-container">
                        <div class="flex-link">
                            <div class="info-about"><?php echo $cl['name']; ?></div>
                            <div class="info-title">ОПГ</div>
                        </div>
                        <div class="flex-link center">
                            <div class="info-about"><?php echo $cl['level']; ?></div>
                            <div class="info-title">Уровень</div>
                        </div>
                        <div class="flex-link center">
                            <div class="info-about"><?php echo $cl['exp']; ?></div>
                            <div class="info-title">Репутация</div>
                        </div>
                        <div class="flex-link center">
                            <div class="info-about"><?php echo $cl['count']; ?> из <?php echo $cl['max_users']; ?></div>
                            <div class="info-title">Участников</div>
                        </div>
                        <div class="flex-link center">
                            <div class="info-about"><?php echo $u->getLogin($cl['id_lider'], false); ?></div>
                            <div class="info-title">Лидер</div>
                        </div>
                    </a>
                    <div class="clearfix"></div>
                </div>
                <?php
            }
        } else {
            $m->pda(['По сводкам из новостей - ОПГ в городе нет']);
        }
        require '../../main/foot.php';
        break;
}

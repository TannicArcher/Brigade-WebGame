<?php
require '../../main/main.php';
switch ($method) {
    default:
        $title = 'Мобила';
        require '../../main/head.php';
        $sms = $db->getCount('SELECT count(id) FROM phone_sms WHERE id_to = ? and read_at = ?', [$user['id'], 0]);
        $notify = $db->getCount('SELECT count(id) FROM phone_notify WHERE id_user = ? and read_at = ?', [$user['id'], 0]);
        ?>
        <div class="pda-message w-100 center">
            <div class="pda-message__title">Экран телефона</div>
            <div class="pda-message__item">
                <?php echo date("Сегодня d.m.Y, время: H:i:s", time()); ?>
            </div>
        </div>
        <div class="grid">
            <div class="four columns">
                <a href="/phone/contacts" class="href">Контакты</a>
            </div>
            <div class="four columns">
                <a href="/phone/sms" class="href">
                    SMS
                    <span class="count"><?php echo $sms; ?></span>
                    <div class="clearfix"></div>
                </a>
            </div>
            <div class="four columns">
                <a href="/phone/notify" class="href">
                    Уведомления
                    <span class="count"><?php echo $notify; ?></span>
                    <div class="clearfix"></div>
                </a>
            </div>
            <div class="four columns">
                <a href="/forum/category/6" class="href">Оператор поддержки</a>
            </div>
            <div class="four columns">
                <a href="/phone/settings" class="href">Настройки</a>
            </div>
            <div class="four columns">
                <a href="/phone/balance" class="href">
                    Баланс
                    <span class="count"><?php echo $user['rubles']; ?> РУБ.</span>
                    <div class="clearfix"></div>
                </a>
            </div>
        </div>
        <?php
        require '../../main/foot.php';
        break;
    case 'contacts':
        $title = 'Телефон :: Контакты';
        require '../../main/head.php';
        ?>
        <a href="/referral" class="href">Программа «Позови братка»</a>
        <?php
        require '../../main/foot.php';
        break;

    case 'balance':
        $title = 'Телефон :: пополнение баланса';
        require '../../main/head.php';
        if (isset($_GET['buy'])) {
            switch ($_GET['buy']) {
                default:
                    $m->pda(['Ошибка в выборе рублей для покупки']);
                    break;
                case 1:
                    $wk->createPayment($user['id'], 50.00, 50);
                    break;
                case 2:
                    $wk->createPayment($user['id'], 98.00, 100);
                    break;
                case 3:
                    $wk->createPayment($user['id'], 285.00, 300);
                    break;
                case 4:
                    $wk->createPayment($user['id'], 465.00, 500);
                    break;
                case 5:
                    $wk->createPayment($user['id'], 900.00, 1000);
                    break;
            }
        }
        ?>
        <div class="flex-container" style="align-items: flex-start">
            <div class="card">
                <div class="card__title center">
                    Пополнение баланса
                </div>
                <div class="flexpda main top">
                    <div class="flexpda-image center">
                        <div class="top-img main"><img src="/files/icons/rubles/01.png" alt=""></div>
                    </div>
                    <div class="flexpda-content" style="padding-left: 10px;">
                        <div class="forum-id access-4">
                            50 игровых рублей
                        </div>
                        <a href="?buy=1" class="href">Купить за 50 рублей</a>
                    </div>
                </div>
                <div class="flexpda main top">
                    <div class="flexpda-image center">
                        <img src="/files/icons/rubles/02.png" class="top-img main" alt="">
                    </div>
                    <div class="flexpda-content" style="padding-left: 10px;">
                        <div class="forum-id access-4">
                            100 игровых рублей
                        </div>
                        <a href="?buy=2" class="href">Купить 98 рублей <span class="count">-2%</span></a>
                    </div>
                </div>
                <div class="flexpda main top">
                    <div class="flexpda-image center">
                        <img src="/files/icons/rubles/03.png" class="top-img main" alt="">
                    </div>
                    <div class="flexpda-content" style="padding-left: 10px;">
                        <div class="forum-id access-4">
                            300 игровых рублей
                        </div>
                        <a href="?buy=3" class="href">Купить за 285 рублей <span class="count">-5%</span></a>
                    </div>
                </div>
                <div class="flexpda main top">
                    <div class="flexpda-image center">
                        <img src="/files/icons/rubles/04.png" class="top-img main" alt="">
                    </div>
                    <div class="flexpda-content" style="padding-left: 10px;">
                        <div class="forum-id access-4">
                            500 игровых рублей
                        </div>
                        <a href="?buy=4" class="href">Купить за 465 рублей <span class="count">-7%</span></a>
                    </div>
                </div>
                <div class="flexpda main top">
                    <div class="flexpda-image center">
                        <img src="/files/icons/rubles/05.png" class="top-img main" alt="">
                    </div>
                    <div class="flexpda-content" style="padding-left: 10px;">
                        <div class="forum-id access-4">
                            1000 игровых рублей
                        </div>
                        <a href="?buy=5" class="href">Купить за 900 рублей <span class="count">-10%</span></a>
                    </div>
                </div>
                <div class="outline center mv-5">
                    1 <img src="/files/icons/rubles.png" alt="Руб."> = 1 реальному рублю
                </div>
            </div>
            <?php if ($user['access'] > 3):?>
                <div class="card">
                    <div class="card__title center">
                        FreeKassa
                    </div>
                </div>
            <?php endif;?>
        </div>
        <?php
        require '../../main/foot.php';
        break;

    case 'sms':
        $title = 'Телефон :: входящие SMS';
        require '../../main/head.php';
        ?>
        <div class="flex-container">
            <div class="flex-link"><a href="/phone/sms/new" class="href">Написать SMS</a></div>
            <div class="flex-link"><a href="/phone/sms" class="href">Входящие SMS</a></div>
            <div class="flex-link"><a href="/phone/sms/to" class="href">Исходящие SMS</a></div>
        </div>
        <?php
        $count = $db->getCount('SELECT COUNT(`id`) FROM `phone_sms` WHERE `id_to` = ?', [$user['id']]);
        $pg = new Game\Paginations (10, 'page');
        $pg->setTotal($count);
        $get = $db->getAll('SELECT * FROM `phone_sms` WHERE `id_to` = ? ' . $pg->getLimit('read_at, created', 'DESC'), [$user['id']]);
        if ($count > 0) {
            foreach ($get as $key) {
                ?>
                <div class="main mv-5">
                    <div class="flex-container">
                        <div class="flex-link" style="flex-grow: 10">
                            <a href="/phone/sms/<?php echo $key['id']; ?>" class="link">
                                SMS от <?php echo $u->getLogin($key['id_user']); ?>
                                <?php echo(!$key['read_at'] ? '<div class="count">новое сообщение</div>' : ''); ?>
                            </a>
                        </div>
                        <div class="flex-link center">
                            <div class="outline">
                                <?php echo date("d.m.Y в H:i:s", $key['created']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo $pg->render();
        } else $m->pda(['Сообщений нет']);
        echo '<a href="/phone/" class="href">Вернуться к телефону</a>';
        require '../../main/foot.php';
        break;

    case 'sms_to':
        $title = 'Телефон :: исходящие SMS';
        require '../../main/head.php';
        ?>
        <div class="flex-container center">
            <div class="flex-link"><a href="/phone/sms/new" class="href">Написать SMS</a></div>
            <div class="flex-link"><a href="/phone/sms" class="href">Входящие SMS</a></div>
            <div class="flex-link"><a href="/phone/sms/to" class="href">Исходящие SMS</a></div>
        </div>
        <?php
        $count['to'] = $db->getCount('SELECT COUNT(`id`) FROM `phone_sms` WHERE `id_user` = ?', [$user['id']]);
        $pg = new Game\Paginations(10, 'page');
        $pg->setTotal($count['to']);
        $get = $db->getAll('SELECT * FROM `phone_sms` WHERE `id_user` = ? ' . $pg->getLimit('created', 'DESC'), [$user['id']]);
        if ($count['to'] > 0) {
            foreach ($get as $key) {
                ?>
                <div class="main mv-5">
                    <div class="flex-container">
                        <div class="flex-link" style="flex-grow: 10">
                            <a href="/phone/sms/<?php echo $key['id']; ?>" class="link">
                                SMS для <?php echo $u->getLogin($key['id_to']); ?>
                                <?php echo(!$key['read_at'] ? '<div class="count">еще не прочитано</div>' : ''); ?>
                            </a>
                        </div>
                        <div class="flex-link center">
                            <div class="outline">
                                <?php echo date("d.m.Y в H:i:s", $key['created']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo $pg->render();
        } else $m->pda(['Сообщений нет']);
        echo '<a href="/phone/" class="href">Вернуться к телефону</a>';
        require '../../main/foot.php';
        break;
    case 'sms_new':
        $title = 'Телефон :: написать SMS';
        require '../../main/head.php';
        if (isset($_POST['send'])) {
            $form['id'] = abs(intval($_POST['id']));
            $form['text'] = trim($_POST['text']);
            if (empty(trim($form['text']))) $error[] = 'Введите текст сообщения';
            if (empty($form['id'])) $error[] = 'Введите ID номер собеседника';
            elseif ($form['id'] == $user['id']) $error[] = 'Нельзя отправить SMS самому себе';
            elseif (!$to = $db->get('SELECT `id` FROM `users` WHERE `id` = ?', [$form['id']])) $error[] = 'Нет такого пользователя';
            elseif ($form['id'] == 2) $error[] = 'Нельзя писать сообщения системному боту';
            elseif ($user['level'] < 3) $error[] = 'Писать СМС сообщения разрешено только с 3 уровня.';

            if (!isset($error)) {
                $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [$user['id'], $form['id'], time(), htmlspecialchars($form['text'])]);
                $_SESSION['notify'][] = 'Сообщение успешно отправлено игроку ' . $u->getLogin($form['id']);
                $m->to('/phone/sms');
            } else {
                $_SESSION['form']['text'] = $form['text'];
                $m->pda($error);
            }
        }
        ?>
        <form method="post" name="sms">
            <label for="id">ID номер</label>
            <input type="text" name="id" id="id" placeholder="Введите ID"
                   value="<?php echo(isset($_GET['to']) ? $_GET['to'] : NULL); ?>"/>
            <label for="text">Текст сообщения</label>
            <textarea name="text" id="text"
                      rows="5"><?php echo(isset($_SESSION['form']['text']) ? $_SESSION['form']['text'] : NULL); ?></textarea>
            <input type="submit" name="send" value="Отправить сообщение">
        </form>
        <?php
        if (isset($_SESSION['form']['text'])) unset($_SESSION['form']['text']);
        echo '<a href="/phone/sms" class="href mv-5">Вернуться к SMS</a>';
        echo '<a href="/phone" class="href">Вернуться к телефону</a>';
        require '../../main/foot.php';
        break;
    
    case 'sms_view':
        $id = abs((int)$_GET['id']);
        $sms = $db->get('select * from phone_sms where id = ? and (id_user = ? or id_to = ?)', [$id, $user['id'], $user['id']]);
        if (!$sms) header('location: /phone/sms');
        $title = 'Телефон :: просмотр SMS';
        require '../../main/head.php';
        if ($sms['id_to'] == $user['id'] && $sms['read_at'] == 0) {
            $db->query('UPDATE `phone_sms` SET `read_at` = ? WHERE id = ?', [1, $id]);
        }
        ?>
        <div class="flexpda main top">
            <div class="flexpda-image center">
                <a href="/id<?php echo $sms['id_user']; ?>">
                    <div class="top-img main">
                        <img src="/files/icons/user.png" alt="">
                    </div>
                </a>
                <?php echo($sms['id_to'] == $user['id'] && $sms['read_at'] == 0 ? '<div class="href">NEW</div>' : NULL); ?>
            </div>
            <div class="flexpda-content" style="padding-left: 10px;">
                <div class="forum-id">
                    <?php echo($sms['id_user'] == $user['id'] ? 'Ваше сообщение для ' . $u->getLogin($sms['id_to'], true) : 'Сообщение от ' . $u->getLogin($sms['id_user'], true)); ?>
                </div>
                <div class="access-2">
                    <?php echo $m->message($sms['message']); ?>
                </div>
                <span class="small">Отправлено <?php echo date("d.m.Y в H:i:s", $sms['created']); ?></span>
            </div>
        </div>
        <?php if ($sms['id_to'] == $user['id']):
            if ($attach = $db->getAll('SELECT * FROM `phone_sms_attach` WHERE `id_sms` = ?', [$sms['id']])) {
                $canTake = false;
                ?>
                <div class="block">
                    <h1>К сообщению прикреплено:</h1>
                    <div class="flex-container">
                        <?php foreach ($attach as $item):?>
                            <div class="flex-link">
                                <?php
                                switch ($item['attachType']){
                                    case 'rubles':
                                        $at['image'] = '/files/icons/rubles.png';
                                        $at['link'] = $item['attachAmount'];
                                        $at['name'] = 'рубли';
                                        break;
                                    case 'bolts':
                                        $at['image'] = '/files/icons/bolts.png';
                                        $at['link'] = $item['attachAmount'];
                                        $at['name'] = 'ч.нал';
                                        break;
                                    case 'repute':
                                        $at['image'] = '/files/icons/repute.png';
                                        $at['link'] = $item['attachAmount'];
                                        $at['name'] = 'репутация';
                                        break;
                                    case 'equip':
                                        $at['image'] = '/files/icons/cupboard.png';
                                        $at['link'] = $wpn->link($item['attachID'], true);
                                        $at['name'] = 'экипировка';
                                        break;
                                    case 'object':
                                        $at['image'] = '/files/icons/cupboard.png';
                                        $at['link'] = $obj->link($item['attachID'], false).' '.$item['attachAmount'].' ед.';
                                        $at['name'] = 'предмет';
                                        break;
                                    case 'userEquip':
                                        $at['image'] = '/files/icons/cupboard.png';
                                        $at['link'] = $wpn->link($item['attachID'], false);
                                        $at['name'] = 'экипировка';
                                        break;
                                }
                                if ($item['take'] == 0) $canTake = true;
                                ?>
                                    <div class="flexpda-content" style="padding-left: 5px;">
                                        <div class="main">
                                            <img src="<?php echo $at['image'];?>">
                                            <strong class="access-3"><?php echo $at['link'];?></strong>
                                        </div>
                                        <div class="main">
                                            <?php echo $at['name'].' '.($item['take'] == 1 ? '(взято)':null);?> 
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    if ($canTake) {
                        if (isset($_GET['take'])) {
                            if($take = $db->getAll('SELECT * FROM `phone_sms_attach` WHERE `id_sms` = ? and `take` = ?', [$sms['id'], 0])) {
                                foreach ($take as $t) {
                                    switch($t['attachType']) {
                                        case 'rubles':
                                            $u->giveRubles($user['id'], $t['attachAmount']);
                                        break;
                                    case 'bolts':
                                            $u->giveBolts($user['id'], $t['attachAmount']);
                                        break;
                                    case 'repute':
                                            $u->giveRepute($user['id'], $t['attachAmount']);
                                        break;
                                    case 'equip':
                                            $wpn->give($t['attachID'], $user['id']);
                                        break;
                                    case 'object':
                                            $obj->give($t['attachID'], $user['id'], (empty($t['attachAmount']) or $t['attachAmount'] == 1 ? 1 : $t['attachAmount']));
                                        break;
                                    case 'userEquip':
                                            $wpn->change($t['attachID'], $user['id']);
                                        break;
                                    }
                                }
                                $db->query('UPDATE `phone_sms_attach` SET `take` = ? WHERE `id_sms` = ? and `take` = ?', [1, $sms['id'], 0]);
                                $_SESSION['notify'][] = 'Вы забрали все предметы прикрепленные к письму';
                                $m->to('/phone/sms/'.$sms['id']);
                            }
                        }
                        echo '<a href="?take" class="href mv-5">Забрать все</a>';
                    }
                    ?>

                </div>
                <?php
            }
            if (isset($_POST['send'])) {
                $msg = trim(htmlspecialchars($_POST['message']));
                if (empty($msg)) $error[] = 'Введите текст сообщения';
                if ($sms['id_user'] == 2) $error[] = 'Нельзя писать сообщения системному боту';
                if ($user['level'] < 3) $error[] = 'Отправлять СМС сообщения разрешено только с 3 уровня.';

                if (empty($error)) {
                    $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`, `answer`) VALUES (?, ?, ?, ?,? )', [$user['id'], $sms['id_user'], time(), $msg, $sms['id']]);
                    $_SESSION['notify'][] = 'Сообщение успешно отправлено игроку ' . $u->getLogin($sms['id_user']);
                    $m->to('/phone/sms');
                } else {
                    $m->pda($error);
                }
            }
        ?>
        <div class="block">
            <h1>Ответить на SMS</h1>
            <form method="post">
                <textarea name="message" cols="3" minlength="5" required=""
                          placeholder="Введите сообщение..."></textarea>
                <input type="submit" value="Отправить" name="send" class="w-100">
            </form>
        </div>
        <?php
        endif;
        echo '<a href="/phone/sms" class="href mv-5">Вернуться к SMS</a>';
        echo '<a href="/phone" class="href">Вернуться к телефону</a>';
        require '../../main/foot.php';
        break;

    case 'notify':
        $title = 'Телефон :: просмотр уведомлений';
        require '../../main/head.php';
        $count['notify'] = $db->getCount('SELECT COUNT(`id`) FROM `phone_notify` WHERE `id_user` = ?', [$user['id']]);
        $count['notifyNew'] = $db->getCount('SELECT COUNT(`id`) FROM `phone_notify` WHERE `id_user` = ? and `read_at` = ?', [$user['id'], 0]);
        $pg = new Game\Paginations(10, 'page');
        $pg->setTotal($count['notify']);
        $get = $db->getAll('SELECT * FROM `phone_notify` WHERE `id_user` = ? ' . $pg->getLimit('read_at, created_at', 'DESC'), [$user['id']]);
        if ($count['notify'] > 0) {
            foreach ($get as $key) {
                ?>
                <div class="block line-height">
                    <span class="small <?php echo ($key['read_at'] == 0 ? 'access-2' : null);?>">Уведомление</span><br>
                    <div>
                        <?php echo $m->message($key['text']);?>
                    </div>
                    <?php echo (!empty($key['linkAccept']) ? '<a href="'.$key['linkAccept'].'" class="href mv-5">Принять</a>':null); ?>
                    <span class="small"><?php echo date("d.m.Y в H:i:s", $key['created_at']);?></span>
                </div>
                <?php
            }
            echo $pg->render();
            if ($count['notifyNew'] > 0) $db->query('UPDATE `phone_notify` SET `read_at` = ? WHERE `id_user` = ? and `read_at` = ?', [1, $user['id'], 0]);
        } else $m->pda(['Уведомлений нет']);
        echo '<a href="/phone" class="href">Вернуться к телефону</a>';
        require '../../main/foot.php';
        break;

    case 'settings':
        $title = 'Настройки :: телефон';
        require '../../main/head.php';
        ?>
        <div class="block">
            <h1>Электронная почта</h1><br/>
            <form method="post">
                <?php if ($db->getCount('SELECT COUNT(`id`) FROM `emails` WHERE `id_user` = ?', [$user['id']]) > 0 && empty($user['email'])):?>
                    <?php
                    if (isset($_GET['confirm']) and empty($user['email'])) {
                        $getEmail = $db->get('SELECT * FROM `emails` WHERE `id_user` = ?', [$user['id']]);
                        if ($_GET['confirm'] == $getEmail['emailToken']) {
                            $db->query('UPDATE `users` SET `email` = ? WHERE `id` = ?', [$getEmail['email'], $user['id']]);
                            $db->query('DELETE FROM `emails` WHERE `id_user` = ?', [$user['id']]);
                            $u->giveRubles($user['id'], 50);
                            $_SESSION['notify'][] = 'Вам отправлен бонус в виде 50 рублей за привязку почтового ящика.';
                            $m->to('/phone/settings');
                        } else {
                            $m->pda(['Ошибка']);
                        }
                    } else {
                        $m->pda(['Письмо с подтверждением отправлено на почту, что была Вами указана. Если не письмо не нашлось, попробуйте проверить папку "СПАМ"']);
                    }
                    ?>
                <?php elseif (!empty($user['email'])): ?>
                    <input type="text" value="<?php echo substr($user['email'], 0, 3).'****'.substr($user['email'], strpos($user['email'], "@")); ?>" disabled>
                    <span class="access-2">
                        Вы успешно привязали почту к аккаунту.
                    </span>
                <?php else: ?>
                    <?php
                    if (isset($_POST['emailConfirm'])){
                        if (empty($_POST['email']) || empty($_POST['reemail'])) $error[] = 'Заполните оба поля';
                        elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !filter_var($_POST['reemail'], FILTER_VALIDATE_EMAIL)) $error[] = 'Неправильно заполнены поля';
                        elseif ($_POST['email'] != $_POST['reemail']) $error[] = 'Поля должны быть одинаковыми. Проверьте введеные данные.';
                        elseif ($db->getCount('SELECT COUNT(`id`) FROM `users` WHERE `email` = ?', [$_POST['email']]) > 1) $error[] = 'Данный почтовый ящик уже привязан к другому аккаунту';
                        elseif ($db->getCount('SELECT COUNT(`id`) FROM `emails` WHERE `id_user` = ?', [$user['id']]) > 0) $error[] = 'Подтверждение почтового ящика уже отправлено вам';
                    
                        if (empty($error)) {
                            unset($_SESSION['form']['email']);
                            unset($_SESSION['form']['reemail']);
                            $hash = md5($user['id'].$_POST['email'].time());
                            $db->query('INSERT INTO `emails` (`id_user`, `email`, `emailToken`) VALUES (?, ?, ?)', [$user['id'], $_POST['email'], $hash]);
                            require '../../main/classes/PHPMailer/PHPMailer.php';
                            require '../../main/classes/PHPMailer/Exception.php';
                            require '../../main/classes/PHPMailer/SMTP.php';
                            
                            // Создаем письмо
                            $mail = new PHPMailer\PHPMailer\PHPMailer();
                            $mail->CharSet = 'UTF-8';
                            $mail->setFrom('no-reply@brigada.mobi', 'BRIGADA.MOBI / Системный бот');     // от кого
                            $mail->addAddress($_POST['email'], 'Пользователь');    // кому
                            
                            $mail->Subject = 'Привязка почтового ящика';
                            $mail->msgHTML('
                            <html>
                            <body>
                                <h1>Здравствуйте, '.$u->getLogin($user['id']).'.</h1>
                                <p>
                                    Ссылка для привязки данного почтового адреса к аккаунту: <a href="https://brigada.mobi/email/'.$hash.'">подтвердить</a><br/>
                                    Либо перейдите по адресу: https://brigada.mobi/email/'.$hash.'
                                </p>
                                <small>
                                    В случае, если Вы не запрашивали подтверждение аккаунта в нашей игре, не переходите по этой ссылке и проигнорируйте данное письмо.
                                </small>
                            </body>
                            </html>
                            ');
                            
                            $mail->send();
                            $_SESSION['notify'][] = 'На почтовый адрес '.$_POST['email'].' отправлено письмо с ссылкой для подтверждения привязки аккаунта.';
                            $m->to('/phone/settings');
                        } else {
                            $_SESSION['notify'] = $error;
                            $_SESSION['form']['email'] = $_POST['email'];
                            $_SESSION['form']['reemail'] = $_POST['reemail'];
                            $m->to('/phone/settings');
                        }
                    }
                    ?>
                    <div class="flex-container">
                        <div class="flex-link">
                            <label for="email">Введите свой почтовый адрес</label>
                            <input type="text" name="email" id="email" value="<?php echo (isset($_SESSION['form']['email']) ? $_SESSION['form']['email'] : null);?>" placeholder="example@brigada.mobi" required/>
                        </div>
                        <div class="flex-link">
                            <label for="reemail">Введите свой почтовый адрес еще раз</label>
                            <input type="text" name="reemail" id="reemail" value="<?php echo (isset($_SESSION['form']['reemail']) ? $_SESSION['form']['reemail'] : null);?>" placeholder="example@brigada.mobi" required/>
                        </div>
                    </div>
                    <div class="main center access-1">Привяжи почтовый ящик к аккаунту и получи 50 <img src="/files/icons/rubles.png" alt=""> на свой счет.</div>
                    <span class="small access-2">Почтовый ящик требуется для восстановления персонажа, в случае утери пароля.</span><br>
                    <span class="small quality-rare">Изменить почтовый адрес в дальнейшем будет нельзя.</span>
                    <input type="submit" value="Привязать почтовый адрес" name="emailConfirm">
                <?php endif;?>
            </form>
        </div>
        <div class="block">
            <h1>Смена пароля</h1><br/>
            <?php
            if (isset($_POST['changePassword'])){
                $form['password'] = trim($_POST['password']);
                $form['newPassword'] = trim($_POST['newPassword']);
                $form['newPasswordRetry'] = trim($_POST['newPasswordRetry']);
                if (empty($form['password']) || empty($form['newPassword']) || empty($form['newPasswordRetry'])) $error[] = 'Все поля обязательны для заполнения';
                elseif ($form['newPassword'] != $form['newPasswordRetry']) $error[] = 'Новые пароли не совпадают';
                elseif (!password_verify($form['password'], $user['password'])) $error[] = 'Неправильно введен текущий пароль';
                
                if (empty($error)){
                    $password = password_hash($form['newPassword'], PASSWORD_BCRYPT);
                    $db->query('UPDATE `users` SET `password` = ? WHERE `id` = ?', [$password, $user['id']]);
                    setcookie('password', $password, time() + 60 * 60 * 24 * 30, '/');
                    $_SESSION['notify'][] = 'Пароль успешно изменен.';
                    $m->to('/phone/settings');
                } else $m->pda($error);
            }
            ?>
            <form method="post">
                <label for="password">Текущий пароль:</label>
                <input type="password" name="password" id="password" required>
                <div class="flex-container">
                    <div class="flex-link">
                        <label for="newPassword">Новый пароль:</label>
                        <input type="password" name="newPassword" id="newPassword" required>
                    </div>
                    <div class="flex-link">
                        <label for="newPasswordRetry">Новый пароль еще раз:</label>
                        <input type="password" name="newPasswordRetry" id="newPasswordRetry" required>
                    </div>
                </div>
                <input type="submit" value="Сменить пароль" name="changePassword">
            </form>
        </div>
        <div class="block">
            <h1>Смена никнейма</h1><br/>
            <?php
            $count['change'] = $db->getCount('SELECT COUNT(`id`) FROM `oldLogin` WHERE `id_user` = ?', [$user['id']]);
            if (isset($_POST['changeLogin'])){
                $form['login'] = trim($_POST['login']);
                if (empty($form['login'])) $error[] = 'Введите нужный никнейм';
                elseif (!preg_match('/(*UTF8)^[a-zA-ZА-Яа-яЁё][a-zA-ZА-Яа-яЁё0-9-_\.]{1,20}$/', $form['login'])) $error[] = 'Никнейм не может быть менее 2 и более 20 символов, не иметь пробелы или иметь число первым символом';
                elseif ($db->getCount('SELECT COUNT(`id`) FROM `users` WHERE `login` = ?', [$form['login']]) > 0) $error[] = 'Этот никнейм уже кем-то занят';
                
                if ($count['change'] > 0 && $user['rubles'] < 30) $error[] = 'Недостаточно рублей на счету';
                
                if (empty($error)){
                    $db->query('INSERT INTO `oldLogin` (`id_user`, `oldLogin`, `newLogin`, `updated_at`) VALUES (?, ?, ?, ?)', [$user['id'], $user['login'], $form['login'], time()]);
                    $db->query('UPDATE `users` SET `login` = ? WHERE `id` = ?', [$form['login'], $user['id']]);
                    if ($count['change'] > 0) $u->takeRubles($user['id'], 30);
                    $_SESSION['notify'][] = 'Вы успешно сменили свой никнейм на '.$form['login'];
                    $m->to('/phone/settings');
                } else $m->pda($error);
            }
            ?>
            <form method="post">
                <label for="login">Новый логин:</label>
                <input type="text" name="login" id="login" required>
                <input type="submit" value="Сменить никнейм" name="changeLogin">
            </form>
            <div class="main center access-2">
                Стоимость смены никнейма - <?php echo ($count['change'] > 0 ? '30 <img src="/files/icons/rubles.png" /> рублей' : '1 раз бесплатно');?>
            </div>
        </div>
        <?php
        echo '<a href="/phone" class="href mv-5">Вернуться к телефону</a>';
        require '../../main/foot.php';
        break;
}
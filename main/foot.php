<?php
if (!$user['save'] and $_SERVER['PHP_SELF'] != '/pages/account/save.php') {
    ?>
    <div class="pda-message">
        <div class="pda-message__item center">
            <a class="href m-5" href="/save/">Сохранить персонажа</a>
            и получите бонус <strong>30</strong> <img src="/files/icons/rubles.png" width="10px" alt="">
        </div>
        <div class="pda-message__item small quality-rare center">
            Аккаунты, что не были сохранены, удаляются раз в 30 дней для освобождения никнеймов.
        </div>
    </div>
    <?php
}
?>
</article>
<?php
if ($user['updateEnergy'] === NULL) $db->query('update users set updateEnergy = ? where id = ?', [time(), $user['id']]);
if ($user['updateHP'] === NULL) $db->query('update users set updateHP = ? where id = ?', [time(), $user['id']]);

if ($user['energy'] < $user['max_energy']) {
    $countEnergy = floor((time() - $user['updateEnergy']) / 150);
    if ($countEnergy > 0) {
        if ($user['energy'] + $countEnergy >= $user['max_energy']) {
            $db->query('update `users` set `energy` = `max_energy`, `updateEnergy` = ? where `id` = ?', [time(), $user['id']]);
        } else {
            $db->query('update `users` set `energy` = `energy` + ?, `updateEnergy` = ? where `id` = ?', [$countEnergy, time(), $user['id']]);
        }
    }
}

if ($user['hp'] < $user['max_hp']) {
    $countHP = floor((time() - $user['updateHP']) / 30);
    if ($countHP > 0) {
        if ($user['energy'] + ($countHP * 5) >= $user['max_hp']) {
            $db->query('update `users` set `hp` = `max_hp`, `updateHP` = ? where `id` = ?', [time(), $user['id']]);
        } else {
            $db->query('update `users` set `hp` = `hp` + ?, `updateHP` = ? where `id` = ?', [$countHP * 5, time(), $user['id']]);
        }
    }
}

if ($user['energy'] > $user['max_energy']) $db->query('update users set energy = max_energy where id = ?', [$user['id']]);
if ($user['hp'] > $user['max_hp']) $db->query('update users set hp = max_hp where id = ?', [$user['id']]);

if ($user['exp'] >= ($level[$user['level'] + 1])) {
    if (($user['level'] + 1) == 10) {
        if ($ref = $db->get('SELECT `id_user` FROM `refferals_in` WHERE `id_ref` = ?', [$user['id']])){
            $db->query('UPDATE `users` SET `rubles` = `rubles` + ? WHERE `id` = ?', [10, $user['id']]);
            $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [2, $ref['id_user'], time(), 'Твой браток @id'.$user['id'].' достиг 10 уровня. Твой баланс пополнен на 10 рублей.']);
            $db->query('UPDATE `users` SET `rubles` = `rubles` + ? WHERE `id` = ?', [10, $ref['id_user']]);
            $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [2, $user['id'], time(), 'Ты и твой браток @id'.$ref['id_user'].' получаете по 10 рублей за получением тобой 10 уровня.']);
        }
    }
    $db->query('update users set level = level + ?, exp = exp - ?, rubles = rubles + ?, bolts = bolts + ?, energy = max_energy where id = ?', [1, $level[$user['level'] + 1], $user['level'] + 1, $user['level'] * 50, $user['id']]);
    $newLevel = [
        '<div class="center access-2">Вы перешли на уровень ' . ($user['level'] + 1) . '</div>',
        'Награда: ' . ($user['level'] + 1) . ' <img src="/files/icons/rubles.png" alt="рубли" title=""> и ' . ($user['level'] * 50) . ' <img src="/files/icons/bolts.png" alt="" title="">',
        'Энергия полностью восстановлена'
    ];
    echo $m->pda($newLevel);
}
if ($user['everyDay'] == 0){
    $_SESSION['notify'][] = '<b>Ежедневный бонус:</b> твой баланс пополнен на 5 <img src="/files/icons/rubles.png" alt="рубли" title=""> рублей.';
    $db->query('UPDATE `users` SET `everyDay` = ?, `rubles` = `rubles` + ? WHERE `id` = ?', [1, 5, $user['id']]);
}

if ($db->getCount('SELECT COUNT(`id`) FROM `everyDay` WHERE `id_user` = ?', [$user['id']]) < 1)
{
    $db->query('INSERT INTO `everyDay` (`id_user`) VALUES (?)', [$user['id']]);
}

if ($db->getCount('SELECT COUNT(`id`) FROM `boss_users` WHERE `id_user` = ?', [$user['id']]) < 1)
{
    $db->query('INSERT INTO `boss_users` (`id_user`) VALUES (?)', [$user['id']]);
}

if ($db->getCount('SELECT COUNT(`id`) FROM `backgrounds` WHERE `id_user` = ? and `background` = ?', [$user['id'], 1]) < 1)
{
    $db->query('INSERT INTO `backgrounds` (`id_user`, `background`, `used`) VALUES (?, ?, ?)', [$user['id'], 1, 1]);
}

$get = $db->get('SELECT hp, max_hp, energy, max_energy, repute, bolts, rubles, exp, level FROM users WHERE id = ?', [$user['id']]);
?>
<aside class="aside bg" id="top">
    <div class="flexpda block">
        <div class="flexpda-content">
            <div class="flex-container center" style="align-items: center">
                <div class="flex-link grow-0">
                    <a href="/id<?php echo $user['id']; ?>" class="btn-orange">
                        <img src="/files/icons/user.png" alt="">
                    </a>
                </div>
                <div class="flex-link">
                    <div class="info-about-top"><?php echo $get['energy']; ?>/<?php echo $get['max_energy']; ?> <a href="/buffet/"><img src="/files/icons/plus.png" alt="+" width="10px"></a></div>
                    <div class="info-title">энергия</div>
                </div>
                <div class="flex-link">
                    <div class="info-about-top"><?php echo $get['repute']; ?></div>
                    <div class="info-title">репутация</div>
                </div>
                <div class="flex-link">
                    <div class="info-about-top"><?php echo $get['bolts']; ?></div>
                    <div class="info-title">черный нал.</div>
                </div>
                <div class="flex-link">
                    <div class="info-about-top"><?php echo $get['rubles']; ?> <a href="/phone/balance"><img src="/files/icons/plus.png" alt="+" width="10px"></a></div>
                    <div class="info-title">рубли</div>
                </div>
                <div class="flex-link">
                    <div class="exp-mini">
                        <div style="width: <?php echo 100 * $get['exp'] / $level[$get['level'] + 1]; ?>%;"
                             class="exp-line-mini"></div>
                    </div>
                    <div class="center">
                        <span class="pull-right"><?php echo $level[$user['level'] + 1]; ?></span>
                        <span class="pull-left"><?php echo $get['exp']; ?></span>
                        <strong style="color: #ffb533; font-size: 12px; margin: 0 5px;"><?php echo $get['level']; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-5">
        <?php
        if ($get['energy'] < 10 && $user['free_energy'] != 1) {
            if (isset($_GET['freeEnergy'])){
                $db->query('UPDATE `users` SET `free_energy` = ? WHERE `id` = ?', [1, $user['id']]);
                $u->giveEnergy($user['id'], 50);
                $_SESSION['notify'][] = 'Ты захавал подгон и восстановил 50 энергии.';
                $m->to($_SERVER['HTTP_REFERER']);
            }
            ?>
            <div class="main">
                <div class="flex-container">
                    <div class="flex-item">
                        <img src="/files/icons/food.png" style="height: 64px" alt="">
                    </div>
                    <div class="flex-link pull-left">
                        <h1>Подгон</h1><span class="access-2 small">от смотрящих</span><br>
                        <a href="/?freeEnergy" class="link"> Восполнить 50 <img src="/files/icons/energy.png" alt=""> бесплатно 1 раз</a>
                    </div>
                </div>
            </div>
            <?php
        }

        $sms = $db->getCount('SELECT count(id) FROM phone_sms WHERE id_to = ? and read_at = ?', [$user['id'], 0]);
        if ($sms) echo '<a class="href" href="/phone/sms">Есть SMS сообщения <span class="count">' . $sms . '</span><div class="clearfix"></div></a>';

        $notify = $db->getCount('SELECT count(id) FROM `phone_notify` WHERE `id_user` = ? and `read_at` = ?', [$user['id'], 0]);
        if ($notify) echo '<a class="href" href="/phone/notify">Новое уведомление <span class="count">' . $notify . '</span><div class="clearfix"></div></a>';

        if (isset($_SESSION['notify'])) {
            echo $m->pda($_SESSION['notify']);
            unset($_SESSION['notify']);
        }

        if ($user['training'] !== NULL && $user['training'] <= time()) echo '<a class="href" href="/gym/">Тренировка окончена <span class="count">перейти</span><div class="clearfix"></div></a>';
        ?>
    </div>
</aside>
<?php
$db->query('UPDATE users SET updDate = ? WHERE id = ?', [time(), $user['id']]);
$online = $db->getCount('SELECT count(id) FROM users WHERE updDate > ?', [time() - 900]);
$opg = $clan->have($user['id']);

$check['fights'] = $fights->inFights($user['id']);
?>
<footer class="footer m-0">
    <div class="flex-container grow center">
        <?php if ($user['beta'] == 1 && $user['gift'] == 0):?>
            <a href="/gift" class="btn-orange m-5">
                <img src="/files/icons/gift.png" style="height: 16px" alt=""> Кто потерял сумку?
            </a>
        <?php endif;?>
        <?php if ($check['fights']):?>
            <a class="link w-100 m-5" href="/boss/room/<?php echo $check['fights']['id'];?>">Битва с залётным</a>
        <?php endif;?>
        <a href="/id<?php echo $user['id']; ?>" class="link m-5">Мой профиль</a>
        <a href="/" class="link m-5">Центр города</a>
        <a href="/map/" class="link m-5">Районы</a>
        <a href="/inventory/" class="link m-5">Инвентарь</a>
        <a href="/boss/" class="link m-5 access-2">Залётные</a>
        <a href="/phone/" class="link m-5">Телефон</a>
        <a href="/gym/" class="link m-5">Тренажерка</a>
        <a href="/rating/" class="link m-5">Рейтинг</a>
        <a href="/rating/online" class="link m-5">Игроки онлайн (<?php echo $online;?>)</a>
        <a href="/clan/" class="link m-5">ОПГ</a>
        <a href="/chat/" class="link m-5">Чат</a>
        <?php if (isset($opg) and $opg['radio'] == 1):?>
            <a href="/clan/<?php echo $opg['id_group'];?>/chat/" class="link m-5">Чат ОПГ</a>
        <?php endif;?>
        <a href="/forum/" class="link m-5">Форум</a>
    </div>
    <a href="/dayQuest" class="href center m-5">Ежедневные задания</a>
    <a href="/referral" class="href center m-5">«Позови братка»</a>
    <?php if (empty($user['email'])) echo '<a href="/phone/settings" class="href center mv-5 small">Привяжи email и получи 50 <img src="/files/icons/rubles.png" alt=""></a>'; ?>
    <div class="foot mv-5">
        <a href="/logout/">Выйти из аккаунта?</a><br/>
        <?php echo date("d.m.Y / H:i:s", time()); ?><br/>
        &copy; 2022 г.<br/><br/>
        На данный момент игра находится на стадии <strong>Beta</strong> тестирования. Игровой процесс может кардинально поменяться в будущем.<br/>
        Текущая версия: v0.75
    </div>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(89871934, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/89871934" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
    <a href="//mobtop.ru/in/132698"><img src="//mobtop.ru/132698.gif" alt="MobTop.Ru - Рейтинг и статистика мобильных сайтов"/></a> / <a href="https://t.me/brigadamobi">Канал в Telegram</a>
</footer>
</div>
</div>
<?php
        if(isset($include['other'])) {
            if (is_array($include['other'])) {
                foreach ($include['other'] as $other) {
                    echo $other;
                }
            } else echo $include['other'];
        }
    ?>
</body>
</html>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/files/icons/energy.png"/>
    <link rel="stylesheet" href="/style/fonts.css" type="text/css"/>
    <link rel="stylesheet" href="/style/start.css" type="text/css"/>
    <title>BRIGADA.MOBI</title>
</head>
<body>
<div class="about center">
    <img src="/files/other/logo.png" alt="logo">
    Возвращение бандитской романтики 90х годов
</div>
<div class="start-button">
    <a href="?start">
        <img src="/style/images/Button.png" alt="Старт" title="Быстрый старт">
    </a>
</div>
<?php
if (isset($_GET['start'])) {
    if (isset($_POST['force'])) {
        if (empty($_POST['nickname'])) $error[] = 'Никнейм не может быть пустым';
        elseif (!preg_match('/(*UTF8)^[a-zA-ZА-Яа-яЁё][a-zA-ZА-Яа-яЁё0-9-_\.]{1,20}$/', $_POST['nickname'])) $error[] = 'Никнейм не может быть менее 2 и более 20 символов, не иметь пробелы или иметь число первым символом';
        elseif ($check = $db->get('SELECT `id` FROM `users` WHERE `login` = ?', [$_POST['nickname']])) $error[] = 'Этот никнейм уже занят';
        elseif($_COOKIE['ban'] == 1 or $_SESSION['ban'] == 1) $error[] = 'У вас есть действующая блокировка в игре. Зайдите в свой аккаунт';

        if (!$error) {
            $_POST['password'] = password_hash($m->generatePassword(8), PASSWORD_BCRYPT);
            $db->query('INSERT INTO `users` (`login`, `password`, `addDate`, `updDate`) VALUES (?, ?, ?, ?)', [trim($_POST['nickname']), $_POST['password'], time(), time()]);
            $lastIDS = $db->lastInsertId();
            if (isset($_SESSION['ref']) and is_numeric($_SESSION['ref'])) {
                $db->query('INSERT INTO `refferals_in` (`id_user`, `id_ref`, `dateAdd`) VALUES (?, ?, ?)', [$_SESSION['ref'], $lastIDS, time()]);
                $db->query('INSERT INTO `phone_sms` (`id_user`, `id_to`, `created`, `message`) VALUES (?, ?, ?, ?)', [2, $_SESSION['ref'], time(), 'По Вашей реферальной ссылке зарегистрировался новый игрок @id'.$lastIDS.'<br/>По достижению им 10 уровня Вы оба получите по 10 рублей на свой счет, а также Вы теперь будете получать 5% от его внутреигровых покупок.']);
                unset($_SESSION['ref']);
            }
            if (isset($_SESSION['HTTP_REFERER']) and $_SESSION['HTTP_REFERER'] != 'https://brigada.mobi/?start'){
                $db->query('INSERT INTO `refferals_site` (`ref`, `id_user`, `created_at`) VALUES (?, ?, ?)', [$_SESSION['HTTP_REFERER'], $lastIDS, time()]);
                unset($_SESSION['HTTP_REFERER']);
            }
            setcookie('uid', $lastIDS, time() + 60 * 60 * 24 * 30);
            setcookie('password', $_POST['password'], time() + 60 * 60 * 24 * 30);
            $m->to('/map/');
        } else {
            $m->pda($error);
        }
    }
?>
<div class="start-game">
    <form method="post">
        <label>Введите свой никнейм</label>
        <input type="text" name="nickname" placeholder="Так Вас будут знать игроки...">
        <input type="submit" name="force" value="Продолжить">
        <div class="center">Нажимая ПРОДОЛЖИТЬ Вы даете свое согласие с <a href="/rules/">пользовательским соглашением</a></div>
    </form>
</div>
<?php } ?>
<div class="variant">
    <div class="variant-text">
        <span class="variant-text-span">или</span>
    </div>
</div>
<?php
if (isset($_POST['signin'])) {
    $auth = $db->get('select id, password from users where login = ?', [$_POST['login']]);
    if (empty($_POST['login']) or empty($_POST['password'])) $error[] = 'Введите логин и пароль';
    elseif (!$auth) $error[] = 'Такого игрока не обнаружено';
    elseif (!password_verify(trim($_POST['password']), $auth['password'])) $error[] = 'Ошибка в введенных данных';

    if (!$error) {
        setcookie('uid', $auth['id'], time() + 60 * 60 * 24 * 30);
        setcookie('password', $auth['password'], time() + 60 * 60 * 24 * 30);
        die(header('Location: /'));
    } else {
        echo $m->pda($error);
    }
}
?>
<form method="post">
    <label>Введите логин</label>
    <input type="text" name="login" placeholder="Введите логин...">
    <label>Введите пароль <a href="/restore/" class="restore pull-right">Восстановить?</a></label>
    <input type="password" name="password" placeholder="Введите пароль...">
    <input type="submit" name="signin" value="Авторизация">
</form>
<div class="footer">
    xynd3r &copy; 2022 г.
    <hr/>
    <a href="/rules/">Пользовательское соглашение</a> / <a href="/refferals/">Реферальная программа</a><br/><br/>
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
    <script type="text/javascript" src="//mobtop.ru/c/132697.js"></script><noscript><a href="//mobtop.ru/in/132697"><img src="//mobtop.ru/132697.gif" alt="MobTop.Ru - Рейтинг и статистика мобильных сайтов"/></a></noscript>
</div>
</body>
</html>
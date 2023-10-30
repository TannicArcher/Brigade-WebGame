<?php
if (!isset($user) and $_SERVER['PHP_SELF'] != '/index.php') {
    $m->to('/');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/files/icons/energy.png"/>
    <link rel="stylesheet" href="/style/fonts.css" type="text/css"/>
    <link rel="stylesheet" href="/style/default.css" type="text/css"/>
    <link rel="stylesheet" href="/style/grid.css" type="text/css"/>
    <?php
    if(isset($include['css'])) {
        if (is_array($include['css'])) {
            foreach ($include['css'] as $css) {
                echo '<link rel="stylesheet" href="/style/'.$css.'.css?cache='.time().'" type="text/css"/>';
            }
        } else echo '<link rel="stylesheet" href="/style/'.$include['css'].'?cache='.time().'" type="text/css"/>';
    }
    if(isset($include['js'])) {
        if (is_array($include['js'])) {
            foreach ($include['js'] as $js) {
                echo '<script src="'.$js.'"></script>';
            }
        } else echo '<script src="'.$include['js'].'"></script>';
    }
    ?>
    <title><?php echo(isset($title) ? $title : 'Бригада - онлайн игра'); ?></title>
</head>
<body>
<?php
$checkBan = $u->checkBan($user['id']);
if($checkBan and $checkBan['typeBan'] == 1) {
    if (isset($_GET['exit'])){
        setcookie('uid', '', time() - 60 * 60 * 24 * 30);
        setcookie('password', '', time() - 60 * 60 * 24 * 30);
        setcookie('ban', 1, time() + 60 * 60 * 24 * 30);
        $_SESSION['ban'] = 1;
        $m->to('/');
    }
    ?>
        <div class="banned">
            <div class="center mv-5"><h1>Ваш аккаунт заблокирован</h1></div>
            <div class="banned-info">
                <span class="access-4">Причина блокировки:</span> <?php echo $checkBan['reason'];?><br/>
                <span class="access-4">Выдал блокировку:</span> <?php echo $u->getLogin($checkBan['id_admin']);?><br/>
                <span class="access-4">Дата блокировки:</span> <?php echo date('d.m.Y в H:i:s', $checkBan['startBan']);?><br/>
                <span class="access-4">Окончание блокировки:</span> <?php echo ($checkBan['forever'] == 0 ? date('d.m.Y в H:i:s', $checkBan['endBan']) : 'никогда');?>
                <?php if($checkBan['id_admin'] != 1 and $checkBan['apply'] == 0):?>
                <hr>
                <div class="access-2 main mv-5">
                    Вашу блокировку проверит главный администратор и вынесет окончательное решение в течении 24 часов.
                </div>
                <?php else:?>
                    <div class="access-4 main mv-5 center">
                        Вердикт окончательный
                    </div>
                <?php endif;?>
            </div>
            <a href="/ban/" class="href mv-5">Диалог с администрацией</a>
            <a href="?exit" class="href mv-5">Выйти из аккаунта</a>
        </div>
    <?php
    die();
} elseif ($checkBan and $checkBan['typeBan'] == 0) {
    ?>
    <div class="block">
        <div class="flex-container" style="align-items: center;">
            <div class="flex-link">
                <div class="quality-rare">У вас блокировка общения</div>
                <div class="small">Окончание блокировки: <?php echo ($checkBan['forever'] == 0 ? date('d.m.Y в H:i:s', $checkBan['endBan']) : 'никогда');?></div>
            </div>
            <div class="flex-link grow-0">
                <a href="/id<?php echo $user['id'];?>" class="href">Подробнее</a>
            </div>
            <div class="flex-link grow-0">
                <a href="/ban/" class="href">Обсудить</a>
            </div>
        </div>
    </div>
    <?php
}
if (isset($_COOKIE['ban']) or isset($_SESSION['ban'])) {
    unset($_SESSION['ban']);
    setcookie('ban', '', time() - 60 * 60 * 24 * 30);

}
?>
<div class="wrapper" id="content">
    <div class="container">
        <article class="main">
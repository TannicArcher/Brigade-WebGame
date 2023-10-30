<?php
require '../../main/main.php';
$title = 'Панель управления';
require '../../main/head.php';
switch ($method) {
    default:
        $u->getAccess($user['id'], 1);
        ?>
        <div class="flex-container center">
            <div class="flex-link"><a class="href" href="/admin/users">Управление игроками</a></div>
            <div class="flex-link"><a class="href" href="/admin/weapons">Управление снаряжением</a></div>
            <div class="flex-link"><a class="href" href="/admin/items">Управление предметами</a></div>
            <div class="flex-link"><a class="href" href="/admin/bans">Управление блокировками</a></div>
            <div class="flex-link"><a class="href" href="#">Сообщение</a></div>
            <div class="flex-link"><a class="href" href="#">Статистика</a></div>
        </div>
        <?php
        break;
    case 'users':
        $u->getAccess($user['id'], 4);
        ?>
        <div class="flex-container center">
            <div class="flex-link"><a class="href" href="/admin/users/search">Найти игрока</a></div>
            <div class="flex-link"><a class="href" href="#">Статистика</a></div>
            <div class="flex-link"><a class="href" href="#">Торговать</a></div>
            <div class="flex-link"><a class="href" href="#">Напасть</a></div>
            <div class="flex-link"><a class="href" href="#">Сообщение</a></div>
            <div class="flex-link"><a class="href" href="#">Статистика</a></div>
        </div>
        <?php
        break;
}
require '../../main/foot.php';


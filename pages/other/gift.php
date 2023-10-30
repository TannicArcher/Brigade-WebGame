<?php
include '../../main/main.php';
$title = 'Подарок';
include '../../main/head.php';
if (isset($_GET['take']) && $user['beta'] == 1 && $user['gift'] == 0)
{
    $db->query('UPDATE `users` SET `gift` = ? WHERE `id` = ?', [1, $user['id']]);
    $wpn->give(1, $user['id']);
    $wpn->give(2, $user['id']);
    $wpn->give(3, $user['id']);
    $u->giveRubles($user['id'], 250);
    $u->giveBolts($user['id'], 20000);
    $u->giveRepute($user['id'], 1000);
    $obj->give(7, $user['id'], 5);
    $obj->give(6, $user['id'], 3);
    $obj->give(5, $user['id'], 10);
    $_SESSION['notify'][] = 'Вы открыли неизвестную сумку';
    $db->query('INSERT INTO `chat` (`uid`, `message`, `timeAdd`) VALUES (?, ?, ?)', [2, '@id'.$user['id'].' нашел чью-то сумку.', time()]);
    $m->to('/gift');
} elseif ($user['beta'] == 1 && $user['gift'] == 1) {
    ?>
    <div class="center">
        <img src="/files/icons/giftOpen.png" alt=""><br/>
        <div class="quest-take access-2">Вы успешно открыли сумку с подарком.</div>
    </div>
    <h1>Одежда</h1><br/>
    <div class="flex-container">
        <div class="flex-link">
            <div class="flex-container main">
                <div class="flex-item">
                    <img src="/files/items/head/3.png" style="height: 64px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <h1 class="quality-souvenir">Восьмиклинка</h1><br>
                    <div class="quest-take">
                        Голова
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-link">
            <div class="flex-container main">
                <div class="flex-item">
                    <img src="/files/items/top/1.png" style="height: 64px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <h1 class="quality-souvenir">Олимпийка</h1><br/>
                    <div class="quest-take">
                        Верхняя одежда
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-link">
            <div class="flex-container main">
                <div class="flex-item">
                    <img src="/files/items/boot/2.png" style="height: 64px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <h1 class="quality-souvenir">Спортивки</h1><br/>
                    <div class="quest-take">
                        Ноги
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1>Ресурсы</h1><br/>
    <div class="flex-container">
        <div class="flex-link">
            <div class="href">
                250 <div class="count">рублей</div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="flex-link">
        <div class="href">
                20000 <div class="count">черного нала</div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="flex-link">
            <div class="href">
                1000 <div class="count">влияние</div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <h1>Предметы</h1><br/>
    <div class="flex-container">
        <div class="flex-link">
            <div class="flex-container main">
                <div class="flex-item">
                    <img src="/files/objects/7.png" style="height: 48px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <h1>Пакетик энергочая</h1> <span class="small access-2">5 ед.</span><br>
                    <div class="quest-take">
                        прибавит 50 <img src="/files/icons/energy.png" width="12px">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-link">
            <div class="flex-container main">
                <div class="flex-item">
                    <img src="/files/objects/6.png" style="height: 48px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <h1>Чили</h1> <span class="small access-2">3 ед.</span><br>
                    <div class="quest-take">
                        прибавит 35 <img src="/files/icons/energy.png" width="12px">
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-link">
            <div class="flex-container main">
                <div class="flex-item">
                    <img src="/files/objects/5.png" style="height: 48px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <h1>Мешок кофе</h1> <span class="small access-2">10 ед.</span><br>
                    <div class="quest-take">
                        прибавит 25 <img src="/files/icons/energy.png" width="12px">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1>Дополнительно</h1><br/>
    <div class="main line-height">
        - Статус "Бета-тестер" остается навсегда на Вашем аккаунте. В дальнейшем он позволит получать раньше всех доступ к будущим обновлениям.<br/>
        - В профиле игрока всегда будет видно, что вы бета-тестер.<br/>
        - Игра так и остается в статусе "бета-теста", пока не сделаю хотя бы половину от того, что задумано.<br/>
        - Перед выхода игры из статуса "бета" в статус "продакшен" будет выдан еще один бонус. <br/>
        - Предметы и ресурсы добавлены в Ваш инвентарь. 
    </div>
    <?php
} elseif ($user['beta'] == 1 && $user['gift'] == 0) {
    ?>
    <div>
        <div class="flex-container">
            <div class="flex-item">
                <img src="/files/icons/gift.png" class="m-5" alt=""><br>
            </div>
            <div class="flex-link">
                <div class="quest-take w-100 access-2">
                    Хм, какая-то странная сумка. На вид хорошая. Что же там внутри?
                </div>
            </div>
        </div>
        <a href="?take" class="btn-orange center" style="display: block;">Проверить, что в сумке</a>
    </div>
    <?php
} else $m->to('/404');
include '../../main/foot.php';
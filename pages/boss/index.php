<?php
include '../../main/main.php';
$title = 'Битва с залётными';
$include['css'] = 'boss.style.css';
$include['js'][] = '/assets/boss.include.js';
include '../../main/head.php';
if ($user['access'] < 3)
{
    $_SESSION['notify'][] = 'Временно недоступно.';
    $m->to('/');
}
?>
<div class="outline quality-rare small" style="padding: 5px 10px;">
    Сражения с боссами запущены в тестовом режиме. В будущем всё может поменяться.<br/>
    О найденых ошибках сообщайте администратору.
</div>
<?php
if ($user['repute'] < 500){
    $_SESSION['notify'][] = 'Эй, стой. Тебе туда пока нельзя. Сначала набери 500 репутации.';
    $m->to('/');
}

switch ($method) {
    default:
        $count['boss'] = $db->getCount('SELECT COUNT(`id`) FROM `boss`');
        if (isset($_GET['start']) and is_numeric($_GET['start'])) {
            if (!$db->get('SELECT `id` FROM `boss` WHERE `id` = ?', [$_GET['start']])) $error[] = 'Такого залётного я не нашел';
            else {
                $timeout = $fights->getTimeout($user['id'], $_GET['start']);
                if ($timeout > time()) $error[] = 'Пока рано нападать, кипиш должен утихнуть.';
                $need = $db->get('SELECT `needRepute`, `needKey` FROM `boss` WHERE `id` = ?', [$_GET['start']]);
                if ($need['needRepute'] > $user['repute']) $error[] = 'Недостаточно влияния, чтобы напасть.';
                if (!is_null($need['needKey']) && !$obj->have($need['needKey'], $user['id'], 3)) $error[] = 'Недостаточно ярлыков с прошлого босса';
            }

            if (empty($error)) {
                $room = $fights->createRoom($m->number($_GET['start']), $user['id']);
                $m->to('/boss/room/'.$room);
            } else {
                $_SESSION['notify'] = $error;
                $m->to('/boss/');
            }
        }

        if ($room = $fights->getRoomUser($user['id'])){
            $m->to('/boss/room/'.$room['id_fight']);
        }
        if($count['boss'] > 0){
            $bosses = $db->getAll('SELECT `id`, `name` FROM `boss`');
            ?>
            <div class="boss-container">
                <div class="boss-tabs">
                    <?php foreach ($bosses as $boss):?>
                        <button class="tabLinks" onclick="openTab(event, <?php echo $boss['id'];?>)" <?php echo ($boss['id'] == 1 ? 'id="defaultOpen"' : null);?>>
                            <img src="/files/boss/<?php echo $boss['id'];?>/icon.png" alt="">
                        </button>
                    <?php endforeach;?>
                </div>
                <div class="boss-info">
                    <?php foreach ($bosses as $boss):
                        $item = $fights->getBoss($boss['id']);
                        $timeout = $fights->getTimeout($user['id'], $boss['id']);
                        $lobby = $db->getCount('SELECT COUNT(`id`) FROM `boss_fight` WHERE `id_boss` = ? and `started_at` IS NULL', [$boss['id']]);
                        ?>
                        <div id="<?php echo $boss['id'];?>" class="tabContent">
                            <h1><?php echo $boss['name'];?></h1><span class="access-2"><img src="/files/icons/hp.png" alt=""> <?php echo $item['health'];?></span><br/>
                            
                            <div class="small"><?php echo $item['about'];?></div>
                            <div class="flex-container mt-5">
                                <div class="flex-link">
                                    <h1>Требования</h1><br/>
                                    <div class="quest-take"><img src="/files/icons/repute.png" alt=""> <?php echo $item['needRepute'];?></div>
                                    <?php echo (!is_null($item['needKey']) ? $m->message('@object'.$item['needKey']).' x3' : null);?>
                                </div>
                                <div class="flex-link">
                                    <h1>Награда за победу</h1><br/>
                                    <div class="quest-take"><img src="/files/icons/repute.png" alt=""> <?php echo $item['giveRepute'];?></div>
                                    <div class="quest-take"><img src="/files/icons/exp.png" alt=""> <?php echo $item['giveExp'];?></div>
                                    <div class="quest-take"><img src="/files/icons/bolts.png" alt=""> <?php echo $item['giveBolts'];?></div>
                                </div>
                                <?php if (isset($item['drop'])):?>
                                <div class="flex-link">
                                    <h1>Шанс получить</h1><br/>
                                    <?php 
                                    foreach($item['drop'] as $drops) {
                                        echo $m->message('@'.$drops['typeDrop'].$drops['id_drop']);
                                    }
                                    ?>
                                </div>
                                <?php endif;?>
                            </div>
                            <?php if ($timeout > time()):?>
                                <div class="main small center">
                                    <strong class="access-2">Поднялся кипиш.</strong> Нужно залечь на дно.<br/>
                                    Следующее нападение на этого залётного возможно через <?php echo $m->downcounter(date("d.m.Y H:i:s", $timeout));?>
                                </div>
                            <?php elseif (!is_null($item['needKey']) && !$obj->have($item['needKey'], $user['id'], 3)):?>
                                <div class="main small center">
                                    <strong class="access-2">Недостаточно ярылков.</strong><br/>
                                    Победи прошлого залётного 3 раза.
                                </div>
                            <?php elseif ($item['needRepute'] > $user['repute']):?>
                                <div class="main small center">
                                    <strong class="access-2">Недостаточно влияния.</strong><br/>
                                    Требуется <strong class="access-2"><?php echo $item['needRepute'];?></strong>, а у тебя всего <strong class="access-2"><?php echo $user['repute'];?></strong>
                                </div>
                            <?php else:?>
                                <a href="?start=<?php echo $item['id'];?>" class="href m-5">Создать бой</a>
                                <?php if ($lobby > 0):?>
                                <a href="/boss/list/<?php echo $item['id'];?>" class="href m-5">
                                    Присоединиться к игрокам
                                    <div class="count"><?php echo $lobby;?></div>
                                </a>
                                <?php endif;?>
                            <?php endif;?>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
            <?php
        } else $m->pda(['Пока залётных типов нет, расслабься, мужик']);
        break;
    case 'room':
        $id = $m->number($_GET['id']);
        if (!$id) $m->to('/boss/');
        if (!$room = $fights->getRoomInfo($id)) $m->to('/boss/');
        $boss = $fights->getBoss($room['id_boss']);
        $inFight = $fights->inFight($id, $user['id']);
        if ($inFight && !is_null($room['started_at']) && $inFight['takeDrop'] == 0 && $inFight['kick'] == 0) $m->to('/boss/fight/'.$id);

        if (is_null($room['ending_at'])){
            if ($user['id'] == $room['id_lider'] && is_null($room['started_at'])) {
                if (isset($_GET['lider']) && is_numeric($_GET['lider']) && isset($_GET['yes'])){
                    $check['lider'] = $fights->inFight($id, $m->number($_GET['lider']));
                    if ($m->number($_GET['lider']) == $user['id']) $error[] = 'Вы уже лидер битвы';
                    if (!$check['lider'] || $check['lider']['kick'] == 1) $error[] = 'Этот игрок не состоит в участниках битвы или уже является лидером';
                    
                    if (empty($error)){ 
                        $db->query('UPDATE `boss_fight` SET `id_lider` = ? WHERE `id` = ?', [$m->number($_GET['lider']), $id]);
                        $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, 2, '@id'.$user['id'].' передал лидерство рейдом игроку @id'.$m->number($_GET['lider']), time()]);
                        $_SESSION['notify'][] = 'Вы успешно передали лидерство битвы';
                        $m->to('/boss/room/'.$id);
                    } else {
                        $_SESSION['notify'] = $error;
                        $m->to('/boss/room/'.$id);
                    }
                } elseif (isset($_GET['lider']) && is_numeric($_GET['lider'])) {
                    ?>
                    <div class="question mv-5">
                        <div class="question-answer center access-2">
                            Вы действительно хотите передать лидерство битвой игроку <?php echo $u->getLogin($m->number($_GET['lider']));?>?<br/>
                        </div>
                        <div class="question-option">
                            <a href="/boss/room/<?php echo $id;?>?lider=<?php echo $m->number($_GET['lider']);?>&yes" class="href"
                            style="margin-bottom: 1px;">Да</a>
                            <a href="/boss/room/<?php echo $id;?>" class="href">Нет</a>
                        </div>
                    </div>
                    <?php
                }

                if (isset($_GET['kick']) && is_numeric($_GET['kick']) && isset($_GET['yes'])){
                    $check['kick'] = $fights->inFight($id, $m->number($_GET['kick']));
                    if ($m->number($_GET['kick']) == $user['id']) $error[] = 'Вы нельзя исключить себя';
                    if (!$check['kick'] || $check['kick']['kick'] == 1) $error[] = 'Этот игрок не состоит в участниках битввы';
                    
                    if (empty($error)){ 
                        $db->query('UPDATE `boss_members` SET `kick` = ? WHERE `id_fight` = ? and `id_user` = ?', [1, $id, $m->number($_GET['kick'])]);
                        $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, 2, '@id'.$user['id'].' исключил из рейда игрока @id'.$m->number($_GET['kick']), time()]);
                        $_SESSION['notify'][] = 'Вы успешно исключили игрока из битвы';
                        $m->to('/boss/room/'.$id);
                    } else {
                        $_SESSION['notify'] = $error;
                        $m->to('/boss/room/'.$id);
                    }
                } elseif (isset($_GET['kick']) && is_numeric($_GET['kick'])) {
                    ?>
                    <div class="question mv-5">
                        <div class="question-answer center access-2">
                            Вы действительно хотите исключить из битвы игрока <?php echo $u->getLogin($m->number($_GET['kick']));?>?<br/>
                        </div>
                        <div class="question-option">
                            <a href="/boss/room/<?php echo $id;?>?kick=<?php echo $m->number($_GET['kick']);?>&yes" class="href"
                            style="margin-bottom: 1px;">Да</a>
                            <a href="/boss/room/<?php echo $id;?>" class="href">Нет</a>
                        </div>
                    </div>
                    <?php
                }

                if (isset($_GET['dissolve']) && isset($_GET['yes'])){
                    $db->query('DELETE FROM `boss_chat` WHERE `id_fight` = ?', [$id]);
                    $db->query('DELETE FROM `boss_invite` WHERE `id_fight` = ?', [$id]);
                    $db->query('DELETE FROM `boss_logs` WHERE `id_fight` = ?', [$id]);
                    $db->query('DELETE FROM `boss_members` WHERE `id_fight` = ?', [$id]);
                    $db->query('DELETE FROM `boss_fight` WHERE `id` = ?', [$id]);

                    $_SESSION['notify'][] = 'Вы успешно распустили игроков';
                    $m->to('/boss');
                } elseif (isset($_GET['dissolve'])) {
                    ?>
                    <div class="question mv-5">
                        <div class="question-answer center access-2">
                            Вы действительно хотите распустить игроков и отменить битву?<br/>
                        </div>
                        <div class="question-option">
                            <a href="/boss/room/<?php echo $id;?>?dissolve&yes" class="href"
                            style="margin-bottom: 1px;">Да</a>
                            <a href="/boss/room/<?php echo $id;?>" class="href">Нет</a>
                        </div>
                    </div>
                    <?php
                }

                if (isset($_GET['start']) && isset($_GET['yes'])){
                    $fights->startFight($id);
                    $_SESSION['notify'][] = 'Вы успешно начали битву';
                    $m->to('/boss/fight/'.$id);
                } elseif (isset($_GET['start'])) {
                    ?>
                    <div class="question mv-5">
                        <div class="question-answer center access-2">
                            Вы действительно хотите начать битву?<br/>
                        </div>
                        <div class="question-option">
                            <a href="/boss/room/<?php echo $id;?>?start&yes" class="href"
                            style="margin-bottom: 1px;">Да</a>
                            <a href="/boss/room/<?php echo $id;?>" class="href">Нет</a>
                        </div>
                    </div>
                    <?php
                }
            }
            if (!$fights->inFights($user['id']) && isset($_GET['join']) && is_null($room['started_at'])) {
                if ($inFight && $inFight['kick'] == 1) $error[] = 'Вы были исключены из этой битвы';
                if ($boss['needRepute'] > $user['repute']) $error[] = 'Недостаточно влияния, чтобы присоединиться';
                if (!is_null($boss['needKey']) && !$obj->have($boss['needKey'], $user['id'], 3)) $error[] = 'Недостаточно ярылков от прошлого босса, чтобы присоединиться';
                $timeout = $fights->getTimeout($user['id'], $boss['id']);
                if ($timeout > time()) $error[] = 'Кипиш с прошлого боя еще не утих.';

                if (empty($error)){
                    $db->query('INSERT INTO `boss_members` (`id_fight`, `id_user`, `created_at`) VALUES (?, ?, ?)', [$id, $user['id'], time()]);
                    $_SESSION['notify'][] = 'Вы успешно присоединились к рейду';
                } else $_SESSION['notify'] = $error;
                $m->to('/boss/room/'.$id);
            }

            if ($inFight && $inFight['kick'] == 0 && isset($_GET['leave']) && isset($_GET['yes'])) {
                if ($room['id_lider'] == $user['id']) $error[] = 'Лидер не может покинуть бой, не распустив игроков';

                if (empty($error)){
                    $db->query('UPDATE `boss_members` SET `kick` = ? WHERE `id_fight` = ? and `id_user` = ?', [1, $id, $user['id']]);
                    $_SESSION['notify'][] = 'Вы успешно покинули данный рейд';
                } else $_SESSION['notify'] = $error;
                $m->to('/boss/room/'.$id);
            } elseif ($inFight && $inFight['kick'] == 0 && isset($_GET['leave'])) {
                ?>
                <div class="question mv-5">
                    <div class="question-answer center access-2">
                        Вы действительно хотите покинуть битву?<br/>
                        <strong>Вернуться в этот рейд уже не получится.</strong>
                    </div>
                    <div class="question-option">
                        <a href="/boss/room/<?php echo $id;?>?leave&yes" class="href"
                        style="margin-bottom: 1px;">Да</a>
                        <a href="/boss/room/<?php echo $id;?>" class="href">Нет</a>
                    </div>
                </div>
                <?php
            }
        }
        ?>
        <div class="room-container">
            <div class="room-info">
                <div class="members-container">
                    <div class="room-boss">
                        <div class="image-background" style="background-image: url('/files/background/<?php echo $boss['background'];?>.png')">
                            <div class="image-boss" style="background-image: url('/files/boss/<?php echo $room['id_boss'];?>/1.png')"></div>
                        </div>
                    </div>
                    <div class="flex-link m-5">
                        <div class="room-boss-title center"><?php echo $boss['name'];?></div>
                        — <strong class="access-2"><?php echo $boss['health'];?></strong> здоровья<br/>
                        — битва <strong class="access-2"><?php echo $typeRoom[$room['fightType']];?></strong><br/>
                        — сбор начат <strong class="access-2"><?php echo date('j.m.Y в H:i:s', $room['created_at']);?></strong>
                    </div>
                </div>
                <div class="members-container" style="align-items: center;">
                    <div class="flex-item mh-5">
                        Ссылка на комнату
                    </div>
                    <div class="flex-link">
                        <input type="text" value="@room<?php echo $id;?>" disabled>
                    </div>
                </div>
                <?php if ($user['id'] == $room['id_lider'] && is_null($room['started_at'])):?>
                    <a href="/boss/room/<?php echo $id;?>?start" class="href mt-5">Начать битву</a>
                    <a href="/boss/room/<?php echo $id;?>?dissolve" class="href mt-5">Распустить участников</a>
                <?php elseif (!is_null($room['started_at']) && !is_null($room['ending_at'])):?>
                    <div class="main mt-5 center access-4">
                        Битва с залётным закончилась
                    </div>
                <?php elseif ($room['started_at'] != null):?>
                    <div class="main mt-5 center access-4">
                        Битва с залётным уже идёт
                    </div>
                <?php else:?>
                    <?php if ($inFight && $inFight['kick'] == 0 && is_null($room['started_at'])):?>
                        <a href="/boss/room/<?php echo $id;?>?leave" class="href mt-5">Покинуть битву</a>
                    <?php elseif (!$inFight && !$getRoomUser && $room['fightType'] == 'all' && is_null($room['started_at']) && !$fights->inFights($user['id'])): ?>
                        <a href="/boss/room/<?php echo $id;?>?join" class="href mt-5">Присоединиться к битве</a>
                    <?php elseif ($inFight && $inFight['kick'] == 1):?>
                        <div class="main mt-5">
                            Вы были исключены из битвы лидером
                        </div>
                    <?php endif; ?>
                    <a href="/boss/room/<?php echo $id;?>?refresh=<?php echo time();?>" class="href mt-5">
                        Обновить
                    </a>
                <?php endif;?>
            </div>
            <div class="room-members">
                <div class="room-boss-title">Участники <div class="count"><?php echo count($room['members']);?></div></div>
                <?php foreach ($room['members'] as $member): ?>
                    <div class="members-container mv-5">
                        <div class="flex-link">
                            <a href="/id<?php echo $member['id_user'];?>" class="link">
                                <?php echo $u->getLogin($member['id_user']);?>
                                <?php echo ($member['id_user'] == $room['id_lider'] ? '<div class="count">лидер</div>':null);?>
                                <div class="clearfix"></div>
                            </a>
                        </div>
                        <?php if ($user['id'] == $room['id_lider'] && $member['id_user'] != $user['id']):?>
                        <div class="flex-item">
                            <a href="/boss/room/<?php echo $id;?>?lider=<?php echo $member['id_user'];?>" class="link">
                                <img src="/files/icons/crown.png" width="14px" alt="">
                            </a>
                        </div>
                        <div class="flex-item">
                            <a href="/boss/room/<?php echo $id;?>?kick=<?php echo $member['id_user'];?>" class="link">
                            <img src="/files/icons/kick.png" width="14px" alt="">
                            </a>
                        </div>
                        <?php endif;?>
                    </div>
                <?php endforeach;?>

                <div class="room-boss-title">Чат <div class="count"><?php echo $room['chat'];?></div></div>
                <?php
                if ($inFight && $inFight['kick'] == 0 && is_null($room['ending_at'])):
                    if (isset($_POST['send'])){
                        $post['chat'] = trim($_POST['chat']);
                        if (empty($post['chat'])) $error[] = 'Введите сообщение';
                        elseif (mb_strlen($post['chat']) < 3)  $error[] = 'Сообщение не может быть короче 3 символов';

                        if (empty($error)) {
                            $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, $user['id'], $emoji::Encode($post['chat']), time()]);
                            $m->to('/boss/room/'.$id.'#chat');
                        } else {
                            $_SESSION['notify'] = $error;
                            $m->to('/boss/room/'.$id);
                        }

                    }
                ?>
                <form method="post" id="chat">
                    <div class="members-container" style="align-items: center;">
                        <div class="flex-link">
                            <textarea name="chat" id="message" cols="1" rows="1" placeholder="Введите сообщение..."></textarea>
                        </div>
                        <div class="flex-item">
                            <input type="submit" class="m-5" value=">" name="send">
                        </div>
                    </div>
                </form>
                <?php
                endif;
                ?>
                <div id="messages"></div>
                <script>
                    loadChat(<?php echo $room['id'];?>)
                    setInterval(() => {
                        loadChat(<?php echo $room['id'];?>)
                    }, 5000)
                </script>
            </div>
        </div>
        <?php
        break;

    case 'fight':
        $id = $m->number($_GET['id']);
        $room = $fights->getRoomInfo($id);
        if (!$id) $m->to('/boss/');
        if (!$room) $m->to('/boss/');
        if ($room['started_at'] == null) $m->to('/boss/room/'.$id);
        $boss = $fights->getBoss($room['id_boss']);
        $inFight = $fights->inFight($id, $user['id']);
        if (!$inFight || $inFight['kick'] == 1) $m->to('/boss/room/'.$id);
        if ($room['health'] <= 0 && $inFight['takeDrop'] == 0) {
            
            $place = 1;
            $myDamage = $fights->getDamage($id, $user['id']);
            if (is_null($room['ending_at'])){
                $db->query('UPDATE `boss_fight` SET `ending_at` = ? WHERE `id` = ?', [time(), $id]);
                $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, 2, 'Битва закончилась победой, поздравляю участников!', time()]);
            }
            if ($myDamage > 0) {
                $randomDropCheck = $m->roulette([0 => 80, 1 => 20]);
                if ($randomDropCheck > 0) {
                    $getDrop = $db->getAll('SELECT `id_drop`, `chance` FROM `boss_drop` WHERE `id_boss` = ?', [$room['id_boss']]);
                    if ($getDrop) {
                        foreach ($getDrop as $key) {
                            $pop[$key['id_drop']] = $key['chance'];
                        }
                        $bossDrop = $m->roulette($pop);
                        $bossDrop = $db->get('SELECT * FROM `boss_drop` WHERE `id_drop` = ?', [$bossDrop]);
                        $msg = '@id'.$user['id'].' выпал случайный предмет - @'.$bossDrop['typeDrop'].$bossDrop['id_drop'];
                        $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, 2, $msg, time()]);
                        if ($bossDrop['typeDrop'] == 'item') $wpn->give($bossDrop['id_drop'], $user['id']);
                    }
                }
                $bgCheck = $db->getCount('SELECT COUNT(`id`) FROM `backgrounds` WHERE `id_user` = ? and `background` = ?', [$user['id'], $boss['background']]);
                if ($bgCheck == 0) {
                    $randomDropBgCheck = $m->roulette([0 => 70, 1 => 30]);
                    if ($randomDropBgCheck > 0) {
                        $bgDrop = $db->query('INSERT INTO `backgrounds` (`id_user`, `background`) VALUES (?, ?)', [$user['id'], $boss['background']]);
                    }
                }
                $u->giveBolts($user['id'], $boss['giveBolts']);
                $u->giveRepute($user['id'], $boss['giveRepute']);
                $u->giveExp($user['id'], $boss['giveExp']);
                $db->query('UPDATE `boss_members` SET `takeDrop` = ? WHERE `id_user` = ? and `id_fight` = ?', [1, $user['id'], $id]);
                if (is_null($room['ending_at'])){
                    foreach ($room['members'] as $member) {
                        $sql = 'UPDATE `boss_users` SET `boss_'.$boss['id'].'_timeout` = ?, `boss_'.$boss['id'].'_success` = `boss_'.$boss['id'].'_success` + ? WHERE `id_user` = ?';
                        $db->query($sql, [time() + 3600, 1, $member['id_user']]);
                    }
                }
                $obj->give($boss['giveKey'], $user['id']);
            } else {
                $db->query('UPDATE `boss_members` SET `takeDrop` = ? WHERE `id_user` = ? and `id_fight` = ?', [1, $user['id'], $id]);
            }
            ?>
            <div class="fight-container">
                <div class="room-members">
                    <h1>Твоя награда за бой</h1><br/>
                    <?php if ($myDamage > 0):?>
                        <div class="quest-take access-4">
                            <img src="/files/icons/repute.png" width="16px" alt=""> <?php echo $boss['giveRepute'];?>
                        </div>
                        <div class="quest-take access-4">
                            <img src="/files/icons/exp.png" width="16px" alt=""> <?php echo $boss['giveExp'];?>
                        </div>
                        <div class="quest-take access-4">
                            <img src="/files/icons/bolts.png" width="16px" alt=""> <?php echo $boss['giveBolts'];?>
                        </div>
                         И 
                        <?php echo $m->message('@object'.$boss['giveKey']);?><br/>

                        <?php if (isset($bossDrop)):?>
                            <h1>Выпал случайный предмет, поздравляем!</h1><br/>
                            <?php echo $m->message('@'.$bossDrop['typeDrop'].$bossDrop['id_drop']);?>
                        <?php endif;?>
                        <?php if (isset($bgDrop)):?>
                            <?php echo $m->message('@back'.$boss['background']);?>
                        <?php endif;?>
                    <?php else: ?>
                        <div class="access-2">
                            Ты ничего не получил, так как не нанес урон по боссу.
                        </div>
                    <?php endif; ?>
                    <a href="/boss/room/<?php echo $id;?>" class="href mt-5">Вернуться к комнате боя</a>
                    <a href="/boss/" class="href mt-5">Вернуться к залётным</a>
                </div>
                <div class="room-members">
                    <div class="room-boss-title">ТОП по урону за бой</div>
                    <?php foreach ($room['members'] as $member): ?>
                        <div class="members-container mv-5">
                            <div class="flex-item">
                                <div class="link">
                                    <?php echo $place++;?>
                                </div>
                            </div>
                            <div class="flex-link">
                                <a href="/id<?php echo $member['id_user'];?>" class="link">
                                    <?php echo $u->getLogin($member['id_user']);?>
                                    <?php echo ($member['id_user'] == $room['id_lider'] ? '<div class="count">лидер</div>':null);?>
                                    <div class="clearfix"></div>
                                </a>
                            </div>
                            <div class="flex-item">
                                <div class="link">
                                    <?php echo $fights->getDamage($id, $member['id_user']);?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
            <?php
            include '../../main/foot.php';
            die();
        } elseif ($room['health'] <= 0 && $inFight['takeDrop'] == 1) {
            $_SESSION['notify'][] = 'Ты уже забрал награду за этот бой.';
            $m->to('/boss/room/'.$id);
        } elseif (time() > $room['started_at'] + 14400) {
            if (is_null($room['ending_at'])){
                $db->query('UPDATE `boss_fight` SET `ending_at` = ? WHERE `id` = ?', [time(), $id]);
                $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, 2, 'Битва закончилась поражением. Возвращайтесь, когда будете более сильны.', time()]);
                foreach ($room['members'] as $member) {
                    $sql = 'UPDATE `boss_users` SET `boss_'.$boss['id'].'_timeout` = ?, `boss_'.$boss['id'].'_success` = `boss_'.$boss['id'].'_success` + ? WHERE `id_user` = ?';
                    $db->query($sql, [time() + 3600, 1, $member['id_user']]);
                }
            }
            $db->query('UPDATE `boss_members` SET `takeDrop` = ? WHERE `id_user` = ? and `id_fight` = ?', [1, $user['id'], $id]);
            $m->to('/boss/room/'.$id);
        }

        $attack = [
            1 => [
                'log' => 'втащил',
                'price' => 'energy',
                'amount' => '10',
                'damage' => 50 + $user['power'],
            ],
            2 => [
                'log' => 'ударил часами',
                'price' => 'energy',
                'amount' => '18',
                'damage' => (50 + $user['power']) * 2,
            ],
            3 => [
                'log' => 'пырнул',
                'price' => 'knife',
                'damage' => 200,
            ],
            4 => [
                'log' => 'шмальнул',
                'price' => 'pistol',
                'damage' => 500,
            ],
        ];
        if (isset($_GET['attack']) && is_numeric($_GET['attack']) && array_key_exists($_GET['attack'], $attack)){
            if ($attack[$_GET['attack']]['price'] == 'energy' && $user['energy'] < $attack[$_GET['attack']]['amount']) $error[] = 'Недостаточно энергии';
            if ($attack[$_GET['attack']]['price'] == 'knife' && $user['knife'] < 1) $error[] = 'Недостаточно финок';
            if ($attack[$_GET['attack']]['price'] == 'pistol' && $user['pistol'] < 1) $error[] = 'Недостаточно патронов';

            if (empty($error)){
                $random = $m->roulette([0 => (100 - $user['criticalChance']), 1 => ceil($user['criticalChance'] / 2)]);
                if ($random > 0) {
                    $damage = ceil($attack[$_GET['attack']]['damage'] * ($user['criticalDamage'] / 100));
                    $crit = true;
                } else $damage = $attack[$_GET['attack']]['damage'];
                $db->query('UPDATE `boss_fight` SET `health` = `health` - ? WHERE `id` = ?', [$damage, $id]);
                $db->query('INSERT INTO `boss_logs` (`id_fight`, `id_user`, `message`, `damage`, `created_at`) VALUES (?, ?, ?, ?, ?)', [$id, $user['id'], ($crit ? 'критически' : null).' '.$attack[$_GET['attack']]['log'], $damage, time()]);
                if ($attack[$_GET['attack']]['price'] == 'energy') $u->takeEnergy($user['id'], $attack[$_GET['attack']]['amount']);
                if ($attack[$_GET['attack']]['price'] == 'knife') $u->takeKnife($user['id'], 1);
                if ($attack[$_GET['attack']]['price'] == 'pistol') $u->takePistol($user['id'], 1);
                $_SESSION['notify'][] = 'Ты '.($crit ? 'критически' : null).' '.$attack[$_GET['attack']]['log'].' нанеся '.$damage.' урона.';
            } else $_SESSION['notify'] = $error;
            $m->to('/boss/fight/'.$id);
        }
        ?>
        <div class="fight-container">
            <div class="fight-boss">
                <div class="fight-background" style="background-image: url('/files/background/<?php echo $boss['background'];?>.png')">
                    <div class="fight-boss-image" style="background-image: url('/files/boss/<?php echo $room['id_boss'];?>/<?php echo $fights->getImageBoss($room['health'], $boss['health']);?>.png')"></div>
                </div>
                <div class="link mt-5 center">
                    <img src="/files/icons/hp.png" alt=""> <?php echo $room['health'];?> из <?php echo $boss['health'];?>
                </div>
                <div class="link mt-5 center small">
                    Конец боя через <?php echo $m->downcounter(date("d.m.Y H:i:s", $room['started_at'] + 14400));?>
                </div>
                <a href="/boss/fight/<?php echo $id;?>?refresh=<?php echo time();?>" class="href mt-5 center">
                    Обновить
                </a>
            </div>
            <div class="fight-side" id="action">
                <a href="/boss/fight/<?php echo $id;?>?attack=1" class="fights-action fights-action-outline">
                    <img src="/files/fight/1.png" width="48px" alt="">
                    <div class="action-price">
                        <div class="action-price-title">Втащить</div>
                        <div class="action-price-info">
                            <?php echo 50 + $user['power'];?> урона за 10 <img src="/files/icons/energy.png" alt="">
                        </div>
                    </div>
                </a>
                <a href="/boss/fight/<?php echo $id;?>?attack=2" class="fights-action fights-action-outline">
                    <img src="/files/fight/2.png" width="48px" alt="">
                    <div class="action-price">
                        <div class="action-price-title">Удар часами</div>
                        <div class="action-price-info">
                        <?php echo (50 + $user['power']) * 2;?> урона за 18 <img src="/files/icons/energy.png" alt="">
                        </div>
                    </div>
                </a>
                <a href="/boss/fight/<?php echo $id;?>?attack=3" class="fights-action fights-action-outline">
                    <img src="/files/fight/3.png" width="48px" alt="">
                    <div class="action-price">
                        <div class="action-price-title">Пырнуть</div>
                        <div class="action-price-info">
                            200 урона за 1 <img src="/files/icons/knife.png" width="16px" alt=""> / у тебя <?php echo $user['knife'];?> шт.
                        </div>
                    </div>
                </a>
                <a href="/boss/fight/<?php echo $id;?>?attack=4" class="fights-action fights-action-outline">
                    <img src="/files/fight/4.png" width="48px" alt="">
                    <div class="action-price">
                        <div class="action-price-title">Шмальнуть</div>
                        <div class="action-price-info">
                            500 урона за 1 <img src="/files/icons/pistol.png" width="16px" alt=""> / у тебя <?php echo $user['pistol'];?> шт.
                        </div>
                    </div>
                </a>
                <a href="/blackmarket" class="href m-5">Купить на чёрном рынке</a>
            </div>
        </div>
        <div class="room-container">
            <div class="room-members">
                <div class="room-boss-title">
                    Лог битвы 
                    <div class="count"><?php echo $room['log'] ?? 0;?></div>
                </div>
                <?php
                if ($room['log'] > 0) {
                    $pg = new Game\Paginations (10, 'page');
                    $pg->setTotal($room['log']);
                    $get = $db->getAll('SELECT * FROM `boss_logs` WHERE `id_fight` = ? '.$pg->getLimit('`id`'), [$id]);
                    foreach ($get as $key) {
                        ?>
                        <div class="block line-height mt-5 small">
                            <?php echo $u->getLogin($key['id_user'], true);?> <?php echo $emoji::Decode($m->message($key['message']));?> на <strong class="access-2"><?php echo $key['damage'];?></strong> урона.
                        </div>
                        <?php
                    }
                    echo $pg->render();
                } else echo '<div class="main center">Логов нет</div>';
                ?>
            </div>
            <div class="room-members">
                <div class="room-boss-title">Участники <div class="count"><?php echo count($room['members']);?></div></div>
                <?php foreach ($room['members'] as $member): ?>
                    <div class="members-container mv-5">
                        <div class="flex-link">
                            <a href="/id<?php echo $member['id_user'];?>" class="link">
                                <?php echo $u->getLogin($member['id_user']);?>
                                <?php echo ($member['id_user'] == $room['id_lider'] ? '<div class="count">лидер</div>':null);?>
                                <div class="clearfix"></div>
                            </a>
                        </div>
                        <div class="flex-item">
                            <div class="link">
                                <?php echo $fights->getDamage($id, $member['id_user']);?>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
            <div class="room-members">
                <div class="room-boss-title">Чат <div class="count"><?php echo $room['chat'] ?? 0;?></div></div>
                <?php
                if ($inFight && $inFight['kick'] == 0):
                    if (isset($_POST['send'])){
                        $post['chat'] = trim($_POST['chat']);
                        if (empty($post['chat'])) $error[] = 'Введите сообщение';
                        elseif (mb_strlen($post['chat']) < 3)  $error[] = 'Сообщение не может быть короче 3 символов';

                        if (empty($error)) {
                            $db->query('INSERT INTO `boss_chat` (`id_fight`, `id_user`, `message`, `created_at`) VALUES (?, ?, ?, ?)', [$id, $user['id'], $emoji::Encode($post['chat']), time()]);
                            $m->to('/boss/fight/'.$id.'#chat');
                        } else {
                            $_SESSION['notify'] = $error;
                            $m->to('/boss/fight/'.$id);
                        }

                    }
                ?>
                <form method="post" id="chat">
                    <div class="members-container" style="align-items: center;">
                        <div class="flex-link">
                            <textarea name="chat" id="message" cols="1" rows="1" placeholder="Введите сообщение..."></textarea>
                        </div>
                        <div class="flex-item">
                            <input type="submit" class="m-5" value=">" name="send">
                        </div>
                    </div>
                </form>
                <?php
                endif;
                if ($room['chat'] > 0) {
                    $pg = new Game\Paginations (5, 'page');
                    $pg->setTotal($room['chat']);
                    $get = $db->getAll('SELECT * FROM `boss_chat` WHERE `id_fight` = ? '.$pg->getLimit('`id`'), [$id]);
                    foreach ($get as $key) {
                        ?>
                        <div class="block line-height mv-5 small">
                            <?php echo $u->getLogin($key['id_user'], true);?> <span class="small pull-right"><?php echo date("d.m.Y в H:i:s", $key['created_at']);?></span><br/>
                            <div class="clearfix"></div>
                            <div><?php echo $emoji::Decode($m->message($key['message']));?></div>
                        </div>
                        <?php
                    }
                    echo $pg->render();
                } else echo '<div class="main center">Сообщений нет</div>';
                ?>
            </div>
        </div>
        <?php
        break;
    case 'list':
        $id = $m->number($_GET['id']);
        $boss = $fights->getBoss($id);
        if (!$boss) $m->to('/boss/');

        $count['lobby'] = $db->getCount('SELECT COUNT(`id`) FROM `boss_fight` WHERE `id_boss` = ? and `started_at` IS NULL', [$id]);
        ?>
        <div class="block">
            <strong class="access-4">Рейды на <?php echo $boss['name'];?></strong>
            <div class="count">
                <?php echo $count['lobby'];?>
                <div class="clearfix"></div>
            </div>
        </div>
        <?php
        if ($count['lobby'] > 0) {
            $pg = new Game\Paginations (10, 'page');
            $pg->setTotal($count['lobby']);
            $get = $db->getAll('SELECT * FROM `boss_fight` WHERE `id_boss` = ? and `started_at` IS NULL '.$pg->getLimit('`created_at`', 'DESC'), [$id]);
            foreach ($get as $key) {
                $count['members'] = $db->getCount('SELECT COUNT(`id`) FROM `boss_members` WHERE `id_fight` = ? and `kick` = ?', [$key['id'], 0]);
                ?>
                <div class="members-container mt-5">
                    <div class="flex-link">
                        <a href="/boss/room/<?php echo $key['id'];?>" class="link">Рейд игрока <?php echo $u->getLogin($key['id_lider']);?></a>
                    </div>
                    <div class="flex-item">
                        <div class="link"><?php echo $count['members'];?> чел.</div>
                    </div>
                </div>
                <?php
            }
            echo $pg->render();
        } else $m->pda('Никто не ищет быков на разборки. Напади сам и позови друзей!');
        ?>
        <a href="/boss/" class="href">Вернуться к залётным</a>
        <?php
        break;
}

include '../../main/foot.php';
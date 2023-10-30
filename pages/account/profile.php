<?php
require '../../main/main.php';

switch ($method) {
    default:
        $id = abs((int)$_GET['id']);
        $profile = $u->getInfo($id);
        if (!$profile) die(header('location: /'));
        $title = "Профиль «{$profile['login']}»";
        $include['css'] = 'profile.style.css';
        require '../../main/head.php';
        $under_title = "<span class='small access-{$profile['access']}'>" . (!empty($profile['access_name']) ? $profile['access_name'] : $access[$profile['access']]) . "</span>";
        $userClan = $clan->have($user['id']);
        $profileClan = $clan->have($profile['id']);
        $equip = $wpn->getEquip($profile['id']);
        if ($equip){
            foreach ($equip as $eq) {
                if ($eq['info']['slot'] == 'head') $usage['head'] = $eq['info']['id'];
                if ($eq['info']['slot'] == 'accessory') $usage['accessory'] = $eq['info']['id'];
                if ($eq['info']['slot'] == 'top') $usage['top'] = $eq['info']['id'];
                if ($eq['info']['slot'] == 'body') $usage['body'] = $eq['info']['id'];
                if ($eq['info']['slot'] == 'boot') $usage['boot'] = $eq['info']['id'];
            }
        }
        $background = $db->get('SELECT * FROM `backgrounds` WHERE `id_user` = ? and `used` = ?', [$profile['id'], 1]);
        if ($background){
            $back = $background['background'];
        } else $back = 1;
        $linkToImage = '/image/profile/'.(isset($usage['head']) ? $usage['head'] : 0).'/'.(isset($usage['accessory']) ? $usage['accessory'] : 0).'/'.(isset($usage['top']) ? $usage['top'] : 0).'/'.(isset($usage['body']) ? $usage['body'] : 0).'/'.(isset($usage['boot']) ? $usage['boot'] : 0).'/'.$back;
        if (isset($_GET['inviteInClan']) && $userClan['rank'] >= 3 && !$profileClan && isset($_GET['yes'])) {
            if ($db->getCount('SELECT COUNT(`id`) FROM `groups_users` WHERE `id_group` = ? and `id_user` = ?', [$userClan['id_group'], $profile['id']])) {
                $_SESSION['notify'][] = 'Данный игрок уже имеет приглашение в ваше ОПГ';
            } else {
                $db->query('INSERT INTO `groups_users` (`id_group`, `id_user`, `invite`, `invite_time`) VALUES (?, ?, ?, ?)', [$userClan['id_group'], $profile['id'], $user['id'], time()]);
                $log = '@id'.$user['id'].' приглашает тебя присоединиться к ОПГ @clan'.$userClan['id_group'];
                $db->query('INSERT INTO `phone_notify` (`id_user`, `text`, `created_at`, `read_at`, `linkAccept`) VALUES (?, ?, ?, ?, ?)', [$profile['id'], $log, time(), 0, '/clan/'.$userClan['id_group'].'?acceptInvite']);
                $_SESSION['notify'][] = 'Приглашение в ОПГ успешно отправлено';
                $m->to('/id'.$profile['id']);
            }
        } elseif (isset($_GET['inviteInClan']) && $userClan['rank'] >= 3 && !$profileClan) {
            ?>
            <div class="question mv-5">
                <div class="question-answer center access-2">
                    Вы действительно хотите пригласить игрока в свое ОПГ?<br/>
                </div>
                <div class="question-option">
                    <a href="?inviteInClan&yes" class="href"
                       style="margin-bottom: 1px;">Да</a>
                    <a href="/id<?php echo $profile['id'];?>" class="href">Нет</a>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="account">
            <div class="account-action">
                <?php if ($profile['id'] != $user['id']): ?>
                <a href="/phone/sms/new?to=<?php echo $id; ?>" class="action">
                    <img src="/files/icons/sms.png" alt="*">
                </a>
                    <?php if (!$profileClan && isset($userClan) && $userClan['rank'] >= 3): ?>
                    <a href="?inviteInClan" class="action">
                        <img src="/files/icons/add.png" alt="*">
                    </a>
                    <?php endif;?>
                <?php else:?>
                <a href="/phone/settings" class="action">
                    <img src="/files/icons/settings.png" alt="*">
                </a>
                <?php endif;?>
            </div>
            <div class="account-side first">
                <div class="account__title">
                    Криминальная сводка по делу №<?php echo $profile['id'];?>
                </div>
                <div class="account-side__info line-height">
                    — Известен как <strong class="access-2"><?php echo $profile['login'];?></strong> / <?php echo $under_title; ?><br/>
                    — <strong class="access-2"><?php echo date('j.m.Y', $profile['addDate']);?></strong> прибыл в город<br/>
                    — Был замечен <strong class="access-2"><?php echo ($profile['updDate'] > time() - 3 ? 'только что': $m->oncounter(date('Y-m-j H:i:s', $profile['updDate'])));?></strong><br/>
                    — Имеет <strong class="access-2"><?php echo number_format($profile['repute']);?></strong> ед. влияния в городе<br/>
                    — Имеет <strong class="access-2"><?php echo $profile['level'];?></strong> уровень преступности
                </div>
                <?php
                $cu = $clan->have($profile['id']);
                if (isset($cu) and $cu):
                ?>
                <div class="account__title">
                    Деятельность <a href="/clan/<?php echo $cu['id_group'];?>">Перейти к ОПГ</a>
                </div>
                <div class="account-side__info line-height">
                    — состоит в ОПГ <strong class="access-2">«<?php echo trim($cu['name']);?>»</strong><br/>
                    — занимает ранг <strong class="access-2">«<?php echo $rankGrp[$cu['rank']];?>»</strong>
                </div>
                <?php endif;?>
                <div class="account__title">
                    Физические способности
                </div>
                <div class="account-side__info line-height">
                    — имеет <strong class="access-2"><?php echo $profile['power'];?></strong> ед. силы<br/>
                    — имеет <strong class="access-2"><?php echo $profile['shield'];?></strong> ед. защиты<br/>
                    — наносит <strong class="access-2"><?php echo $profile['criticalDamage'];?>%</strong> критического урона, с шансом <strong class="access-2"><?php echo $profile['criticalChance'];?>%</strong><br/>
                </div>
            </div>
            <div class="account-image">
                <div class="account__title">
                    Фото из личного дела
                </div>
                <img src='<?php echo $linkToImage;?>' width='320px'/>
            </div>
            <div class="account-side last">
                <?php
                $s['head'] = $wpn->getEquipSlot('head', $id);
                $s['accessory'] = $wpn->getEquipSlot('accessory', $id);
                $s['top'] = $wpn->getEquipSlot('top', $id);
                $s['body'] = $wpn->getEquipSlot('body', $id);
                $s['boot'] = $wpn->getEquipSlot('boot', $id);
                ?>
                <div class="account-slot">
                    <div class="account-slot__image">
                        <?php echo ($s['head'] ? '<a href="/view/equipments/' . $s['head']['id'] . '"><img src="/files/items/head/' . $s['head']['id_weapon'] . '.png" title="' . $s['head']['info']['name'] . '" alt="img"></a>':'<img src="/files/items/head/default.png" alt="img">');?>
                    </div>
                    <div class="account-slot__image">
                        <?php echo ($s['accessory'] ? '<a href="/view/equipments/' . $s['accessory']['id'] . '"><img src="/files/items/accessory/' . $s['accessory']['id_weapon'] . '.png" title="' . $s['accessory']['info']['name'] . '" alt="img"></a>':'<img src="/files/items/accessory/default.png" alt="img">');?>
                    </div>
                    <div class="account-slot__image">
                        <?php echo ($s['top'] ? '<a href="/view/equipments/' . $s['top']['id'] . '"><img src="/files/items/top/' . $s['top']['id_weapon'] . '.png" title="' . $s['top']['info']['name'] . '" alt="img"></a>':'<img src="/files/items/top/default.png" alt="img">');?>
                    </div>
                    <div class="account-slot__image">
                        <?php echo ($s['body'] ? '<a href="/view/equipments/' . $s['body']['id'] . '"><img src="/files/items/body/' . $s['body']['id_weapon'] . '.png" title="' . $s['body']['info']['name'] . '" alt="img"></a>':'<img src="/files/items/body/default.png" alt="img">');?>
                    </div>
                    <div class="account-slot__image">
                        <?php echo ($s['boot'] ? '<a href="/view/equipments/' . $s['boot']['id'] . '"><img src="/files/items/boot/' . $s['boot']['id_weapon'] . '.png" title="' . $s['boot']['info']['name'] . '" alt="img"></a>':'<img src="/files/items/boot/default.png" alt="img">');?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'profile_old':
        ?>
        <div class="flex-container" style="align-items: flex-start;">
            <div class="profile">
                <div class="profile__name">
                    <div class="flex-container center mv-5" style="align-items: center">
                        <div class="flex-link">
                            <div class="info-about"><?php echo $u->getLogin($profile['id']); ?></div>
                        </div>
                        <div class="flex-link">
                            <?php echo $under_title; ?>
                        </div>
                    </div>
                </div>
                <div class="profile__image">
                    <img src='<?php echo $linkToImage;?>' width='320px'/>
                </div>
                <div class="profile__name">
                    <div class="flex-container center mv-5">
                        <div class="flex-link">
                            <div class="info-about"><?php echo $profile['level']; ?></div>
                            <div class="info-title">Уровень</div>
                        </div>
                        <div class="flex-link">
                            <div class="info-about"><?php echo $profile['repute']; ?></div>
                            <div class="info-title">Репутация</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="profile__info" style="flex-grow: 1">
                <?php
                if ($checkBan = $u->checkBan($id)){
                    if (isset($_GET['unban']) and $user['access'] > 2) {
                        $db->query('UPDATE `banList` SET `endBan` = ?, `forever` = ?, `id_unban` = ? WHERE `id` = ?', [time(), 0, $user['id'], $checkBan['id']]);
                        $_SESSION['notify'][] = 'Игрок разблокирован';
                        $m->to('/id'.$id);
                    } elseif ($user['access'] > 2) {
                        echo '<a href="?unban" class="href mv-5">Разблокировать</a>';
                    }
                    ?>
                    <div class="mv-5">
                        <div class="banned-info">
                            <span class="access-4">Тип:</span> <?php echo ($checkBan['typeBan'] == 0 ? 'блокировка общения' : 'полная блокировка');?><br/>
                            <span class="access-4">Причина блокировки:</span> <?php echo $checkBan['reason'];?><br/>
                            <span class="access-4">Выдал блокировку:</span> <?php echo $u->getLogin($checkBan['id_admin'], true);?><br/>
                            <span class="access-4">Дата блокировки:</span> <?php echo date('d.m.Y в H:i:s', $checkBan['startBan']);?><br/>
                            <span class="access-4">Окончание блокировки:</span> <?php echo ($checkBan['forever'] == 0 ? date('d.m.Y в H:i:s', $checkBan['endBan']) : 'никогда');?>
                        </div>
                        <?php if($checkBan['id_admin'] != 1 and $checkBan['apply'] == 0):?>
                            <hr>
                            <div class="access-2 main">
                                Блокировку проверит главный администратор и вынесет окончательное решение в течении 24 часов.
                            </div>
                        <?php else:?>
                            <div class="access-4 main center">
                                Вердикт окончательный
                            </div>
                        <?php endif;?>
                    </div>
                    <?php
                }
                $cu = $clan->have($profile['id']);
                if (isset($cu) and $cu) {
                    ?>
                    <div class="mv-5">
                        <a class="link" href="/clan/<?= $cu['id_group']; ?>">
                            <div class="info-title">ОПГ / <?= $rankGrp[$cu['rank']]; ?></div>
                            <div class="info-about"><?= $cu['name']; ?></div>
                        </a>
                    </div>
                    <?php
                }
                ?>
                <div class="flex-container center mv-5 outline">
                    <div class="flex-link">
                        <div class="info-about"><?php echo $profile['power']; ?></div>
                        <div class="info-title">Сила</div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about"><?php echo $profile['shield']; ?></div>
                        <div class="info-title">Защита</div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about"><?php echo $profile['criticalDamage']; ?>%</div>
                        <div class="info-title">Крит. урон</div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about"><?php echo $profile['criticalChance']; ?>%</div>
                        <div class="info-title">Шанс крита</div>
                    </div>
                </div>
                <div class="block line-height mv-5 small">
                    Попал на район: <?php echo date('j.m.Y в H:i:s', $profile['addDate']);?><br/>
                    Последний движ был: <?php echo ($profile['updDate'] > time() - 3 ? 'только что': $m->oncounter(date('Y-m-j H:i:s', $profile['updDate'])));?><br/>
                </div>
                <?php if ($user['access'] > 2): ?>
                    <div class="block center">
                        <h1>Информация для администратора</h1><br/>
                        <div class="flex-container">
                            <div class="flex-link">
                                <div class="info-about"><?php echo $profile['bolts']; ?></div>
                                <div class="info-title">чн</div>
                            </div>
                            <div class="flex-link">
                                <div class="info-about"><?php echo $profile['rubles']; ?></div>
                                <div class="info-title">руб</div>
                            </div>
                        </div>
                    </div>
                <?php
                endif;
                if (isset($_GET['stats'])):
                    $stats['chat'] = $db->getCount('SELECT COUNT(`id`) FROM `chat` WHERE `uid` = ?', [$profile['id']]);
                    ?>
                    <div class="block line-height mv-5">
                        Сообщений в чате: <span class="access-2"><?php echo number_format($stats['chat']);?></span>
                    </div>
                <?php endif;?>
                <div class="flex-container">
                    <div class="flex-link"><a class="href" href="#">Статистика <span class="count">скоро</span><div class="clearfix"></div></a></div>
                    <?php if ($profile['id'] != $user['id']): ?>
                        <div class="flex-link">
                            <a class="href" href="/phone/sms/new?to=<?php echo $id; ?>">Написать SMS</a>
                        </div>
                    <?php endif; ?>
                    <?php if (!$profileClan && isset($userClan) && $userClan['rank'] >= 3): ?>
                        <div class="flex-link">
                            <a class="href" href="?inviteInClan">Завербовать в ОПГ</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
        break;

    case 'weapons':
        $title = 'Инвентарь :: Экипировка';
        require '../../main/head.php';
        ?>
        <div class="flex-container center">
            <div class="flex-link"><a href="/inventory/" class="href">Экипировка</a></div>
            <div class="flex-link"><a href="/inventory/objects" class="href">Предметы</a></div>
            <div class="flex-link"><a href="/inventory/backgrounds" class="href">Задний план</a></div>
        </div>
        <?php
        $cnt = $db->getCount('SELECT count(`id`) FROM `weapons_users` WHERE `id_user` = ?', [$user['id']]);
        if ($cnt > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($cnt);

            if (isset($_GET['equip']) and is_numeric($_GET['equip'])) {
                $res = $wpn->equip((int)$_GET['equip'], $user['id']);
                $m->pda($res);
            }

            // Разбор предмета
            if (isset($_GET['parsing']) and is_numeric($_GET['parsing']) and isset($_GET['yes'])) {
                //$res = $wpn->parse((int)$_GET['parsing'], $user['id']);
                //$m->pda($res);
            } elseif (isset($_GET['parsing']) and is_numeric($_GET['parsing'])) {
                $parse = $wpn->getUser((int)$_GET['parsing']);
                if (!$parse) $m->pda(['Такого предмета нет в инвентаре.']);
                else {
                    ?>
                    <div class="question m-5">
                        <div class="question-answer center">
                            <div class="small access-2">Данное действие нельзя будет отменить</div>
                            Вы действительно хотите разобрать <?php echo $parse['info']['name']; ?>?
                        </div>
                        <div class="question-option">
                            <a href="<?php echo($pg->_page > 1 ? '?page=' . $pg->_page . '&' : '?'); ?>parsing=<?php echo (int)$_GET['parsing']; ?>&yes"
                               class="href" style="margin-bottom: 1px;">Да</a>
                            <a href="<?php echo($pg->_page > 1 ? '?page=' . $pg->_page : '?'); ?>" class="href">Нет</a>
                        </div>
                    </div>
                    <?php
                }
            }

            // Разбор предмета
            if (isset($_GET['trash']) and is_numeric($_GET['trash']) and isset($_GET['yes'])) {
                $res = $wpn->trash((int)$_GET['trash'], $user['id']);
                $m->pda($res);
            } elseif (isset($_GET['trash']) and is_numeric($_GET['trash'])) {
                $trash = $wpn->getUser((int)$_GET['trash']);
                if (!$trash) $m->pda(['Такого предмета нет в инвентаре.']);
                else {
                    ?>
                    <div class="question m-5">
                        <div class="question-answer center">
                            <div class="small access-2">Данное действие нельзя будет отменить</div>
                            Вы действительно хотите выбросить <?php echo $trash['info']['name']; ?>?
                        </div>
                        <div class="question-option">
                            <a href="<?php echo($pg->_page > 1 ? '?page=' . $pg->_page . '&' : '?'); ?>trash=<?php echo (int)$_GET['trash']; ?>&yes"
                               class="href" style="margin-bottom: 1px;">Да</a>
                            <a href="<?php echo($pg->_page > 1 ? '?page=' . $pg->_page : '?'); ?>" class="href">Нет</a>
                        </div>
                    </div>
                    <?php
                }
            }

            $get = $db->getAll('SELECT `id` FROM `weapons_users` WHERE `id_user` = ? ' . $pg->getLimit('`id`'), [$user['id']]);
            echo '<div class="flex-container">';
            foreach ($get as $key) {
                $list = $wpn->getUser($key['id']);
                ?>
                <div class="flex-link">
                    <div class="flexpda main top">
                        <div class="flexpda-image">
                            <img class="top-img"
                                 src="/files/items/<?php echo $list['info']['slot']; ?>/<?php echo $list['info']['id']; ?>.png"
                                 title="<?php echo $list['info']['name'] ?>"
                                 alt="<?php echo $slot[$list['info']['slot']]['name']; ?>" width="64px"/>
                        </div>
                        <div class="flexpda-content" style="padding-left: 5px;">
                            <div class="pull-right small"><?php echo $slot[$list['info']['slot']]['name']; ?></div><?php echo $wpn->link($list['id'], false); ?>
                            <div class="clearfix"></div>
                            <div class="flex-container">
                                <div class="flex-link">
                                    <a class="href"
                                       href="<?php echo($pg->_page > 1 ? '?page=' . $pg->_page . '&' : '?'); ?>equip=<?php echo $list['id']; ?>"><?php echo($list['used'] == 1 ? 'Снять' : 'Надеть'); ?></a>
                                </div>
                                <div class="flex-link">
                                    <a class="href"
                                       href="<?php echo($pg->_page > 1 ? '?page=' . $pg->_page . '&' : '?'); ?>trash=<?php echo $list['id']; ?>">Выкинуть</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            echo $pg->render();
        } else $m->pda(['Инвентарь пуст.']);
        break;

    case 'objects':
        $title = 'Инвентарь :: Предметы';
        require '../../main/head.php';
        ?>
        <div class="flex-container center">
            <div class="flex-link"><a href="/inventory/" class="href">Экипировка</a></div>
            <div class="flex-link"><a href="/inventory/objects" class="href">Предметы</a></div>
            <div class="flex-link"><a href="/inventory/backgrounds" class="href">Задний план</a></div>
        </div>
        <?php
        if (isset($_GET['use']) and is_numeric($_GET['use'])) {
            $res = $obj->use($_GET['use'], $user['id']);
            $_SESSION['notify'] = $res;
            $m->to($_SERVER['HTTP_REFERER']);
        }
        $cnt = $db->getCount('SELECT count(`id`) FROM `objects_users` WHERE `id_user` = ?', [$user['id']]);
        if ($cnt > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($cnt);

            $get = $db->getAll('SELECT `id` FROM `objects_users` WHERE `id_user` = ? ' . $pg->getLimit('`id`'), [$user['id']]);
            echo '<div class="flex-container">';
            foreach ($get as $key) {
                $list = $obj->getUser($key['id']);
                ?>
                <div class="flex-link">
                    <div class="main">
                        <div class="flex-container">
                            <div class="flex-item">
                                <img src="/files/objects/<?php echo $list['info']['id']?>.png" style="height: 48px" alt="">
                            </div>
                            <div class="flex-link pull-left">
                                <h1><?php echo $obj->link($list['info']['id']); ?></h1> <span class="small access-2"><?php echo $list['count']; ?> ед.</span><br/>
                                <?php if ($list['info']['types'] == 'hp' or $list['info']['types'] == 'energy') : ?>
                                    <div class="quest-take">
                                        прибавит <?php echo $list['info']['what']; ?><?php echo($list['info']['types'] == 'hp' && $list['info']['whatType'] == 1 ? '% от макс.' : ''); ?> <?php echo($list['info']['types'] == 'hp' ? '<img src="/files/icons/hp.png" width="12px" />' : '<img src="/files/icons/energy.png" width="12px" />'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($list['info']['types'] == 'hp' or $list['info']['types'] == 'energy') : ?>
                    <div class="flex-container">
                        <div class="flex-link">
                            <a href="/inventory/objects?use=<?php echo $list['info']['id']; ?>" class="href">Использовать</a>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
                <?php
            }
            echo '</div>';
            echo $pg->render();
        } else $m->pda(['Инвентарь пуст.']);
        break;

    case 'backgrounds':
        $title = 'Инвентарь :: Задний план';
        require '../../main/head.php';
        ?>
        <div class="flex-container center">
            <div class="flex-link"><a href="/inventory/" class="href">Экипировка</a></div>
            <div class="flex-link"><a href="/inventory/objects" class="href">Предметы</a></div>
            <div class="flex-link"><a href="/inventory/backgrounds" class="href">Задний план</a></div>
        </div>
        <?php
        if (isset($_GET['use']) and is_numeric($_GET['use'])) {
            if (!$back = $db->get('SELECT * FROM `backgrounds` WHERE `id_user` = ? and `id` = ?', [$user['id'], $_GET['use']])) $error[] = 'У вас нет этого заднего плана';
            elseif ($back['used'] == 1) $error[] = 'Вы уже используете этот задний план';

            if (empty($error)){
                $db->query('UPDATE `backgrounds` SET `used` = ? WHERE `id_user` = ?', [0, $user['id']]);
                $db->query('UPDATE `backgrounds` SET `used` = ? WHERE `id_user` = ? and `id` = ?', [1, $user['id'], $_GET['use']]);
                $_SESSION['notify'][] = 'Вы успешно сменили задний план';
            } else $_SESSION['notify'] = $error;
            $m->to($_SERVER['HTTP_REFERER']);
        }
        $cnt = $db->getCount('SELECT COUNT(`id`) FROM `backgrounds` WHERE `id_user` = ?', [$user['id']]);
        if ($cnt > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($cnt);

            $get = $db->getAll('SELECT `backgrounds`.*, `background`.`name` FROM `backgrounds` JOIN `background` ON (`backgrounds`.`background` = `background`.`id`) WHERE `backgrounds`.`id_user` = ? ' . $pg->getLimit('`id`', 'ASC'), [$user['id']]);
            foreach ($get as $key) {
                ?>
                <div class="main mt-5">
                    <div class="flex-container">
                        <div class="flex-item">
                            <img src="/files/background/<?php echo $key['background'];?>.png" style="height: 80px" alt="">
                        </div>
                        <div class="flex-link pull-left">
                            <h1><?php echo $key['name'];?></h1><br/>
                            <?php if ($key['used'] == 0):?>
                                <a href="/inventory/backgrounds?use=<?php echo $key['id']; ?>" class="href m-5">Сменить локацию</a>
                            <?php endif ;?>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo $pg->render();
        }
        break;

    case 'equipment':
        $id = abs((int)$_GET['id']);
        $profile = $u->getInfo($id);
        if (!$profile) header('location: /');
        $title = 'Экипировка ' . $profile['login'];
        require '../../main/head.php';
        $equip = $wpn->getEquip($profile['id']);
        if (empty($equip)) {
            echo $m->pda(['На игроке нет надетой экипировки.']);
        } else {
            foreach ($equip as $list) {
                ?>
                <div class="block">
                    <table>
                        <tr>
                            <td width="53px" valign="top">
                                <img src="/files/items/<?php echo $list['info']['slot']; ?>/<?php echo $list['info']['id']; ?>.png"
                                     title="<?php echo $list['info']['name'] ?>"
                                     alt="<?php echo $slot[$list['info']['slot']]['name']; ?>" width="48px"/>
                            </td>
                            <td>
                                <div class="small"><?php echo $slot[$list['info']['slot']]['name']; ?></div><?php echo $wpn->link($list['id'], true); ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            }
        }
        echo "<a class='l' href='/id" . $id . "'>Вернуться назад</a>";
        break;
}
require '../../main/foot.php';

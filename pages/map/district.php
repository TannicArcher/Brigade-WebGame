<?php
require '../../main/main.php';
$id = abs((int)$_GET['id']);
$dist = $map->getInfoDistrict($id);
if (!$dist) die(header('Location: /map/'));
$title = $dist['name'];
require '../../main/head.php';
$district = $map->getUserDistrict($user['id'], $id);
if (!$district) {
    $db->query('INSERT INTO districts_users (id_district, id_user) VALUES (?,?)', [$id, $user['id']]);
    $m->to('/map/'.$id);
}
if ($id != 1) {
    $prevDist = $map->getUserDistrict($user['id'], ($id - 1));
    if (!$prevDist) $m->to('/map/');
    if ($prevDist['success'] < 3) {
        $_SESSION['notify'][] = 'Сначала выполните 3 раза все движухи на прошлом районе';
        $m->to('/map/');
    }
}
$dot = [1 => 'biz_1', 2 => 'biz_2', 3 => 'biz_3'];
if (isset($_GET['newDot']) && ($_GET['newDot'] == 1 || $_GET['newDot'] == 2 || $_GET['newDot'] == 3)) {
    ?>
    <h1>Точка №<?php echo $_GET['newDot'];?></h1>
    <?php
    $count['dots'] = $db->getCount('SELECT COUNT(`id`) FROM `dots` WHERE `needRepute` < ?', [$district['repute']]);
    if ($count['dots'] > 0) {
        $get = $db->getAll('SELECT * FROM `dots` WHERE `needRepute` < ? ORDER BY `giveBolts` DESC', [$district['repute']]);
        foreach ($get as $item):
        ?>
        <div class="outline">
            <div class="main">
                <div class="flex-container">
                    <div class="flex-item">
                        <img src="/files/dot/<?php echo $item['id'];?>.png" style="height: 64px" alt="">
                    </div>
                    <div class="flex-link pull-left">
                        <h1><?php echo $item['name'];?></h1><span class="access-2 small"><?php echo ($item['typeCrime'] == 'crime' ? 'криминальный':'легальный');?></span><br/>
                        <div class="small mh-5"><?php echo $item['about'];?></div>
                        <div class="quest-take">
                            Приносит <img src="/files/icons/bolts.png" alt=""> <?php echo $item['giveBolts'];?> каждые 5 минут
                        </div>
                        <div class="quest-take">
                            Макс.заработок <img src="/files/icons/bolts.png" alt=""> <?php echo $item['giveBolts'] * 96;?> за 8 часов
                        </div>
                        <?php
                        if (isset($_GET['select']) && $_GET['select'] == $item['id']) {
                            if ($item['needRepute'] > $district['repute']) $error[] = 'У тебя недостаточно репутации, чтобы начать этот бизнес';
                            if ($item['typePrice'] == 'bolts' && $user['bolts'] < $item['amountPrice']) $error[] = 'У тебя нет столько черного нала.';
                            if ($item['typePrice'] == 'rubles' && $user['rubles'] < $item['amountPrice']) $error[] = 'У тебя нет столько рублей.';
                            $check['dot'] = 'biz_'.$_GET['newDot'];
                            if ($district[$check['dot']] != NULL) $error[] = 'Сначала избавься от старого рабочего.';
            
                            if(empty($error)){
                                if ($item['typePrice'] == 'rubles') $u->takeRubles($user['id'], $item['amountPrice']);
                                    else $u->takeBolts($user['id'], $item['amountPrice']);

                                $db->query('UPDATE `districts_users` SET `'.$check['dot'].'` = ?, `'.$check['dot'].'_time` = ? WHERE `id_user` = ? and `id_district` = ?', [$item['id'], time()+28800, $user['id'], $id]);
                                $_SESSION['notify'][] = 'Вы успешно поставили рабочего на точку';
                                $m->to('/map/'.$id);
                            } else $m->pda($error);
                        } else echo '<a href="/map/'.$id.'?newDot='.$_GET['newDot'].'&select='.$item['id'].'" class="link">Поставить за '.$item['amountPrice'].' <img width="12px" src="/files/icons/'.$item['typePrice'].'.png" alt=""></a>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        endforeach;
    } else $m->pda(['У тебя слишком мало влияния на районе, приходи позже.']);
    echo '<a href="/map/'.$id.'" class="href mv-5">Вернуться на район</a>';
    require '../../main/foot.php';
    die();
}
if (isset($_GET['remove']) && ($_GET['remove'] == 1 || $_GET['remove'] == 2 || $_GET['remove'] == 3) && isset($_GET['yes'])){
    if ($district[$dot[$_GET['remove']]] == NULL) $m->pda(['Точка свободна, некого увольнять']);
    else {
        $db->query('UPDATE `districts_users` SET `'.$dot[$_GET['remove']].'` = ?, `'.$dot[$_GET['remove']].'_time` = ? WHERE `id_user` = ? and `id_district` = ?', [NULL, NULL, $user['id'], $id]);
        $_SESSION['notify'][] = 'Работник уволен. Точка свободна.';
        $m->to('/map/'.$id);
    }
} elseif (isset($_GET['remove']) && ($_GET['remove'] == 1 || $_GET['remove'] == 2 || $_GET['remove'] == 3)){
    ?>
    <div class="question mv-5">
        <div class="question-answer center access-2">
            Вы действительно хотите освободить точку №<?php echo $_GET['remove'];?>?<br>
        </div>
        <div class="question-option">
            <a href="/map/<?php echo $id;?>?remove=<?php echo $_GET['remove'];?>&yes" class="href" style="margin-bottom: 1px;">Да</a>
            <a href="/map/<?php echo $id;?>" class="href">Нет</a>
        </div>
    </div>
    <?php
}
if (isset($_GET['collect']) && ($_GET['collect'] == 1 || $_GET['collect'] == 2 || $_GET['collect'] == 3)){
    if ($district[$dot[$_GET['collect']]] == NULL) $m->pda(['Точка свободна. Откуда тут взять деньгам?']);
    else {
        $canTake = floor((28800 - ($district[$dot[$_GET['collect']].'_time'] - time())) /300);
        if ($canTake < 1) $m->pda(['Еще нечего собирать.']);
        else {
            if ($canTake > 96) $canTake = 96;
            $dotInfo = $map->getInfoDot($district[$dot[$_GET['collect']]]);
            $db->query('UPDATE `districts_users` SET `'.$dot[$_GET['collect']].'_time` = ? WHERE `id_user` = ? and `id_district` = ?', [time()+28800, $user['id'], $id]);
            $u->giveBolts($user['id'], $canTake * $dotInfo['giveBolts']);
            $u->updateDayQuest($user['id'], 'quest_3', 1);
            $_SESSION['notify'][] = 'Вы успешно собрали '.($canTake * $dotInfo['giveBolts']).' черного нала с точки.';
            $m->to('/map/'.$id);
        }
    }
}
if (isset($_GET['min'])){
    if (!isset($_COOKIE['min']) || $_COOKIE['min'] == 0){
        setcookie('min', 1, time() + 60 * 60 * 24 * 30, '/');
    } else {
        setcookie('min', 0, time() + 60 * 60 * 24 * 30, '/');
    }
    $m->to('/map/'.$id);
}
?>
<h1>Информация</h1>
<div class="flex-container center">
    <div class="flex-link">
        <div class="info-about"><?php echo $dist['name']; ?></div>
        <div class="info-title">Район</div>
    </div>
    <div class="flex-link">
        <div class="info-about"><?php echo $district['repute']; ?></div>
        <div class="info-title">Влияние</div>
    </div>
    <div class="flex-link">
        <div class="info-about"><?php echo $district['success']; ?></div>
        <div class="info-title">Прохождений</div>
    </div>
</div>
<div class="mv-5">
    <h1>Точки</h1> <a class="small" href="?min"><?php echo (!isset($_COOKIE['min']) || $_COOKIE['min'] == 0 ? 'свернуть':'развернуть');?></a>
    <div class="flex-container center">
    <?php foreach ($dot as $item => $key): ?>
    <?php $infoDot = $map->getInfoDot($district[$key]);?>   
    <div class="flex-link outline">
        <?php if (!isset($_COOKIE['min']) || $_COOKIE['min'] == 0):?>
        <div class="main">
            <div class="flex-container">
                <div class="flex-item">
                    <img src="/files/dot/<?php echo ($district[$key] == NULL ? 'default' : $district[$key]);?>.png" style="height: 64px" alt="">
                </div>
                <div class="flex-link pull-left">
                    <?php if ($district[$key] == NULL): ?>
                        <h1>Свободная точка</h1><br/>
                        <div class="quest-take">
                            Нет рабочего.
                        </div>
                    <?php else: 
                        ?>
                        <h1><?php echo $infoDot['name'];?></h1><span class="access-2 small"><?php echo ($infoDot['typeCrime'] == 'crime' ? 'криминальный':'легальный');?></span><br/>
                        <div class="quest-take">
                            Приносит <img src="/files/icons/bolts.png" alt=""> <?php echo $infoDot['giveBolts'];?> за каждые 5 минут
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="flex-container">
            <div class="flex-item">
                <div class="href">№<?php echo $item;?></div>
            </div>
            <?php if($district[$key] == NULL):?>
            <div class="flex-link">
                <a href="/map/<?php echo $id;?>?newDot=<?php echo $item;?>" class="href center">Поставить</a>
            </div>
            <?php else: 
                $canTake = floor((28800 - ($district[$key.'_time'] - time())) /300);
                ?>
                <div class="flex-link">
                    <?php echo ($canTake > 0 ? '<a href="/map/'.$id.'?collect='.$item.'" class="href">Собрать <div class="count">'.($canTake > 96 ? 96 : $canTake) * $infoDot['giveBolts'].' <img src="/files/icons/bolts.png" alt=""> '.($canTake > 96 ? '(макс.)':null).'</div><div class="clearfix"></div></a>':'<div class="href">Собирать нечего</div>');?>
                </div>
                <div class="flex-item">
                    <a href="/map/<?php echo $id;?>?remove=<?php echo $item;?>" class="href">X</a>
                </div>
            <?php endif;?>
        </div>
    </div>
    <?php endforeach;?>
</div>
<h1>Задания</h1>
<?php
if (isset($_GET['quest'])) {
    $q = abs(intval($_GET['quest']));
    if (empty($q)) $error[] = 'Выберите задание.';
    elseif (!is_numeric($q)) $error[] = 'Ошибка в запросе.'; // если id не является числом
    elseif (!array_key_exists($q, $maps[$id])) $error[] = 'Такого задания не существует';
    elseif ($district[$q] >= $maps[$id][$q]['max']) $error[] = 'Вы уже прошли это задание.';
    elseif ($q != 1 and $district[$q - 1] < $maps[$id][$q - 1]['max']) $error[] = 'Сначала пройдите предыдущее задание.';
    elseif ($user['energy'] < $maps[$id][$q]['energy']) $error[] = 'Недостаточно энергии.';

    if (!empty($error)) {
        $m->pda($error);
    } else {
        $map->updateQuest($q, $maps[$id][$q]['repute'], $user['id'], $id);
        $u->updateDayQuest($user['id'], 'quest_4', 1);

        $u->giveBolts($user['id'], $maps[$id][$q]['bolts']);
        $u->giveRepute($user['id'], $maps[$id][$q]['repute']);
        $u->takeEnergy($user['id'], $maps[$id][$q]['energy']);
        $u->giveExp($user['id'], $maps[$id][$q]['exp']);
        
        if ($opg = $clan->have($user['id'])) {
            $rep = floor($maps[$id][$q]['repute'] / 2);
            if ($rep >= 1) $clan->giveRepute($rep, $opg['id_group'], $user['id']);
        }


        $_SESSION['quest'][$q] = [
            'Энергия: -' . $maps[$id][$q]['energy'],
            'Опыт: +' . $maps[$id][$q]['exp'],
            'Влияние: +' . $maps[$id][$q]['repute'],
            'Черный нал.: +' . $maps[$id][$q]['bolts'],
            ($rep >= 1 ? 'Влияние ОПГ: +'.$rep : null),
        ];

        $randomDrop = [0 => 90, 1 => 10];
        if ($m->roulette($randomDrop) == 1) {
            $getObj = $db->getAll('SELECT `id`, `random` FROM `objects` WHERE `random` != ?', [0]);
            foreach ($getObj as $key) {
                $pop[$key['id']] = $key['random'];
            }
            $_SESSION['quest'][$q]['drop'] = $m->roulette($pop);
            $obj->give($_SESSION['quest'][$q]['drop'], $user['id']);
        }

        if ($q == 7 and ($district[$q] + 1) == $maps[$id][$q]['max']) {
            $map->reset($user['id'], $id);
            $u->giveBolts($user['id'], 100);
            $u->giveRepute($user['id'], 50);
            $_SESSION['notify'][] = 'Вы успешно выполнили все задания на районе. Держи бонус в виде 100 ед. черного нала и 50 ед. влияния.';
            $m->to('/map/' . $id . '#top');
        } else {
            $m->to('/map/' . $id . '#' . $q);
        }
    }
}
foreach ($maps[$id] as $key => $item) {
?>
    <div class="quest-container">
        <div class="flex-quest" id="<?php echo $key;?>">
            <div class="quest-block">
                <div class="quest-title"><?php echo $item['title']; ?></div>
                <div class="quest-progress">
                    Прогресс [ <?php echo $district[$key]; ?> из <?php echo $item['max']; ?> ]
                    <div class="clearfix mv-5"></div>
                    <div class="grid center" style="margin-bottom: 3px;">
                        <?php echo $map->showQuest($district[$key], $item['max']); ?>
                    </div>
                </div>
                <div class="quest-info">
                    <div class="quest-take">
                        <img src="/files/icons/energy.png" alt=""><?php echo $item['energy']; ?>
                    </div>
                    <div class="quest-go">
                    <?php if ($key > 1 && $district[$key - 1] < $maps[$id][$key - 1]['max']) : ?>
                    
                    <?php elseif ($district[$key] < $item['max']) : ?>
                        <a href="?quest=<?php echo $key; ?>" class="link center">Выполнить</a>
                    <?php endif; ?>
                    </div>
                    <div class="quest-take pull-right">
                        <img src="/files/icons/repute.png" width="14px" alt=""> <?php echo $item['repute']; ?> / 
                        <img src="/files/icons/bolts.png" alt=""> <?php echo $item['bolts']; ?> / 
                        <img src="/files/icons/exp.png" alt="" width="14px"> <?php echo $item['exp']; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($_SESSION['quest'][$key])):?>
        <div class="flex-quest">
            <div class="quest-block">
                <h1>Награда</h1><br>
                <div class="quest-take"><img src="/files/icons/energy.png" alt=""> <?php echo $_SESSION['quest'][$key][0];?></div>
                <div class="quest-take"><img src="/files/icons/exp.png" alt=""> <?php echo $_SESSION['quest'][$key][1];?></div>
                <div class="quest-take"><img src="/files/icons/repute.png" alt=""> <?php echo $_SESSION['quest'][$key][2];?></div>
                <div class="quest-take"><img src="/files/icons/bolts.png" alt=""> <?php echo $_SESSION['quest'][$key][3];?></div>
                <?php echo (isset($_SESSION['quest'][$key][4]) ? '<div class="quest-take"><img src="/files/icons/repute.png" alt=""> '.$_SESSION['quest'][$key][4].'</div>':null);?>
                <?php
                if (isset($_SESSION['quest'][$key]['drop'])):
                    $drop = $_SESSION['quest'][$key]['drop'];
                    $dropInfo = $obj->get($drop);
                ?>
                    <br/>
                    <div class="main">
                        <h1>Выпал предмет</h1>
                        <div class="flex-container">
                            <div class="flex-item">
                                <img src="/files/objects/<?php echo $drop;?>.png" style="height: 64px" alt="">
                            </div>
                            <div class="flex-link pull-left">
                                <h1><?php echo $dropInfo['name'];?></h1><span class="access-2 small">энергия</span><br/>
                                <div class="quest-take">
                                    Приносит <img src="/files/icons/energy.png" alt=""> <?php echo $dropInfo['what'];?> энергии
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <?php unset($_SESSION['quest']);?>
        <?php endif;?>
    </div>
<?php
}
require '../../main/foot.php';
?>
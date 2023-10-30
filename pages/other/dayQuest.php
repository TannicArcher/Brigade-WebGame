<?php
require '../../main/main.php';
$title = 'Ежедневные задания';
require '../../main/head.php';
$get = $db->get('SELECT * FROM `everyDay` WHERE `id_user` = ?', [$user['id']]);
?>
<h1>Ежедневные задания</h1><br/>
<div class="outline mv-5 quality-rare">
    Выполните все задания на сегодня и получите 5 <img src="/files/icons/rubles.png" alt="">
</div>
<div class="quest-block">
    <div class="quest-title">Получить 50 репутации</div>
    <div class="quest-info">
        <div class="quest-take">
            Прогресс <?php echo $get['quest_1'];?> из 50
        </div>
        <?php echo ($get['quest_1'] >= 50 ? '<div class="quest-take access-3">Выполнено</div>' : null);?>
    </div>
</div>
<div class="quest-block">
    <div class="quest-title">Потрать 100 энергии</div>
    <div class="quest-info">
        <div class="quest-take">
            Прогресс <?php echo $get['quest_2'];?> из 100
        </div>
        <?php echo ($get['quest_2'] >= 100 ? '<div class="quest-take access-3">Выполнено</div>' : null);?>
    </div>
</div>
<div class="quest-block">
    <div class="quest-title">Собери прибыль с точки 3 раза</div>
    <div class="quest-info">
        <div class="quest-take">
            Прогресс <?php echo $get['quest_3'];?> из 3
        </div>
        <?php echo ($get['quest_3'] >= 3 ? '<div class="quest-take access-3">Выполнено</div>' : null);?>
    </div>
</div>
<div class="quest-block">
    <div class="quest-title">Выполни задания на районе 20 раз</div>
    <div class="quest-info">
        <div class="quest-take">
            Прогресс <?php echo $get['quest_4'];?> из 20
        </div>
        <?php echo ($get['quest_4'] >= 20 ? '<div class="quest-take access-3">Выполнено</div>' : null);?>
    </div>
</div>
<div class="quest-block">
    <div class="quest-title">Закончи 1 тренировку</div>
    <div class="quest-info">
        <div class="quest-take">
            Прогресс <?php echo $get['quest_5'];?> из 1
        </div>
        <?php echo ($get['quest_5'] >= 1 ? '<div class="quest-take access-3">Выполнено</div>' : null);?>
    </div>
</div>
<?php
if ($get['quest_1'] >= 50 && $get['quest_2'] >= 100 && $get['quest_3'] >= 3 && $get['quest_4'] >= 20 && $get['quest_5'] >= 1 && $get['take'] == 0){
    if (isset($_GET['take'])) {
        $db->query('UPDATE `everyDay` SET `take` = ? WHERE `id_user` = ?', [1, $user['id']]);
        $u->giveRubles($user['id'], 5);
        $_SESSION['notify'][] = 'Вы успешно выполнили все ежедневные задачи';
        $m->to('/dayQuest');
    } else {
        echo '<a href="?take" class="href mv-5">Забрать награду</a>';
    }
}
require '../../main/foot.php';

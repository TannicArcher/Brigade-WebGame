<?php
require '../../main/main.php';
$title = 'Создание группировки';
require '../../main/head.php';
$cl = $clan->have($user['id']);
if ($cl) header('Location: /clan/');
if (isset($_POST['create'])) {
    if ($cl) $error[] = 'Вы уже состоите в группировке';
    if ($user['rubles'] < CLAN_CREATE_RUBLES) $error[] = 'Не хватает рублей, иди копи';
    if ($user['repute'] < CLAN_CREATE_REPUTE) $error[] = 'Ты пока мало влиятелен, приходи позже';
    if ($db->get('SELECT `id` FROM `groups` WHERE `name` = ?', [$_POST['name']])) $error[] = 'Такая группировка уже есть';

    if (empty($error)) {
        $gid = $clan->create($_POST['name'], $_POST['about'], $user['id']);
        $m->to('/clan/'.$gid);
    } else $m->pda($error);
}
?>
    <h1>Организация преступной группировки</h1>
    <form method="post">
        <small>Название группировки (4-32 символа)</small>
        <input type="text" name="name" placeholder="Введите название" minlength="4" maxlength="32">
        <small>Краткое описание группировки (4-255 символов)</small>
        <textarea name="about" rows="1" maxlength="255" minlength="4" placeholder="Введите описание"></textarea>
        <div class="flex-container">
            <div class="flex-link">
                <input class="w-100" type="submit" name="create" value="Организовать">
            </div>
            <div class="flex-link aitem">
                <div class="center access-2">
                    <div class="outline mv-5">Требования к созданию</div>
                    <img src="/files/icons/repute.png" alt="*" width="12px"> <?php echo CLAN_CREATE_REPUTE; ?> репутации<br/>
                    <img src="/files/icons/rubles.png" alt="*" width="12px"> <?php echo CLAN_CREATE_RUBLES;?>
                    рублей<br/>
                </div>
            </div>
        </div>
    </form>
<?php
require '../../main/foot.php';

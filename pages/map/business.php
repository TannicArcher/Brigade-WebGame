<?php
include '../../main/main.php';
switch ($method) {
    default:
        $title = 'Бизнесы';
        include '../../main/head.php';
        $count = $db->getCount('SELECT COUNT(`id`) FROM `business` WHERE `id_user` = ?', [$user['id']]);
        if (isset($_GET['buy']) and isset($_GET['yes']))
        {
            if ($buyBiz[$count] > $user['rubles']) $error[] = 'Недостаточно рублей, чтобы подмять точку';

            if (empty($error)) {
                $db->query('INSERT INTO `business` (`id_user`) VALUES (?)', [$user['id']]);
                $u->takeRubles($user['id'], $buyBiz[$count]);
                $m->to('/district/');
            } else {
                $m->pda($error);
            }

        } elseif (isset($_GET['buy'])) {
            ?>
            <div class="question mv-5">
                <div class="question-answer center access-2">
                    Вы действительно хотите подмять точку<?php echo ($buyBiz[$count] > 0 ? ' за '.$buyBiz[$count].' <img src="/files/icons/rubles.png" alt="*">':null);?>?<br/>
                </div>
                <div class="question-option">
                    <a href="/district/?buy&yes" class="href"
                        style="margin-bottom: 1px;">Да</a>
                    <a href="/district/" class="href">Нет</a>
                </div>
            </div>
            <?php
        } else {
            if ($count > 0) {
                $bizs = $db->getAll('SELECT * FROM `business` WHERE `id_user` = ?', [$user['id']]);
                foreach ($bizs as $biz) {
                    if (!$biz['id_dot']) {
                        echo 'пустая точка';
                    } else {
                        $info = $map->getInfoBusiness($biz['id_dot']);
                        $debug::view($info);
                    }
                }
            } else {
                ?>
                <div class="center" style="margin: 20px 0">
                    <img src="/files/icons/newBiz.png" alt=""><br/>
                    <a href="?buy" class="btn-orange mv-5" style="padding: 10px 20px">Подмять первую точку</a>
                    <div class="clearfix"></div>
                </div>
                <?php
            }
        }
        break;
    case 'ground':
        $title = 'Бизнесы';
        include '../../main/head.php';
        break;
    case 'view':
        $title = 'Бизнесы';
        include '../../main/head.php';
        break;
}
include '../../main/foot.php';
<?php
include '../../main/main.php';
$title = 'Рынок';
include '../../main/head.php';
if ($user['access'] < 3) $m->to('/404');
switch ($method) {
    case 'view':
        $type = trim($_GET['type']);
        if (!array_key_exists($type, $slot)) $m->to('/market');

        $cnt = $db->getCount('SELECT COUNT(id) FROM `weapons` WHERE `slot` = ? and `how` = ?', [$type, 'market']);
        if ($cnt > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($cnt);
            $get = $db->getAll('SELECT `id` FROM `weapons` WHERE `slot` = ? and `how` = ?  ' . $pg->getLimit('id'), [$type, 'market']);

            echo '<div class="flex-container">';
            foreach ($get as $key) {
                $listed = $wpn->get($key['id']);
                ?>
                <div class="flex-link">
                    <div class="flexpda main top">
                        <div class="flexpda-image">
                            <img class="top-img" src="/files/items/<?php echo $listed['slot']; ?>/<?php echo $listed['id']; ?>.png" title="<?php echo $listed['name'] ?>" alt="<?php echo $slot[$listed['slot']]['name']; ?>" width="64px" />
                        </div>
                        <div class="flexpda-content" style="padding-left: 5px;">
                            <span class="quality-<?php echo $listed['quality']; ?>"><?php echo $listed['name']; ?></span>
                            <hr />
                            <div class="flex-container">
                                <div class="flex-link">
                                    <div class="info-about">
                                        <?php echo ceil($listed['amount']); ?> <img src='/files/icons/<?php echo ($listed['price'] == 'rubles' ? 'rubles' : 'bolts'); ?>.png' width='12px' />
                                    </div>
                                    <div class="info-title">Цена</div>
                                </div>
                                <div class="flex-link">
                                    <div class="info-about"><?php echo $quality[$listed['quality']]; ?></div>
                                    <div class="info-title">Качество</div>
                                </div>
                                <div class="flex-link">
                                    <a class="href" href="/market/equipment/<?php echo $type; ?>/<?php echo $listed['id']; ?>">Подробнее</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            echo $pg->render();
        } else $m->pda(['Нет вещей для продажи']);
        echo "<a class='href' href='/market'> Вернуться к продавцу</a>";
        break;
    case 'info':
        $type = trim($_GET['type']);
        $wid = abs((int) $_GET['wid']);
        $equip = $wpn->get($wid);
        if (!$equip or $equip['how'] != 'market') header('Location: /market');
        ?>
        <div class="flexpda main top">
            <div class="flexpda-image">
                <img class="top-img" src="/files/items/<?php echo $equip['slot']; ?>/<?php echo $equip['id']; ?>.png" title="<?php echo $equip['name'] ?>" alt="" width="64px" />
            </div>
            <div class="flexpda-content" style="padding-left: 5px;">
                <span class="quality-<?php echo $equip['quality']; ?>"><?php echo $equip['name']; ?></span><br />
                <span class="small">Описание: <?php echo $equip['about']; ?></span><br />
                <?php
                if (isset($equip['stats'])) {
                    echo '<span class="access-3">Характеристики экипировки:</span><br/>';
                    foreach ($equip['stats'] as $key) {
                        echo $slot[$key['atrb']]['info'] . ': ' . ($key['bonus'] > 0 ? '+' : '-') . $key['bonus'] . ' ед.<br/>';
                    }
                }
                ?>
                <hr />
                <div class="flex-container">
                    <div class="flex-link">
                        <div class="info-about">
                            <?php echo ceil($equip['amount']); ?> <img src='/files/icons/<?php echo ($equip['price'] == 'rubles' ? 'rubles' : 'bolts'); ?>.png' width='12px' />
                        </div>
                        <div class="info-title">Цена</div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about"><?php echo $quality[$equip['quality']]; ?></div>
                        <div class="info-title">Качество</div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about"><?php echo $slot[$equip['slot']]['name']; ?></div>
                        <div class="info-title">Вид</div>
                    </div>
                    <div class="flex-link">
                        <?php
                        if (isset($_GET['buy']) and isset($_GET['run'])) {
                            $buy = $npc->buyEquip($equip['id'], $user['id'], $equip['price'], $equip['amount']);
                            if ($buy == 200) {
                                $_SESSION['notify'][] = 'Вы успешно купили себе новую вещь';
                                $m->to('/inventory/');
                            } else $m->pda($buy);
                        } elseif (isset($_GET['buy'])) {
                            ?>
                            <div class="question">
                                <div class="question-answer center">
                                    Купить <span class="quality-<?php echo $equip['quality']; ?>">"<?php echo $equip['name']; ?>"</span></strong> за <?php echo ceil($equip['amount']); ?> <img src='/files/icons/<?php echo ($equip['price'] == 'rubles' ? 'rubles' : 'bolts'); ?>.png' width='12px' />?<br />
                                </div>
                                <div class="question-option">
                                    <a href="?buy&run" class="href" style="margin-bottom: 1px;">Да</a>
                                    <a href="?" class="href">Нет</a>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo '<a class="href" href="?buy">Купить</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <a href="/market/equipment/<?php echo $type; ?>" class="href" style="margin-top: 5px;">Вернуться назад к категории</a>
        <?php
        break;
    default:
        ?>
        <div class="flex-link">
            <div class="flexpda">
                <div class="flexpda-content" style="padding-left: 5px;">
                    <div class="dialog-he">
                        <span class="access-3">Продавец</span><br />
                        — Подходи, покупай. Идешь мимо? Не зевай! Всё барахло здесь.
                    </div>
                    <div class="flex-container center">
                        <div class="flex-link">
                            <a href="/market/equipment/head" class="href">Голова</a>
                        </div>
                        <div class="flex-link">
                            <a href="/market/equipment/top" class="href">Верхняя одежда</a>
                        </div>
                        <div class="flex-link">
                            <a href="/market/equipment/body" class="href">Тело</a>
                        </div>
                        <div class="flex-link">
                            <a href="/market/equipment/accessory" class="href">Аксессуары</a>
                        </div>
                        <div class="flex-link">
                            <a href="/market/equipment/boot" class="href">Ноги</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="/map/" class="href m-5">Вернуться назад</a>
        <?php
        break;
}
include '../../main/foot.php';
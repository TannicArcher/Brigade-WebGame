<?php
require '../../main/main.php';
switch ($method) {
    default:
        $title = 'Программа «Позови братка»';
        require '../../main/head.php';
        ?>
        <h1>Подробнее о программе «Позови братка»</h1><br/>
        Данная программа сделана с целью увеличения количества пользователей в игре, что позволит более разнообразить игровой процесс.
        <br/>
        <h1>Как это работает?</h1><br/>
        Вы, пересылаете своему другу специальную ссылку-приглашение, перейдя по которой и начав игру, он станет Вашим рефералом.
        <br/>
        <h1>Что Вы за это получите?</h1><br/>
        1. При достижении Вашим другом 10 уровня персонажа, каждый из Вас получит дополнительно 10 рублей на свой внутреигровой счет.
        <br/>
        2. При покупке Вашим другом внутреигровых рублей путем пополнения счета реальными средствами, Вы будете получать ВСЕГДА на свой счет 5% от его покупки (друг при этом не теряет ничего).
        <br/>
        3. Достижение (в будущем).<br/>
        <h1>Что нужно сделать?</h1><br/>
        Скопируй и передай другу ссылку ниже <br/>
        <input type="text" value="https://brigada.mobi/r/<?php echo $user['id']; ?>"><br/><br/>
        <h1 class="quality-rare">ВАЖНО</h1><br/>
        1. Нельзя регистрировать свои новые аккаунты по своей же реферальной ссылке. (наказание - блокировка аккаунтов, включая основной)
        <br/>
        2. Рефералов может быть сколько угодно, с каждого из них Вы будете получать свои %.<br/>
        3. Администрация имеет право изменить бонус за 10 уровень и % бонуса за покупку в любой момент, в целях регулирования игровой экономики, без согласия и уведомления игроков.
        <br/>
        <a href="/referral/list" class="href mv-5">Мои рефералы <span
                    class="count"><?php echo $db->getCount('SELECT COUNT(`id`) FROM `refferals_in` WHERE `id_user` = ?', [$user['id']]); ?></span></a>
        <?php
        break;
    case 'list':
        $title = 'Мои братки';
        require '../../main/head.php';
        $cnt = $db->getCount('SELECT COUNT(`id`) FROM `refferals_in` WHERE `id_user` = ?', [$user['id']]);
        if ($cnt > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($cnt);
            $get = $db->getAll('SELECT * FROM `refferals_in` WHERE `id_user` = ? ' . $pg->getLimit('`dateAdd`'), [$user['id']]);
            foreach ($get as $key) {
                $player = $u->getInfo($key['id_ref']);
                ?>
                <div class="block">
                    <a href="/id<?php echo $key['id_ref'];?>" class="flex-container center">
                        <div class="flex-link">
                            <div class="info-about"><?php echo $u->getLogin($key['id_ref'], false); ?></div>
                            <div class="info-title">Прозвище</div>
                        </div>
                        <div class="flex-link">
                            <div class="info-about"><?php echo $player['level']; ?></div>
                            <div class="info-title">Уровень</div>
                        </div>
                        <div class="flex-link">
                            <div class="info-about"><?php echo $player['repute']; ?></div>
                            <div class="info-title">Репутация</div>
                        </div>
                        <div class="flex-link">
                            <div class="info-about"><?php echo date('j.m.Y в H:i:s', $key['dateAdd']); ?></div>
                            <div class="info-title">Дата регистрации</div>
                        </div>
                    </a>
                    <div class="clearfix"></div>
                </div>
                <?php
            }
            echo $pg->render();
        } else {
            $m->pda(['Ты еще не позвал братков']);
        }
        echo '<a class="href mv-5" href="/referral">Вернуться назад</a>';
        break;
}
require '../../main/foot.php';
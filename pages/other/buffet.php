<?php
include '../../main/main.php';
$title = 'Буфет';
include '../../main/head.php';
if (isset($_GET['buy']) and isset($_GET['yes'])){
    if ($user['rubles'] < 5) $error[] = 'Недостаточно рублей. Приходи, как будут деньги.';
    elseif ($user['energy'] == $user['max_energy']) $error[] = 'Ты и так полон сил. Не трать мое время';

    if (empty($error)) {
        $u->giveEnergy($user['id'], $user['max_energy']);
        $u->takeRubles($user['id'], 5);
        $_SESSION['notify'][] = 'Ты успешно восстановил всю энергию за 5 рублей.';
        $m->to('/buffet/');
    } else {
        $_SESSION['notify'] = $error;
        $m->to('/buffet/');
    }
} elseif (isset($_GET['buy'])) {
    ?>
    <div class="question mv-5">
        <div class="question-answer center access-2">
            Вы действительно хотите восстановить энергию за 5 <img src="/files/icons/rubles.png" alt="">?<br/>
        </div>
        <div class="question-option">
            <a href="/buffet?buy&yes" class="href"
                style="margin-bottom: 1px;">Да</a>
            <a href="/buffet/" class="href">Нет</a>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="flex-container">
        <div class="profile">
            <div class="profile__name center">
                <div class="flex-container mv-5" style="align-items: center">
                    <div class="flex-link">
                        <div class="info-about">
                            Столовая
                        </div>
                    </div>
                    <div class="flex-link">
                        <div class="info-about">за 5 <img src="/files/icons/rubles.png" alt=""></div>
                    </div>
                </div>
                Восстановится вся <img src="/files/icons/energy.png" alt="">
            </div>
            <div class="profile__image">
                <img src='/files/background/buffet.png' width='320px'/>
            </div>
            <div class="profile__name">
                <a href="?buy" class="link center" style="margin: 5px 15px">Похавать</a>
            </div>
        </div>
    </div>
    <?php
}
include '../../main/foot.php';
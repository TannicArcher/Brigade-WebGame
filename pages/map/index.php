<?php
require '../../main/main.php';
$title = 'Районы влияния';
require '../../main/head.php';
?>
    <h1>Районы, кварталы</h1>
<?php
foreach ($map->getAllDistrict() as $dist):
    $infoDistrict = $map->getInfoDistrict($dist['id']);
    $userDistrict = $map->getUserDistrict($user['id'], $dist['id']);
    if ($userDistrict['id_district'] != 1) $prevDistrict = $map->getUserDistrict($user['id'], $dist['id'] - 1);
?>
<div class="block">
    <div class="flexpda-content" style="padding: 5px">
        <div class=" flex-container center">
            <div class="flex-link">
                <div class="info-about"><?php echo $infoDistrict['name']; ?></div>
                <div class="info-title">Район</div>
            </div>
            <div class="flex-link">
                <div class="info-about"><?php echo(!$userDistrict ? 0 : $userDistrict['repute']); ?></div>
                <div class="info-title">Ваше влияние</div>
            </div>
            <div class="flex-link">
                <div class="info-about"><?php echo(!$infoDistrict['max'] || $infoDistrict['max']['repute'] == 0 ? 'Стань первым!' : $u->getLogin($infoDistrict['max']['id_user'], true).' <span class="small"><img src="/files/icons/repute.png" width="10px" /> '.$infoDistrict['max']['repute'].'</span>'); ?></div>
                <div class="info-title">Крышует район</div>
            </div>
            <div class="flex-link">
                <div class="info-about"><?php echo(!$infoDistrict['max_today'] || $infoDistrict['max_today']['repute_today'] == 0 ? 'Стань первым!' : $u->getLogin($infoDistrict['max_today']['id_user'], true).' <span class="small"><img src="/files/icons/repute.png" width="10px" /> '.$infoDistrict['max_today']['repute_today'].'</span>'); ?></div>
                <div class="info-title">Особо опасен</div>
            </div>
            <?php if($dist['id'] == 1 || $prevDistrict['success'] >= 3):?>
            <div class="flex-link">
                <a href="/map/<?php echo $dist['id'];?>" class="href">Заехать</a>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
<?php endforeach;
require '../../main/foot.php';
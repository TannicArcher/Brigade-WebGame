<div class="flex-container">
    <div class="flex-link center">
        <table class="w-100 mv-5">
            <tr>
                <td>
                    <?php
                    $s['head'] = $wpn->getEquipSlot('head', $id);
                    if ($s['head']) {
                        echo '<a href="/view/equipments/' . $s['head']['id'] . '"><img src="/files/items/head/' . $s['head']['id_weapon'] . '.png" title="' . $s['head']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/head/default.png" alt="img">';
                    ?>
                </td>
                <td rowspan="4">
                    <div class="block">
                        <?php echo $u->getLogin($id); ?>
                    </div>
                    <div class="block">
                        <?php echo $under_title; ?>
                    </div>
                </td>
                <td>
                    <?php
                    $s['knife'] = $wpn->getEquipSlot('knife', $id);
                    if ($s['knife']) {
                        echo '<a href="/view/equipments/' . $s['knife']['id'] . '"><img class="quality-border ' . $s['knife']['info']['quality'] . '" src="/files/items/knife/' . $s['knife']['id_weapon'] . '.png" title="' . $s['knife']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/knife/default.png" alt="img">';
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $s['body'] = $wpn->getEquipSlot('body', $id);
                    if ($s['body']) {
                        echo '<a href="/view/equipments/' . $s['body']['id'] . '"><img src="/files/items/body/' . $s['body']['id_weapon'] . '.png" title="' . $s['body']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/body/default.png" alt="img">';
                    ?>
                </td>
                <td>
                    <?php
                    $s['pistol'] = $wpn->getEquipSlot('pistol', $id);
                    if ($s['pistol']) {
                        echo '<a href="/view/equipments/' . $s['pistol']['id'] . '"><img src="/files/items/pistol/' . $s['pistol']['id_weapon'] . '.png" title="' . $s['pistol']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/pistol/default.png" alt="img">';
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $s['hand'] = $wpn->getEquipSlot('hand', $id);
                    if ($s['hand']) {
                        echo '<a href="/view/equipments/' . $s['hand']['id'] . '"><img src="/files/items/hand/' . $s['hand']['id_weapon'] . '.png" title="' . $s['hand']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/hand/default.png" alt="img">';
                    ?>
                </td>
                <td>
                    <?php
                    $s['gun'] = $wpn->getEquipSlot('gun', $id);
                    if ($s['gun']) {
                        echo '<a href="/view/equipments/' . $s['gun']['id'] . '"><img src="/files/items/gun/' . $s['gun']['id_weapon'] . '.png" title="' . $s['gun']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/gun/default.png" alt="img">';
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $s['boot'] = $wpn->getEquipSlot('boot', $id);
                    if ($s['boot']) {
                        echo '<a href="/view/equipments/' . $s['boot']['id'] . '"><img src="/files/items/boot/' . $s['boot']['id_weapon'] . '.png" title="' . $s['boot']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/boot/default.png" alt="img">';
                    ?>
                </td>
                <td>
                    <?php
                    $s['accessory'] = $wpn->getEquipSlot('accessory', $id);
                    if ($s['accessory']) {
                        echo '<a href="/view/equipments/' . $s['accessory']['id'] . '"><img src="/files/items/accessory/' . $s['accessory']['id_weapon'] . '.png" title="' . $s['accessory']['info']['name'] . '" alt="img"></a>';
                    } else echo '<img src="/files/items/accessory/default.png" alt="img">';
                    ?>
                </td>
            </tr>
        </table>
        <div class="block small mv-5">
            Регистрация: <span
                class="small"><?php echo date("d.m.Y в H:i:s", $profile['addDate']); ?></span><br/>
            Был замечен: <span class="small"><?php echo date("d.m.Y в H:i:s", $profile['updDate']); ?></span>
        </div>
    </div>
</div>
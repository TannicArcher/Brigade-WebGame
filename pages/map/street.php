<?php
require '../../main/main.php';
$title = 'Улица';
require '../../main/head.php';
?>
    <div class="flexpda main top">
        <div class="flexpda-image">
            <img class="top-img" src="/files/items/knife/1.png" title="Заточка" alt="нож" width="64px">
        </div>
        <div class="flexpda-content" style="padding-left: 5px;">
            <div class="flex-container">
                <div class="flex-link">
                    <a class="href mv-5" href="?press">Прессануть лоха</a>
                    <a class="href mv-5" href="?energy">Подгон 50 <img src="/files/icons/energy.png" alt=""> от братвы</a>
                </div>
            </div>
        </div>
    </div>
    <a href="/street?energy" class="link">Получить подгон</a>
    <a href="/street?coin" class="link">Отжать мелочь</a>
<?php
require '../../main/foot.php';

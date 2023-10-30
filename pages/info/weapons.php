<?php
require '../../main/main.php';

switch ($method) {
  default:
    $id = abs((int) $_GET['id']);
    $weapon = $wpn->get($id);
    if (!$weapon) header('location: /');
    $title = 'Просмотр «' . $weapon['name'] . '»';
    require '../../main/head.php';
?>
    <div class="flexpda main top">
      <div class="flexpda-image">
        <img class="top-img" src="/files/items/<?php echo $weapon['slot']; ?>/<?php echo $weapon['id']; ?>.png" title="<?php echo $weapon['name'] ?>" alt="<?php echo $slot[$weapon['slot']]['name']; ?>" width="64px" />
      </div>
      <div class="flexpda-content" style="padding-left: 5px;">
        <div class="outline"><?php echo $weapon['name']; ?></div>
        <div class=" flex-container center">
          <div class="flex-link outline">
            <div class="info-about"><span class="quality-<?php echo $weapon['quality']; ?>"><?php echo $quality[$weapon['quality']]; ?></span></div>
            <div class="info-title">качество</div>
          </div>
          <div class="flex-link outline">
            <div class="info-about"><?php echo $slot[$weapon['slot']]['name']; ?></div>
            <div class="info-title">слот</div>
          </div>
          <div class="flex-link outline">
            <div class="info-about"><?php echo $weapon['lvl']; ?></div>
            <div class="info-title">мин. уровень</div>
          </div>
        </div>
      </div>
    </div>
    <?php if (isset($weapon['stats'])): ?>
      <div class="block mv-5">
        <h1>Характеристики экипировки</h1><br/>
        <?php 
          foreach ($weapon['stats'] as $key) {
            echo ($key['bonus'] > 0 ? '<span class="access-1">» Увеличение</span>' : '<span class="access-2">» Уменьшение</span>') . ' ' . $atrb[$key['atrb']] . ' на ' . abs($key['bonus']) . ' ед.<br/>';
          }
        ?>
      </div>
    <?php endif; ?>
    <div class="block mv-5">
      <h1>Краткая информация</h1><br/>
      Описание: <?php echo $weapon['about']; ?><br />
      Код для вставки<br/>
      <input type="text" name="id" value="@item<?php echo $id;?>" disabled />
    </div>
    <?php
    $check['prev'] = $db->get('SELECT id, name FROM weapons WHERE id < ? ORDER BY id DESC', [$id]);
    $check['next'] = $db->get('SELECT id, name FROM weapons WHERE id > ?', [$id]);
    echo '<div class="flex-container">';
    if ($check['prev']) {
      echo "<div class='flex-link'><a href='/wiki/equipments/{$check['prev']['id']}' class='href'>« {$check['prev']['name']}</a></div>";
    }
    if ($check['next']) {
      echo "<div class='flex-link'><a href='/wiki/equipments/{$check['next']['id']}' class='href right'>{$check['next']['name']} »</a></div>";
    }
    echo '</div>';
    break;

  case 'equipment':
    $id = abs((int) $_GET['id']);
    $weapon = $wpn->getUser($id);
    if (!$weapon) header('location: /');
    $title = 'Просмотр «' . $weapon['info']['name'] . '»';
    require '../../main/head.php';
    ?>
    <div class="flex-container top">
      <div class="flexpda main" style="margin: 5px;">
        <div class="flexpda-image">
          <img class="top-img" src="/files/items/<?php echo $weapon['info']['slot']; ?>/<?php echo $weapon['info']['id']; ?>.png" title="<?php echo $weapon['info']['name'] ?>" alt="<?php echo $weapon['info']['name'] ?>" width="64px" />
        </div>
        <div class="flexpda-content" style="padding-left: 5px;">
          <span class="quality-<?php echo $weapon['info']['quality']; ?>"><?php echo $weapon['info']['name']; ?></span><br />
          Описание: <?php echo $weapon['info']['about']; ?><br />
          <?php
          if (isset($weapon['info']['stats'])) {
            echo '<span class="access-3">Характеристики экипировки:</span><br/>';
            foreach ($weapon['info']['stats'] as $key) {
              echo ($key['bonus'] > 0 ? '<span class="access-1">» Увеличение</span>' : '<span class="access-2">» Уменьшение</span>') . ' ' . $atrb[$key['atrb']] . ' на ' . abs($key['bonus']) . ' ед.<br/>';
            }
          }
          ?>
        </div>
      </div>
    </div>
    <div class="flex-container center">
      <div class="flex-link">
        <div class="info-about">
          <?php echo $weapon['id_weapon']; ?>
        </div>
        <div class="info-title">ID</div>
      </div>
      <div class="flex-link">
        <div class="info-about">
          <?php echo $weapon['id']; ?>
        </div>
        <div class="info-title">Личный ID</div>
      </div>
      <div class="flex-link">
        <div class="info-about">
          <?php echo $u->getLogin($weapon['id_user'], true); ?>
        </div>
        <div class="info-title">Владелец</div>
      </div>
      <div class="flex-link">
        <div class="info-about">
          <span class="quality-<?php echo $weapon['info']['quality']; ?>"><?php echo $quality[$weapon['info']['quality']]; ?></span>
        </div>
        <div class="info-title">Качество</div>
      </div>
      <div class="flex-link">
        <div class="info-about"><?php echo $weapon['info']['lvl']; ?></div>
        <div class="info-title">Уровень</div>
      </div>
      <div class="flex-link">
        <div class="info-about"><?php echo $slot[$weapon['info']['slot']]['name']; ?></div>
        <div class="info-title">Слот</div>
      </div>

      <?php if (isset($weapon['wear'])):?>
      <div class="flex-link">
        <div class="info-about"><?php echo $weapon['wear'];?>%</div>
        <div class="info-title">Прочность</div>
      </div>

      <?php endif; ?>
    </div>
<?php
    if (isset($_SERVER['HTTP_REFERER'])) echo "<a href='{$_SERVER['HTTP_REFERER']}' class='href m-5'>Вернуться назад</a>";
    break;
}
require '../../main/foot.php';

<?php
require '../../main/main.php';

switch ($method) {
  default:
    $title = 'Рейтинг игроков';
    require '../../main/head.php';
    $count = $db->getCount('SELECT count(id) FROM users', []);

    $pg = new \Game\Paginations(10, 'page');
    $pg->setTotal($count);
    $get = $db->getAll('SELECT id FROM users ' . $pg->getLimit('`repute`'), []);

    $num = 1 * ($pg->_page * 10 - 9);
    foreach ($get as $key) {
      $player = $u->getInfo($key['id']);
?>
      <div class="block <?php echo ($user['id'] == $key['id'] ? 'my-place' : null); ?> <?php echo ($num == 1 ? 'first-place' : null); ?>">
        <a href="/id<?php echo $key['id'];?>" class="flex-container center">
          <div class="flex-link">
            <div class="info-about"><?php echo $num++; ?></div>
            <div class="info-title">Место</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $u->getLogin($key['id'], false); ?></div>
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
        </a>
        <div class="clearfix"></div>
      </div>
      <?php
    }
    echo $pg->render();
    break;
  case 'online':
    $count = $db->getCount('SELECT count(id) FROM users WHERE updDate > ?', [time() - 900]);
    $title = 'Онлайн (' . $count . ' чел.)';
    require '../../main/head.php';
    if ($count > 0) {
      $pg = new Game\Paginations(10, 'page');
      $pg->setTotal($count);
      $get = $db->getAll('SELECT id FROM users WHERE updDate > ? ' . $pg->getLimit('`updDate`'), [time() - 900]);
      foreach ($get as $key) {
        $player = $u->getInfo($key['id']);
      ?>
        <div class="block <?php echo ($user['id'] == $key['id'] ? 'my-place' : null); ?>">
          <a href="/id<?php echo $key['id'];?>" class="flex-container center">
            <div class="flex-link">
              <div class="info-about"><?php echo $u->getLogin($key['id'], false); ?></div>
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
              <div class="info-about"><img width="5px" src="/files/icons/online.png"></div>
              <div class="info-title"><?php echo $m->oncounter(date('Y-m-j H:i:s', $player['updDate'])); ?></div>
            </div>
          </a>
          <div class="clearfix"></div>
        </div>
<?php
      }
      echo $pg->render();
    } else {
      $m->pda(['Сейчас никого нет в сети.']);
    }
    break;
}
require '../../main/foot.php';

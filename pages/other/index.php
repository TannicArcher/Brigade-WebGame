<?php
require '../../main/main.php';
$u->forUser($user);
$id = abs((int) $_GET['id']);
$list = $npc->get($id);
if (!$list) header('location: /');
$canUse = $npc->canUse($list['id'], $user['id']);
$title = "{$listNPC[$list['method']]} \"{$list['name']}\"";
require '../../main/head.php';
if (!$canUse) {
  $m->pda(['Вы не можете взаимодействовать с этим NPC']);
} else {
  switch ($list['method']) {

      /**
     *
     * КРАФТ
     *
     */

    case 'craft':
      switch ($method) {
        default:
?>
          <div class="flex-link">
            <div class="flexpda main">
              <div class="">
                <img src="/files/npc/<?php echo $id; ?>.png" width="128px" />
              </div>
              <div class="flexpda-content" style="padding-left: 5px;">
                <div class="dialog-he">
                  <span class="access-3"><?php echo $list['name']; ?></span><br />
                  — <?php echo $list['about']; ?>
                </div>
                <div class="flex-container">
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/list" class="href">— Покажи, что умеешь.</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <a href="/map/" class="href mv-5">Вернуться назад</a>
        <?php
          break;

        case 'info':
          $cid = abs((int) $_GET['cid']);
          $craft = $db->get('SELECT * FROM craft WHERE id = ?', [$cid]);
          if (!$craft) header('location: /npc/' . $id);

          if ($craft['obj_type'] == 'weapon') $list = $wpn->get($craft['obj_id']);
          else $list = $obj->get($craft['obj_id']);

          $need = $db->getAll('SELECT * FROM craft_item WHERE id_craft = ?', [$cid]);
          foreach ($need as $key) {
            $n = $key['amount'];
            $th = $obj->getCountObject($user['id'], $key['id_object']);
            $c[$key['id_object']] = [
              'id' => $key['id_object'],
              'need' => $n,
              'theres' => $th,
              'good' => ($n <= $th ? 1 : 0)
            ];
          }

          if ($craft['obj_type'] == 'weapon') {
            $info = $wpn->get($craft['obj_id']);
            $link = $wpn->link($craft['obj_id'], true);
            $quality = [
              'trash' => [
                'rub' => 3
              ],
              'normal' => [
                'rub' => 5
              ],
              'rare' => [
                'rub' => 10
              ],
              'heroic' => [
                'rub' => 50
              ],
              'souvenir' => [
                'rub' => 120
              ]
            ];
          } else {
            $info = $obj->get($craft['obj_id']);
            $link = $obj->link($craft['obj_id']);
          }
        ?>
          <div class="flex-container">
            <div class="flex-link">
              <div class="flexpda main top">
                <div class="flexpda-image">
                  <img class="top-img" src="/files/<?php echo ($craft['obj_type'] == 'weapon' ? "items/{$list['slot']}/{$list['id']}.png" : "{$list['id']}.png"); ?>" width="64px" />
                </div>
                <div class="flexpda-content" style="padding-left: 10px;">
                  <a class="href" href="/wiki/<?php echo ($craft['obj_type'] == 'weapon' ? 'equipments' : 'objects'); ?>/<?php echo $list['id']; ?>"><?php echo $list['name']; ?></a>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (isset($c)) {
          ?>
            <div class="block" style="margin: 0 5px;">
              <div class="block-item">
                <div class="block-item-header-wrap">
                  <div class="block-item-header">Требуется для изготовления</div>
                  <div class="block-item-text">
                    <div class="flex-container">
                      <?php
                      foreach ($c as $list) {
                      ?>
                        <div class="flex-link">
                          <a class="href" href="<?php echo $obj->link($list['id'], true); ?>"><?php echo $obj->get($list['id'])['name']; ?> <span class="count"><?php echo $list['need']; ?> ед.</span></a>
                          <span class="small access-3">в наличии <?php echo $list['theres']; ?> ед.</span>
                        </div>
                      <?php
                      }
                      if ($craft['obj_type'] == 'weapon') {
                      ?>
                        <div class="flex-link">
                          <a class="href">Рубли <span class="count"><?php echo $quality[$info['quality']]['rub']; ?></span></a>
                        </div>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
          }
          $result = empty(array_filter($c, function ($v) {
            return is_array($v) && isset($v['good']) && $v['good'] !== 1;
          })) ? true : false;
          if ($result) {
            if (isset($_GET['make']) and isset($_GET['ok'])) {
              if ($craft['obj_type'] == 'weapon' and $user['rubles'] < $quality[$info['quality']]['rub']) {
            ?>
                <div class="flex-link m-5">
                  <div class="flexpda main">
                    <div class="">
                      <img src="/files/npc/<?php echo $id; ?>.png" width="128px" />
                    </div>
                    <div class="flexpda-content" style="padding-left: 5px;">
                      <div class="dialog-he">
                        <div class="small access-1">— Недостаточно рублей</div>
                        Не трать мое время, Сталкер.<br />
                        Ты не можешь себе этого позволить.
                      </div>
                    </div>
                  </div>
                </div>
              <?php
              } else {
                if ($craft['obj_type'] == 'weapon') {
                  $db->query('UPDATE users SET rubles = rubles - ? WHERE id = ?', [$quality[$info['quality']]['rub'], $user['id']]); // Берем бабло
                  $wpn->give($craft['obj_id'], $user['id']); // Даем экипировку

                } else $obj->give($craft['obj_id'], $user['id'], 1); // Даем предмет
                // Забираем предметы требуемые для крафта.
                foreach ($c as $takeObj) {
                  $obj->take($takeObj['id'], $user['id'], $takeObj['need']);
                }
                // Показываем игроку информацию
              ?>
                <div class="flex-link m-5">
                  <div class="flexpda main">
                    <div>
                      <img src="/files/npc/<?php echo $id; ?>.png" width="128px" />
                    </div>
                    <div class="flexpda-content" style="padding-left: 5px;">
                      <div class="dialog-he">
                        <div class="small access-3">— <?php echo $info['name']; ?> у вас в инвентаре.</div>
                        Держи, теперь это твое.<br />
                        Еще что надо?
                      </div>
                      <div class="flex-container">
                        <div class="flex-link">
                          <a href="/npc/<?php echo $id; ?>/craft/<?php echo $craft['id']; ?>?make" class="href">— Повтори этот заказ.</a>
                        </div>
                        <div class="flex-link">
                          <a href="/npc/<?php echo $id; ?>/list" class="href">— Сделай другой заказ.</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php
              }
            } elseif (isset($_GET['make'])) {
              ?>
              <div class="question" style="margin: 5px">
                <div class="question-answer center">
                  Вы точно желаете изготовить "<?php echo $info['name']; ?>"?
                </div>
                <div class="question-option">
                  <a href="?make&ok" class="href" style="margin-bottom: 1px;">Да</a>
                  <a href="?" class="href">Нет</a>
                </div>
              </div>
          <?php
            } else {
              echo '<a href="?make" class="href" style="margin: 5px">Изготовить предмет</a>';
            }
          } else {
            echo $m->pda(['У тебя нет нужных материалов для изготовления предмета.']);
          }
          ?>
          <a href="/npc/<?php echo $id; ?>/list" class="href" style="margin: 5px">Вернуться назад</a>
          <?php
          break;

        case 'list':
          $cnt = $db->getCount('SELECT count(id) FROM craft');
          if ($cnt > 0) {
            $pg = new Paginations(10, 'page');
            $pg->setTotal($cnt);
            $get = $db->getAll('SELECT * FROM craft ' . $pg->getLimit('id'), []);
            echo '<div class="flex-container">';
            foreach ($get as $key) {
              if ($key['obj_type'] == 'weapon') $list = $wpn->get($key['obj_id']);
              else $list = $obj->get($key['obj_id']);
          ?>
              <div class="flex-link">
                <div class="flexpda main top">
                  <div class="flexpda-image">
                    <img class="top-img" src="/files/<?php echo ($key['obj_type'] == 'weapon' ? "items/{$list['slot']}/{$list['id']}.png" : "{$list['id']}.png"); ?>" width="64px" />
                  </div>
                  <div class="flexpda-content" style="padding-left: 5px;">
                    <span class="access-3"><?php echo $list['name']; ?></span>
                    <hr />
                    <div class="flex-container">
                      <div class="flex-item">
                        <div class="info-about"><?php echo ($key['obj_type'] == 'weapon' ? 'экипировка' : 'предмет') ?></div>
                        <div class="info-title">вид работы</div>
                      </div>
                      <div class="flex-link">
                        <a class="href" href="/npc/<?php echo $id; ?>/craft/<?php echo $key['id'] ?>">Изготовить</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          <?php
            }
            echo '</div>';
            if ($cnt > 10) echo $pg->render();
          } else $m->pda(['Я ничего не могу сейчас сделать']);
          ?>
          <a href="/npc/<?php echo $id; ?>" class="href" style="margin: 5px">Вернуться назад</a>
          <?php
          break;
      }
      break;

      /**
       *
       * ПРОДАВЕЦ ЭКИПИРОВКИ
       *
       */

    case 'equipment':
      switch ($method) {
        case 'view':
          $type = trim($_GET['type']);
          if (!array_key_exists($type, $slot)) header('Location: /npc/' . $list['id']);

          $cnt = $db->getCount('SELECT count(id) FROM weapons WHERE slot = ? and how = ? and lvl <= ?', [$type, 'shop', $user['level']]);
          if ($cnt > 0) {
            $pg = new Paginations(10, 'page');
            $pg->setTotal($cnt);
            $get = $db->getAll('SELECT id FROM weapons WHERE slot = ? and how = ? and lvl <= ? ' . $pg->getLimit('lvl'), [$type, 'shop', $user['level']]);

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
                      <div class="flex-item">
                        <div class="info-about">
                          <?php echo ceil($listed['amount'] * $list['ratio']); ?> <img src='/files/icons/<?php echo ($listed['price'] == 'rubles' ? 'rubles' : 'bolts'); ?>.png' width='12px' />
                        </div>
                        <div class="info-title">Цена</div>
                      </div>
                      <div class="flex-item">
                        <div class="info-about"><?php echo $quality[$listed['quality']]; ?></div>
                        <div class="info-title">Качество</div>
                      </div>
                      <div class="flex-link">
                        <a class="href" href="/npc/<?php echo $id; ?>/equipment/<?php echo $type; ?>/<?php echo $listed['id']; ?>">Подробнее</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          <?php
            }
            echo '</div>';
            echo $pg->render();
          } else $m->pda(['Нет экипировки для продажи']);
          echo "<a class='href' href='/npc/{$id}'> Вернуться к \"{$npc->link($id, false)}\"</a>";
          break;
        case 'info':
          $type = trim($_GET['type']);
          $wid = abs((int) $_GET['wid']);
          if (!array_key_exists($type, $slot)) header('Location: /npc/' . $list['id']);
          $equip = $wpn->get($wid);
          if (!$equip or $equip['how'] != 'shop') header('Location: /npc/' . $list['id']);
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
                <div class="flex-item">
                  <div class="info-about">
                    <?php echo ceil($equip['amount'] * $list['ratio']); ?> <img src='/files/icons/<?php echo ($equip['price'] == 'rubles' ? 'rubles' : 'bolts'); ?>.png' width='12px' />
                  </div>
                  <div class="info-title">Цена</div>
                </div>
                <div class="flex-item">
                  <div class="info-about"><?php echo $quality[$equip['quality']]; ?></div>
                  <div class="info-title">Качество</div>
                </div>
                <div class="flex-item">
                  <div class="info-about"><?php echo $slot[$equip['slot']]['name']; ?></div>
                  <div class="info-title">Вид</div>
                </div>
                <div class="flex-link">
                  <?php
                  if (isset($_GET['buy']) and isset($_GET['run'])) {
                    $buy = $npc->buyEquip($equip['id'], $user['id'], $equip['price'], ceil($equip['amount'] * $list['ratio']));
                    if ($buy == 200) header('location: /inventory/');
                    else $m->pda($buy);
                  } elseif (isset($_GET['buy'])) {
                  ?>
                    <div class="question">
                      <div class="question-answer center">
                        Купить <span class="quality-<?php echo $equip['quality']; ?>">"<?php echo $equip['name']; ?>"</span></strong> за <?php echo ceil($equip['amount'] * $list['ratio']); ?> <img src='/files/icons/<?php echo ($equip['price'] == 'rubles' ? 'rubles' : 'bolts'); ?>.png' width='12px' />?<br />
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
          <a href="/npc/<?php echo $id; ?>/equipment/<?php echo $type; ?>" class="href" style="margin-top: 5px;">Вернуться назад к категории</a>
        <?php
          break;
        default:
        ?>
          <div class="flex-link">
            <div class="flexpda">
              <div>
                <img src="/files/npc/<?php echo $id; ?>.png" width="128px" />
              </div>
              <div class="flexpda-content" style="padding-left: 5px;">
                <div class="dialog-he">
                  <span class="access-3"><?php echo $list['name']; ?></span><br />
                  —  <?php echo $list['about']; ?>
                </div>
                <div class="flex-container center">
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/head" class="href">Голова</a>
                  </div>
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/body" class="href">Тело</a>
                  </div>
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/hand" class="href">Руки</a>
                  </div>
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/knife" class="href">Ножи</a>
                  </div>
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/pistol" class="href">Пистолеты</a>
                  </div>
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/gun" class="href">Автоматы</a>
                  </div>
                  <div class="flex-link">
                    <a href="/npc/<?php echo $id; ?>/equipment/boot" class="href">Ноги</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <a href="/map/" class="href m-5">Вернуться назад</a>
<?php
          break;
      }
      break;
  }
}
require '../../main/foot.php';

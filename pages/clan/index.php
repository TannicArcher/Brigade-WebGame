<?php
require '../../main/main.php';
$title = 'Группировки';
require '../../main/head.php';
$cl = $clan->have($user['id']);
if ($cl && $cl['id_lider'] != $user['id']) {
  if (isset($_GET['leave']) && isset($_GET['yes'])){
      $db->query('DELETE FROM groups_users WHERE id_user = ? and id_group = ?', [$user['id'], $cl['id_group']]);
      $log = "самовольно покидает ОПГ";
      $db->query('INSERT INTO groups_logs (id_user, id_group, text, time, types) VALUES (?, ?, ?, ?, ?)', [$user['id'], $cl['id_group'], $log, time(), 'members']);
      $_SESSION['notify'][] = 'Вы успешно покинули ОПГ';
      $m->to('/clan');
  } elseif (isset($_GET['leave'])){
    ?>
    <div class="question mv-5">
        <div class="question-answer center access-2">
            Вы действительно хотите покинуть ОПГ?<br>
        </div>
        <div class="question-option">
            <a href="/clan?leave&yes" class="href" style="margin-bottom: 1px;">Да</a>
            <a href="/caln" class="href">Нет</a>
        </div>
    </div>
    <?php
  }
}
?>
<div class="flex-container">
  <?php if (!$cl) : ?>
    <div class="flex-link">
      <a href="/clan/create" class="href">Создать группировку</a>
    </div>
  <?php else : ?>
    <div class="flex-link w-100">
      <div class="block">
        <div class="center mh-5">
          <strong class="access-3"><?php echo $cl['name']; ?></strong><br />
          <span class="small"><?php echo $rankGrp[$cl['rank']]; ?></span>
        </div>
        <hr />
        <div class="flex-container center">
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['count']; ?></div>
            <div class="info-title">Участники</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['level']; ?></div>
            <div class="info-title">Уровень</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['exp']; ?></div>
            <div class="info-title">Влияние</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['bolts']; ?></div>
            <div class="info-title">Черный нал.</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['rubles']; ?></div>
            <div class="info-title">Рублей</div>
          </div>
        </div>
        <hr />
        <div class="flex-container center">
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['donate_bolts']; ?></div>
            <div class="info-title">Вложено черного нала</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['donate_rubles']; ?></div>
            <div class="info-title">Вложено рублей</div>
          </div>
          <div class="flex-link">
            <div class="info-about"><?php echo $cl['exp_all']; ?></div>
            <div class="info-title">Вложено влияния</div>
          </div>
        </div>
        <div class="flex-container">
          <div class="flex-link">
            <a href="/clan/<?php echo $cl['id_group']; ?>" class="href">Перейти</a>
          </div>
          <div class="flex-link">
            <a href="/clan/<?php echo $cl['id_group']; ?>/members" class="href">Участники <span class='count'><?php echo $cl['count']; ?></span>
              <div class='clearfix'></div>
            </a>
          </div>
          <div class="flex-link">
            <a href="/clan/<?php echo $cl['id_group']; ?>/bank" class="href">Общак</a>
          </div>
          <?php if ($cl['radio'] == 1):?>
              <div class="flex-link">
                  <a href="/clan/<?php echo $cl['id']; ?>/chat" class="href">
                      Чат ОПГ
                      <span class='count'><?php echo $db->getCount('SELECT COUNT(`id`) FROM `groups_chat` WHERE `id_group` = ?', [$cl['id']]);?></span>
                      <div class='clearfix'></div>
                  </a>
              </div>
          <?php endif; ?>
          <?php if ($cl['id_lider'] == $user['id']) : ?>
            <div class="flex-link">
              <a href="/clan/<?php echo $cl['id_group']; ?>/settings" class="href">Управление</a>
            </div>
          <?php elseif($cl['id_lider'] != $user['id']): ?>
            <div class="flex-link">
              <a href="/clan?leave" class="href">Покинуть ОПГ</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="flex-link">
    <a href="/clan/rating" class="href">Все группировки</a>
  </div>
</div>
<?php
require '../../main/foot.php';

<?php
require '../../main/main.php';
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) die();
$count = $db->getCount('SELECT count(id) FROM chat', []);
if ($count > 0)
{
  $pg = new Game\Paginations (10, 'page');
  $pg->setTotal($count);
  $get = $db->getAll('SELECT * FROM chat '.$pg->getLimit('id'), []);
  foreach ($get as $key)
  {
    $grouped = $db->get('SELECT `groups`.`tag` FROM `groups` JOIN `groups_users` ON (`groups`.`id` = `groups_users`.`id_group`) WHERE `groups_users`.`id_user` = ? and `groups_users`.`accept` = ? and `groups`.`tag` IS NOT NULL', [$key['uid'], 1]);
    ?>
    <div class="chat-block">
        <div class="chat-block-message">
            <div class="chat-block-message__text">
                <?php echo $emoji::Decode($m->message($key['message']));?>
            </div>
        </div>
        <div class="chat-block-user">
            <div class="chat-block-user-name">
                <?php if ($grouped):?>
                <div class="chat-block-user-tag">
                    <?php echo $grouped['tag'];?>
                </div>
                <?php endif;?>
                <div class="chat-block-user-name__login">
                    <?php echo $u->getLogin($key['uid'], true);?>
                </div>
                <div class="chat-block-user-name__bbcode">
                    <a title="Упомянуть" style="display: inline-block; cursor: pointer;" onclick="bb('@id<?php echo $key['uid'];?>, ');">
                        <img src="/files/icons/chat.png" width="16px" />
                    </a>
                </div>
                <div class="chat-block-message__time">
                    <?php echo date("d.m.Y в H:i:s", $key['timeAdd']);?>
                </div>
            </div>
        </div>
    </div>
    <?php
  }
  echo $pg->render();
}
else
{
  $m->pda(['Сообщений нет.']);
}
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
    ?>
    <div class="block line-height">
      <?php echo $u->getLogin($key['uid'], true);?> <span class="small">/ <a title="Упомянуть" style="display: inline-block; cursor: pointer;" onclick="bb('@id<?php echo $key['uid'];?>, ');"><img src="/files/icons/chat.png" width="16px" /></a> / <a title="Профиль" style="display: inline-block;" href="/id<?php echo $key['uid'];?>"><img src="/files/icons/user.png" width="16px" /></a></span><br/>
      <div style="overflow-wrap: break-word;hyphens: auto;word-break: break-all;">
        <?php echo $emoji::Decode($m->message($key['message']));?>
      </div>
      <div class="small">
        <?php echo date("d.m.Y в H:i:s", $key['timeAdd']);?>
        <div class="clearfix"></div>
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
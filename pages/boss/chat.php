<?php
require '../../main/main.php';
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) die();
if (!is_numeric($_POST['idChat'])) die();
$count = $db->getCount('SELECT count(id) FROM `boss_chat` WHERE `id_fight` = ?', [(int)$_POST['idChat']]);
if ($count > 0)
{
  $pg = new Game\Paginations (10, $_POST['page']);
  $pg->setTotal($count);
  $get = $db->getAll('SELECT * FROM `boss_chat` WHERE `id_fight` = ? '.$pg->getLimit('id'), [(int)$_POST['idChat']]);
  foreach ($get as $key)
  {
    ?>
    <div class="block line-height mv-5">
        <?php echo $u->getLogin($key['id_user'], true);?> <span class="small pull-right"><?php echo date("d.m.Y в H:i:s", $key['created_at']);?></span><br/>
        <div class="clearfix"></div>
        <div><?php echo $emoji::Decode($m->message($key['message']));?></div>
    </div>
    <?php
  }
  echo $pg->render();
}
else
{
  $m->pda(['Сообщений нет.']);
}
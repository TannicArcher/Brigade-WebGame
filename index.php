<?php
include 'main/main.php';

if (!isset($user))
{
  if (isset($_GET['ref']) and is_numeric($_GET['ref'])) {
    $_SESSION['ref'] = abs(intval($_GET['ref']));
    $m->to('/');
  }
  if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) && !isset($_SESSION['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != 'https://brigada.mobi/'){
    $_SESSION['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
    $m->to('/');
  }
  include 'landing.php';
  exit();
}

$include['css'] = 'city.style.css';
include 'main/head.php';

if($user['id'] == 182)
{
  ?>
   <div class="city">
       
</div>
  <?
}else{
?>
<h1>Центр города</h1>
<a href="/buffet/" class="href mv-5">
  Столовая «у тёти Зины»
  <div class="clearfix"></div>
</a>
<a href="/washhouse" class="href mv-5">
  «Прачечная» <span class="count">отмывание денег</span>
  <div class="clearfix"></div>
</a>
<a href="/casino" class="href mv-5">
  Казино «Жар-птица»
</a>
<a href="/market" class="href mv-5">
  Рынок «Черкизон»
</a>
<a href="/blackmarket" class="href mv-5">
  «Черный» рынок <span class="count">требуется 500 репутации</span>
  <div class="clearfix"></div>
</a>
<a href="/forsale" class="href mv-5">
  Фарцовщик <span class="count">поиск редких вещей</span>
</a>
<?php
}
include 'main/foot.php';

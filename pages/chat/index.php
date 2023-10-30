<?php
require '../../main/main.php';
$title = 'Рация';
require '../../main/head.php';
    if (isset($_GET['autoload'])){
        if ($user['autoload'] == 0) $db->query('UPDATE `users` SET `autoload` = ? WHERE `id` = ?', [1, $user['id']]);
        else $db->query('UPDATE `users` SET `autoload` = ? WHERE `id` = ?', [0, $user['id']]);
        $m->to('/chat/');
    }
?>
    <div class="block center access-2">
        За каждое сообщение в чате Вы получаете +10 <img src="/files/icons/bolts.png" alt="">
    </div>
    <form method="post" id="chat">
        <textarea class="w-100" id="text" name="text" placeholder="Введите сообщение..."></textarea><br/>
        <div id="results"></div>
        <div class="flex-container">
            <div class="flex-link">
                <input class="w-100" type="submit" id="send" value="Отправить"/>
            </div>
        </div>
    </form>
    <a href="?autoload" class="href"><?php echo ($user['autoload'] == 1 ? 'Выключить автообновление':'Включить автообновление');?></a>
    <script src="/assets/chat.js"></script>
    <div id="messages">
    </div>
    <script>
        loadChat()
        <?php if ($user['autoload'] == 1):?>
        setInterval(() => {
            loadChat()
        }, 5000)
        <?php endif; ?>
    </script>
<?php
require '../../main/foot.php';

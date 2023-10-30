<?php
require '../../main/main.php';
$title = 'Рация';
$include['css'] = 'chat.style.css';
$include['js'] = '/assets/chat.include.js';
require '../../main/head.php';
?>
    <form method="post" id="newMessage">
        <textarea class="w-100" id="text" name="text" placeholder="Введите сообщение..."></textarea><br/>
        <div id="results"></div>
        <div class="flex-container">
            <div class="flex-link">
                <input class="w-100" type="submit" id="send" value="Отправить"/>
            </div>
        </div>
    </form>
    <div id="messages">
    </div>
    <div class="chat-container">
        <script>
            loadChat()
            const form = document.querySelector('#newMessage')
            form.addEventListener('submit', sendForm)
            setInterval(() => {
                loadChat()
            }, 5000)
        </script>
    </div>
<?php
require '../../main/foot.php';

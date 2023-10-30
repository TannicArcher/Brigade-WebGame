<?php
require '../../main/main.php';
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) die();
if ($user['access'] < 4){
    $post = htmlspecialchars($_POST['text']);
} else $post = $_POST['text'];

if (isset($post)) {
    if (empty(trim($post))) {
        $result = [
            'type' => 'error',
            'message' => 'Введите текст сообщения',
        ];
    } elseif (mb_strlen(trim($post)) < 3) {
        $result = [
            'type' => 'error',
            'message' => 'Сообщение должно быть не меньше 3 символов',
        ];
    } elseif ($user['level'] < 2){
        $result = [
            'type' => 'error',
            'message' => 'Сообщения в чате разрешено писать только с 2 уровня',
        ];
    } else {
        $result = [
            'type' => 'success',
            'message' => 'Сообщение успешно отправлено',
        ];
        $db->query('INSERT INTO chat (uid, message, timeAdd) VALUES (?, ?, ?)', [$user['id'], $emoji::Encode($post), time()]);
        $u->giveBolts($user['id'], 10);
    }

    echo json_encode($result);
}
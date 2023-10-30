<?php
require '../../main/main.php';
$title = 'Сохранение персонажа';
if ($user['save']) die(header('Location: /'));
require '../../main/head.php';
    if (isset($_POST['next'])) {
        if(empty(trim($_POST['password'])) or empty(trim($_POST['repassword']))) $error[] = 'Оба поля обязательны к заполнению';
        elseif ($_POST['password'] != $_POST['repassword']) $error[] = 'Пароли не совпадают';

        if (isset($error)) {
            echo $m->pda($error);
        } else {
            $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
            $db->query('update users set password = ?, rubles = rubles + ?, save = ? where id = ?', [$password, 30, 1, $user['id']]);
            setcookie('password', $password, time() + 60 * 60 * 24 * 30, '/');
            die(header('Location: /'));
        }
    }
?>
    <form method="post">
        Введите пароль:
        <input type="password" name="password" placeholder="Введите пароль..."/>
        Введите пароль еще раз:
        <input type="password" name="repassword" placeholder="Введите пароль..."/>
        <input type="submit" class="w-100" name="next" value="Сохранить персонажа">
    </form>
<?php
require '../../main/foot.php';
?>
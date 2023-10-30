<?php
require '../../main/main.php';

switch ($method)
{
    case 'in':
        getHeader('Страница авторизации');
        $u->forGuest($user);
        ?>
        <div id="pjax-container">
            <form method="POST">
                <input type="submit" name="okay" value="Okey">
            </form>
        </div>
        <?php
    break;
}
getFooter();
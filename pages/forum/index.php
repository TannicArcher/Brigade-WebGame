<?php
require '../../main/main.php';
switch ($method) {
    default:
        $title = 'Форум';
        require '../../main/head.php';
        echo '<h1>Разделы форума</h1>';
        $count = $db->getCount('SELECT count(`id`) FROM `forum_category` WHERE `access` <= ? and `parent` IS NULL', [$user['access']]);
        if ($count > 0) {
            $get = $db->getAll('SELECT * FROM `forum_category` WHERE `access` <= ? and `parent` IS NULL', [$user['access']]);
            foreach ($get as $category) {
                ?>
                <div class="main mv-5">
                    <a href="/forum/category/<?php echo $category['id']; ?>"
                       class="href">— <?php echo $category['name']; ?></a>
                    <div class="mv-5">
                        <?php echo $category['about']; ?>
                        <?php if ($category['access'] > 0) echo '<div class="access-3">— Данный раздел доступен только администрации</div>'; ?>
                        <?php if ($category['onlyAdmin'] == 1) echo '<div class="access-2">— Темы в данном разделе может просмотреть только разработчик и автор</div>'; ?>
                        <?php if ($category['onlySupport'] == 1) echo '<div class="access-1">— Темы в данном разделе может просмотреть только администрация и автор</div>'; ?>
                    </div>

                </div>
                <?php
            }
        } else {
            $m->pda(['Разделы еще не созданы']);
        }
        ?>
        <?php if ($user['access'] == 4) echo '<a href="/forum/create" class="href">Создать категорию</a>'; ?>
        <?php
        break;
    // Категории
    case 'createCategory':
        $title = 'Форум :: Создание категории';
        require '../../main/head.php';
        if ($user['access'] < 4) header('Location: /forum/');

        if (isset($_POST['send'])) {
            if (empty($_POST['name'])) $error[] = 'Название категории не может быть пустым.';

            if (!empty($error)) $m->pda($error);
            else {
                if (empty($_POST['parent'])) $_POST['parent'] = null;
                if (empty($_POST['access'])) $_POST['access'] = 0;
                if (empty($_POST['admin'])) $_POST['admin'] = 0;
                if (empty($_POST['support'])) $_POST['support'] = 0;
                if (empty($_POST['createAdmin'])) $_POST['createAdmin'] = 0;

                $db->query('INSERT INTO `forum_category` 
                            (`name`, `about` , `parent`, `access`, `onlyAdmin`, `onlySupport`, `createAdmin`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [$_POST['name'], $_POST['about'], $_POST['parent'], $_POST['access'], $_POST['admin'], $_POST['support'], $_POST['createAdmin']]);
                $m->pda(['Категория успешно создана с ID: ' . $db->lastInsertId()]);
            }
        }
        $count = $db->getCount('SELECT count(`id`) FROM `forum_category`', []);
        if ($count > 0) $get = $db->getAll('SELECT `id`, `name` FROM `forum_category`', []);
        ?>
        <form method="post">
            <div class="flex-container">
                <div class="flex-link">
                    <h1>Основное</h1><br/>
                    Название
                    <input type="text" minlength="3" maxlength="255" placeholder="Введите название категории"
                           name="name" required/><br/>
                    Описание категории
                    <textarea name="about" rows="2"></textarea><br/>
                    <?php if ($count > 0): ?>
                        Подкатегория
                        <select name="parent">
                            <option value="">- / -</option>
                            <?php foreach ($get as $category): ?>
                                <option value="<? echo $category['id']; ?>"><? echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="flex-link">
                    <h1>Дополнительные параметры</h1><br/>
                    <input type="checkbox" value="1" name="access" id="access">
                    <label for="access">Для админ.состава</label>
                    <hr/>
                    <input type="checkbox" value="1" name="admin" id="admin">
                    <label for="admin">Смотреть только разработчику</label>
                    <hr/>
                    <input type="checkbox" value="1" name="support" id="support">
                    <label for="support">Смотреть только поддержке</label>
                    <hr/>
                    <input type="checkbox" value="1" name="createAdmin" id="createAdmin">
                    <label for="createAdmin">Создает тему только админ</label>
                </div>
            </div>
            <input class="w-100" type="submit" name="send" value="Создать">
        </form>
        <?php
        break;
    case 'viewCategory':
        $id = abs((int)$_GET['id']);
        $cat = $db->get('SELECT * FROM `forum_category` WHERE `id` = ?', [$id]);
        if (!$cat) header('location: /');
        elseif ($cat['access'] > $user['access']) header('location: /');
        $title = "Форум :: раздел «{$cat['name']}»";
        require '../../main/head.php';

        $countCategory = $db->getCount('SELECT count(`id`) FROM `forum_category` WHERE `parent` = ?', [$id]);
        if ($countCategory > 0) {
            $getCategory = $db->getAll('SELECT * FROM `forum_category` WHERE `parent` = ?', [$id]);
            echo '<h1>Подразделы</h1>';
            foreach ($getCategory as $category) {
                ?>
                <div class="main mv-5">
                    <a href="/forum/category/<?php echo $category['id']; ?>"
                       class="href">— <?php echo $category['name']; ?></a>
                    <div class="mv-5">
                        <?php echo $category['about']; ?>
                        <?php if ($category['access'] > 0) echo '<div class="access-3">— Данный раздел доступен только администрации</div>'; ?>
                        <?php if ($category['onlyAdmin'] == 1) echo '<div class="access-2">— Темы в данном разделе может просмотреть только разработчик и автор</div>'; ?>
                        <?php if ($category['onlySupport'] == 1) echo '<div class="access-1">— Темы в данном разделе может просмотреть только администрация и автор</div>'; ?>
                    </div>

                </div>
                <?php
            }
        }
        echo '<h1>Обсуждения</h1>';
        $countTopics = $db->getCount('SELECT count(`id`) FROM `forum_topics` WHERE `id_category` = ?', [$id]);
        if ($countTopics > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($countTopics);
            $getTopics = $db->getAll('SELECT `forum_topics`.*, count(`forum_messages`.`id`) as cnt
                                    FROM `forum_topics` 
                                    LEFT OUTER JOIN `forum_messages` 
                                    ON `forum_topics`.`id` = `forum_messages`.`id_topic` 
                                    WHERE `forum_topics`.`id_category` = ? 
                                    GROUP BY forum_topics.id '
                . $pg->getLimitMany('`attach` DESC, `lastUpdate` DESC'), [$id]);
            foreach ($getTopics as $topic) {
                ?>
                <div class="main mv-5">
                    <div class="flex-container">
                        <div class="flex-link" style="flex-grow: 10">
                            <a href="/forum/topic/<?php echo $topic['id']; ?>" class="href">
                                <?php echo $topic['name']; ?>
                                <?php echo($topic['attach'] == 1 ? '<div class="count">закреплено</div>' : null); ?>
                                <?php echo($topic['closed'] == 1 ? '<div class="count">закрыто</div>' : null); ?>
                            </a>
                        </div>
                        <div class="flex-link center">
                            <a href="/forum/topic/<?php echo $topic['id']; ?>?page=<?php echo $m->getPage($topic['cnt'], false); ?>"
                               class="href">
                                »
                            </a>
                        </div>
                        <div class="flex-link center">
                            <div class="main">Автор: <?php echo $u->getLogin($topic['author'], true); ?> /
                                Ответов: <?php echo $topic['cnt']; ?></div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo $pg->render();
        } else $m->pda(['Темы еще не созданы. Стань первым!']);
        if (($cat['createAdmin'] == 1 && $user['access'] > 2) || $cat['createAdmin'] == 0) echo '<a href="/forum/category/' . $id . '/new" class="href mv-5">Создать обсуждение</a>';
        echo '<a href="' . (empty($cat['parent']) ? '/forum/' : '/forum/category/' . $cat['parent']) . '" class="href">Вернуться назад</a>';
        break;

    case 'newTopic':
        $id = abs((int)$_GET['id']);
        $cat = $db->get('SELECT * FROM `forum_category` WHERE `id` = ?', [$id]);
        if (!$cat) header('location: /');
        elseif ($cat['access'] > $user['access']) header('location: /');
        elseif ($cat['createAdmin'] == 1 && $user['access'] < 3) header('location: /');

        $title = "{$cat['name']} :: Новое обсуждение";
        require '../../main/head.php';
        if (isset($_POST['send'])) {
            if (empty(trim($_POST['name']))) $error[] = 'Название обсуждения не может быть пустым.';
            if (empty(trim($_POST['message']))) $error[] = 'Текст обсуждения не может быть пустым.';
            if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 112) $error[] = 'Используйте не менее 3 и не более 112 символов в названии.';
            if (strlen($_POST['message']) < 3) $error[] = 'Используйте не менее 3 символов в тексте.';

            if (!empty($error)) {
                $m->pda($error);
            } else {
                if (empty($_POST['attach'])) $_POST['attach'] = 0;
                if (empty($_POST['closed'])) $_POST['closed'] = 0;
                if ($user['access'] < 4){
                    $_POST['name'] = htmlspecialchars($_POST['name']);
                    $_POST['message'] = htmlspecialchars($_POST['message']);
                }
                $db->query('INSERT INTO `forum_topics` (`name`, `id_category`, `message`, `author`, `dateAdd`, `lastUpdate`, `closed`, `attach`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [$_POST['name'], $cat['id'], $_POST['message'], $user['id'], time(), time(), $_POST['closed'], $_POST['attach']]);
                header('Location: /forum/topic/' . $db->lastInsertId());
            }
        }
        ?>
        <form method="post">
            <div class="flex-container">
                <div class="flex-link">
                    Название обсуждения
                    <input type="text" minlength="3" maxlength="255" placeholder="Введите название обсуждения"
                           name="name" required/><br/>
                    Текст обсуждения
                    <textarea name="message" rows="3" minlength="5" required></textarea><br/>
                    <a href="/forum/category/<?php echo $id; ?>" class="href"><?php echo $cat['name']; ?> <span
                                class="count">категория</span>
                        <div class="clearfix"></div>
                    </a>
                </div>
                <?php if ($user['access'] > 2): ?>
                    <div class="flex-link">
                        <h1>Дополнительные параметры</h1><br/>
                        <input type="checkbox" value="1" name="attach" id="attach">
                        <label for="attach">Закрепить обсуждение в разделе</label>
                        <hr/>
                        <input type="checkbox" value="1" name="closed" id="closed">
                        <label for="closed">Закрыть обсуждение после создания</label>
                    </div>
                <?php endif; ?>
            </div>
            <input class="w-100" type="submit" name="send" value="Создать">
        </form>
        <?php
        break;

    case 'viewTopic':
        $id = abs((int)$_GET['id']);
        $topic = $db->get('SELECT `ft`.*, `fc`.`name` as `name_category`, `fc`.`access`, `fc`.`onlyAdmin`, `fc`.`onlySupport`, `fc`.`createAdmin`
        FROM `forum_topics` as `ft` 
        LEFT JOIN `forum_category` as `fc` 
        ON (`ft`.`id_category` = `fc`.`id`) WHERE `ft`.`id` = ?', [$id]);
        if (!$topic) header('location: /');
        if ($topic['access'] > $user['access']) header('location: /forum/category/' . $topic['id_category']);
        if ($topic['onlyAdmin'] == 1 && ($user['id'] != $topic['author'] && $user['access'] < 3)) header('location: /forum/category/' . $topic['id_category']);
        if ($topic['onlySupport'] == 1 && ($user['id'] != $topic['author'] && $user['access'] < 2)) header('location: /forum/category/' . $topic['id_category']);

        $title = "Форум :: тема «{$topic['name']}»";
        require '../../main/head.php';

        // УПРАВЛЕНИЕ
        switch ($control) {
            case 'closed':
                if ($topic['author'] == $user['id'] || $user['access'] > 0) {
                    if ($topic['closed'] == 0) {
                        if (isset($_GET['yes'])) {
                            $db->query('UPDATE `forum_topics` SET `closed` = ? WHERE `id` = ?', [1, $id]);
                            header('Location: ' . $_SERVER['REDIRECT_URL']);
                        } else {
                            ?>
                            <div class="question mv-5">
                                <div class="question-answer center access-2">
                                    Вы действительно хотите закрыть обсуждение?<br/>
                                    <?php echo($user['access'] < 1 ? '<strong class="access-1">ВАЖНО:</strong> Открыть обсуждение заново можно будет только через модераторов.' : null); ?>
                                </div>
                                <div class="question-option">
                                    <a href="/forum/topic/<?php echo $id; ?>?control=closed&yes" class="href"
                                       style="margin-bottom: 1px;">Да</a>
                                    <a href="/forum/topic/<?php echo $id; ?>" class="href">Нет</a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        if ($user['access'] < 1) $m->pda(['Открыть свое обсуждение заново возможно только через модераторов']);
                        else {
                            $db->query('UPDATE `forum_topics` SET `closed` = ? WHERE `id` = ?', [0, $id]);
                            header('Location: ' . $_SERVER['REDIRECT_URL']);
                        }
                    }
                }
                break;

            case 'transfer':
                if ($user['access'] > 0) {
                    if (isset($_REQUEST['transfer'])) {
                        $db->query('UPDATE `forum_topics` SET `id_category` = ? WHERE `id` = ?', [$_POST['category'], $id]);
                        header('Location: ' . $_SERVER['REDIRECT_URL']);
                    } else {
                        $transfer = $db->getAll('SELECT `id`, `name` FROM `forum_category` WHERE `id` != ?', [$topic['id_category']]);
                        ?>
                        <div class="main mv-5">
                            <h1>Перенос обсуждения в другой раздел</h1><br/>

                            <form method="post">
                                <select name="category">
                                    <?php foreach ($transfer as $tc): ?>
                                        <option value="<? echo $tc['id']; ?>"><? echo $tc['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="submit" name="transfer" value="Перенести" class="w-100">
                            </form>
                        </div>
                        <?php
                    }
                }
                break;

            case 'attach':
                if ($user['access'] > 2) {
                    $db->query('UPDATE `forum_topics` SET `attach` = ? WHERE `id` = ?', [($topic['attach'] == 0 ? 1 : 0), $id]);
                    header('Location: ' . $_SERVER['REDIRECT_URL']);
                }
                break;

            case 'clear':
                if ($user['access'] > 2) {
                    if (isset($_GET['yes'])) {
                        $db->query('DELETE FROM `forum_messages` WHERE `id_topic` = ?', [$id]);
                        header('Location: ' . $_SERVER['REDIRECT_URL']);
                    } else {
                        ?>
                        <div class="question mv-5">
                            <div class="question-answer center access-2">
                                Вы действительно хотите очистить обсуждение?
                            </div>
                            <div class="question-option">
                                <a href="/forum/topic/<?php echo $id; ?>?control=clear&yes" class="href"
                                   style="margin-bottom: 1px;">Да</a>
                                <a href="/forum/topic/<?php echo $id; ?>" class="href">Нет</a>
                            </div>
                        </div>
                        <?php
                    }
                }
                break;
            case 'delete':
                if ($user['access'] > 2) {
                    if (isset($_GET['yes'])) {
                        $db->query('DELETE FROM `forum_topics` WHERE `id` = ?', [$id]);
                        header('Location: /forum/category/' . $topic['id_category']);
                    } else {
                        ?>
                        <div class="question mv-5">
                            <div class="question-answer center access-2">
                                Вы действительно хотите удалить обсуждение?
                            </div>
                            <div class="question-option">
                                <a href="/forum/topic/<?php echo $id; ?>?control=delete&yes" class="href"
                                   style="margin-bottom: 1px;">Да</a>
                                <a href="/forum/topic/<?php echo $id; ?>" class="href">Нет</a>
                            </div>
                        </div>
                        <?php
                    }
                }
                break;
        }


        ///////////////////////////////////////////////////////////////////
        if ($topic['author'] == $user['id'] || $user['access'] > 0) {
            ?>
            <div class="main">
                <h1>Управление обсуждением</h1>
                <div class="flex-container left">
                    <div class="flex-link">
                        <?php if ($topic['closed'] == 0): ?>
                            <a href="/forum/topic/<?php echo $id; ?>?control=closed" class="href">Закрыть</a>
                        <?php elseif ($topic['closed'] == 1 && $user['access'] > 0): ?>
                            <a href="/forum/topic/<?php echo $id; ?>?control=closed" class="href">Открыть</a>
                        <?php else: ?>
                            <div class="small access-1">Обсуждение закрыто. Если требуется его открыть, то обратитесь к
                                модераторам.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (($user['id'] == $topic['author'] and $topic['closed']) || $user['access'] > 1):?>
                        <div class="flex-link">
                            <a href="/forum/topic/<?php echo $id; ?>?control=edit" class="href">Редактировать</a>
                        </div>
                    <?php endif; ?>
                    <?php if ($user['access'] > 1): ?>
                        <div class="flex-link">
                            <a href="/forum/topic/<?php echo $id; ?>?control=transfer" class="href">Перенести</a>
                        </div>
                    <?php endif; ?>
                    <?php if ($user['access'] > 2): ?>
                        <div class="flex-link">
                            <a href="/forum/topic/<?php echo $id; ?>?control=attach" class="href">
                                <?php echo($topic['attach'] == 0 ? 'Закрепить' : 'Открепить'); ?>
                            </a>
                        </div>
                        <div class="flex-link">
                            <a href="/forum/topic/<?php echo $id; ?>?control=clear" class="href">Очистить</a>
                        </div>
                        <div class="flex-link">
                            <a href="/forum/topic/<?php echo $id; ?>?control=delete" class="href">Удалить</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="flexpda main top">
            <div class="flexpda-image center">
                <a href="/id<?php echo $topic['author']; ?>">
                    <img src="/files/other/battle.png" class="top-img main" alt="">
                </a>
            </div>
            <div class="flexpda-content" style="padding-left: 10px;">
                <div class="forum-id"><?php echo $u->getLogin($topic['author'], true); ?></div>
                <?php if (isset($_GET['control']) and $_GET['control'] == 'edit' and ($user['access'] > 1 || ($topic['author'] == $user['id'] and !$topic['closed']))): ?>
                    <?php
                    if (isset($_POST['topicEdit'])){
                        if (empty($_POST['name'])) $error[] = 'Введите название темы';
                        if (empty($_POST['message'])) $error[] = 'Введите сообщение темы';

                        if (empty($error))
                        {
                            if ($user['access'] < 4){
                                $_POST['name'] = htmlspecialchars($_POST['name']);
                                $_POST['message'] = htmlspecialchars($_POST['message']);
                            }
                            $db->query('UPDATE forum_topics 
                                            SET name = ?,
                                                message = ?,
                                                dateUpd = ?,
                                                whoUpd = ? 
                                            WHERE id = ?', [$_POST['name'], $_POST['message'], time(), $user['id'], $topic['id']]);
                            die(header('Location: /forum/topic/'.$topic['id']));
                        } else {
                            $m->pda($error);
                        }
                    }
                    ?>
                    <h1>Изменение темы обсуждения</h1><br/>
                    <form method="post">
                        Название обсуждения
                        <input type="text" minlength="3" maxlength="255" placeholder="Введите название обсуждения"
                               name="name" value="<?php echo $topic['name']; ?>" required/><br/>
                        Текст обсуждения
                        <textarea name="message" minlength="5"
                                  required><?php echo $topic['message']; ?></textarea><br/>
                        <input type="submit" name="topicEdit" value="Отредактировать тему">
                    </form>
                <?php else: ?>
                    <div class="line-height">
                        <?php echo $m->message($topic['message']); ?>
                    </div>
                    <div class="small">
                        <?php echo date("d.m.Y в H:i:s", $topic['dateAdd']) . (!empty($topic['dateUpd']) ? '<br/> Ред.: ' . date("d.m.Y в H:i:s", $topic['dateUpd']) . ' (' . $u->getLogin($topic['whoUpd'], true) . ')' : ''); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        if ($topic['closed'] == 1) $m->pda(['Обсуждение закрыто.']);

        $countMessages = $db->getCount('SELECT count(`id`) FROM `forum_messages` WHERE `id_topic` = ?', [$id]);

        if (isset($_REQUEST['send'])) {
            if (empty($_POST['message'])) $error[] = 'Сообщение не может быть пустым.';
            if ($topic['closed'] == 1 && $user['access'] < 3) $error[] = 'Нельзя оставлять сообщения в закрытой теме.';

            if (empty($error)) {
                if ($user['access'] < 4){
                    $_POST['message'] = htmlspecialchars($_POST['message']);
                }
                $db->query('INSERT INTO forum_messages (id_topic, id_user, message, dateAdd) 
                                        VALUES (?, ?, ?, ?)',
                    [$topic['id'], $user['id'], $_POST['message'], time()]);
                $db->query('UPDATE `forum_topics` SET `lastUpdate` = ? WHERE id = ?', [time(), $topic['id']]);

                header('Location: /forum/topic/' . $topic['id'] . '?page=' . $m->getPage($countMessages));
            } else {
                $m->pda($error);
            }
        }

        if ($countMessages > 0) {
            $pg = new Game\Paginations(10, 'page');
            $pg->setTotal($countMessages);
            $getMessages = $db->getAll('SELECT * 
                                        FROM `forum_messages` 
                                        WHERE `id_topic` = ?
                                        ' . $pg->getLimit('dateAdd', 'ASC'), [$id]);

            $num = 1 * ($pg->_page * 10 - 9);
            foreach ($getMessages as $message) {
                ?>
                <div class="flexpda main top pos">
                    <div class="flexpda-image center">
                        <a href="/id<?php echo $message['id_user']; ?>">
                            <img src="/files/other/battle.png" class="top-img main" alt="">
                        </a>
                        <div class="main">#<?php echo $num++; ?></div>
                    </div>
                    <div class="flexpda-content" id="<?php echo $num; ?>" style="padding-left: 10px;">
                        <div class="forum-id"><?php echo $u->getLogin($message['id_user'], true); ?> <?php echo($message['id_user'] == $topic['author'] ? '/ Автор обсуждения' : null); ?></div>
                        <div class="line-height">
                            <?php
                            if (isset($_GET['editMsg']) and $_GET['editMsg'] == $message['id'] and ($message['id_user'] == $user['id'] or ($user['access'] > 1))) {
                                if (isset($_POST['editMessage'])) {
                                    if (!$topic['closed'] or $user['access'] > 1) {

                                        if ($user['access'] < 4){
                                            $_POST['message'] = htmlspecialchars($_POST['message']);
                                        }
                                        if (empty($_POST['message'])) $error[] = 'Сообщение не может быть пустым';

                                        if (empty($error)){
                                            $db->query('UPDATE forum_messages SET message = ?, dateUpd = ?, whoUpd = ? WHERE id = ?', [$_POST['message'], time(), $user['id'], $message['id']]);
                                            die(header('Location: /forum/topic/' . $topic['id'] . '?page=' . $pg->_page));
                                        } else {
                                            $m->pda($error);
                                        }
                                        
                                    }
                                }
                                ?>
                                <h1>Редактирование сообщения</h1>
                                <form method="post">
                                    <textarea name="message" cols="5"><?php echo $message['message']; ?></textarea>
                                    <input type="submit" name="editMessage" value="Изменить">
                                </form>
                                <?php
                            } else echo $m->message($message['message']);
                            ?>
                        </div>
                        <div class="small">
                            <?php echo date("d.m.Y в H:i:s", $message['dateAdd']) . (!empty($message['dateUpd']) ? '<br/> Ред.: ' . date("d.m.Y в H:i:s", $message['dateUpd']) . ' (' . $u->getLogin($message['whoUpd'], true) . ')' : ''); ?>
                        </div>
                        <?php if ($user['access'] > 1 || ($user['id'] == $message['id_user'] and !$topic['closed'])): ?>
                            <div class="flex-container pos-block">
                                <div class="flex-link">
                                    <a href="<?php echo $_SERVER['REQUEST_URI']; ?><?php echo(strpos($_SERVER['REQUEST_URI'], '?page') ? '&' : '?'); ?>editMsg=<?php echo $message['id']; ?>#<?php echo $num; ?>"
                                       class="href">Редактировать</a>
                                </div>

                                <?php if ($user['access'] > 1):
                                        if (isset($_GET['delMsg']) and $_GET['delMsg'] == $message['id'] and isset($_GET['yes'])){
                                            $db->query('DELETE FROM `forum_messages` WHERE `id` = ?', [$message['id']]);
                                            $m->to('/forum/topic/'.$id);
                                        } elseif(isset($_GET['delMsg']) and $_GET['delMsg'] == $message['id']) {
                                            ?>
                                            <div class="block question">
                                                <div class="question-answer center access-2">
                                                    Вы действительно хотите удалить это сообщение?
                                                </div>
                                                <div class="question-option">
                                                    <a href="/forum/topic/<?php echo $id; ?>?delMsg=<?php echo $message['id'];?>&yes" class="href"
                                                       style="margin-bottom: 1px;">Да</a>
                                                    <a href="/forum/topic/<?php echo $id; ?>" class="href">Нет</a>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="flex-link">
                                                <a href="<?php echo $_SERVER['REQUEST_URI']; ?><?php echo(strpos($_SERVER['REQUEST_URI'], '?page') ? '&' : '?'); ?>delMsg=<?php echo $message['id']; ?>#<?php echo $num;?>"
                                                   class="href">Удалить</a>
                                            </div>
                                            <?php
                                        }
                                    endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            echo $pg->render();
        } elseif ($countMessages == 0 && $topic['closed'] == 0) $m->pda(['Сообщений нет, стань первым!']);

        if ($topic['closed'] == 0 || ($topic['closed'] == 1 && $user['access'] > 3)) {
            ?>
            <h1>Ответ</h1>
            <form method="post">
                <textarea name="message" cols="3" minlength="5" required placeholder="Введите сообщение..."></textarea>
                <input type="submit" value="Отправить" name="send" class="w-100">
            </form>
            <?php
        }
        echo "<a href='/forum/category/{$topic['id_category']}' class='href'>Назад к разделу «{$topic['name_category']}»</a>";
        break;
}
require '../../main/foot.php';
?>
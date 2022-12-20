<?php
require_once('conf.php');

$error_log_dest = $error_log_dir . '/admin.log';

$user_id = get_authorized_user_id();

if (!$user_id) {
    header('Location: ' . $url_unauthorized);
    die();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .error { color: red; }
            form { margin: 0.5em 0; padding: 0.5em; background-color: linen; max-width: 480px; }
            form.deleted { background-color: lightgray; }
            form div { line-height: 2em; }
            .item-label { display: inline-block; width: 20%; text-align: right; vertical-align: top; }
            textarea, input[type="text"], select { width: 75%; }
            .footer { padding: 1em; }
        </style>
        <title><?php echo $title; ?></title>
    </head>
    <body>
        <h1><?php echo $title; ?></h1>
        <p>Logged in as: <?php echo $user_id; ?></p>
<?php if (isset($_GET['error'])) { ?>
        <p class="error"><?php echo $_GET['error']; ?></p>
<?php } ?>
<?php
    $stmt = $pdo->query("SELECT * FROM message WHERE sent_at is NULL ORDER BY scheduled_after DESC");
    $messages = $stmt->fetchAll();
    $stmt = $pdo->query("SELECT * FROM template ORDER BY template_id");
    $templates = $stmt->fetchAll();
    $stmt = $pdo->query("SELECT * FROM category ORDER BY category_id");
    $categories = $stmt->fetchAll();
?>
        <h2>手動投稿</h2>
        <div>既定の投稿時刻に投稿します。今すぐ投稿したい場合はこの機能を使わず、直接投稿してください。</div>
        <?php
    array_unshift($messages, array(
        'message_id' => null,
        'template_id' => null,
        'content' => '',
        'link' => $default_link,
        'scheduled_after' => date('c'),
        'sent_at' => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    ));
    foreach ($messages as $row) {
?>
        <form
            id="form_message"
            action="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>"
            method="POST"
            <?php echo $row['deleted_at'] ? 'class="deleted"' : '' ?>
        >
            <input type="hidden" name="table" value="message">
            <input type="hidden" name="message_id" value="<?php echo $row['message_id']; ?>">
        <?php if ($row['message_id'] === null) { ?>
            <div>
                <span class="item-label">ID:</span>
                自動採番
            </div>
            <input type="hidden" name="new" value="y">
        <?php } else { ?>
            <input type="hidden" name="new" value="n">
            <div>
                <span class="item-label">ID:</span>
                <?php echo $row['message_id']; ?>
            </div>
        <?php } ?>
            <div>
                <span class="item-label">内容:</span>
                <textarea
                    name="content"
                    rows="5"
                    required
                ><?php echo htmlspecialchars($row['content']); ?></textarea>
            </div>
            <div>
                <span class="item-label">リンク:</span>
                <input
                    type="text"
                    name="link"
                    value="<?php echo htmlspecialchars($row['link']); ?>"
                >
            </div>
            <div>
                <span class="item-label">投稿待機:</span>
                <input
                    type="datetime-local"
                    name="scheduled_after"
                    value="<?php echo toLocalDate($row['scheduled_after']); ?>"
                    required
                >
            </div>
        <?php if ($row['message_id'] !== null) { ?>
            <div>
                <span class="item-label">作成:</span>
                <code><?php echo toLocalDate($row['created_at']); ?></code>
            </div>
            <div>
                <span class="item-label">更新:</span>
                <code><?php echo toLocalDate($row['updated_at']); ?></code>
            </div>
            <div>
                <span class="item-label">削除:</span>
                <input
                    type="checkbox"
                    name="deleted"
                    value="1"
                    <?php echo $row['deleted_at'] ? 'checked' : ''; ?>
                >
                <code><?php echo toLocalDate($row['deleted_at']); ?></code>
            </div>
        <?php } ?>
            <div>
                <button type="submit">保存</button>
                <button type="reset">リセット</button>
            </div>
        </form>
<?php
    }
?>
        <h2>テンプレート</h2>
        <div>記事のタイトルとURLを埋め込む場所は <code>%%content%%</code> および <code>%%link%%</code> としてください。</div>
        <div>カテゴリが「更新のみ」の場合、 <code>%%content%%</code> は使用できません。</div>
<?php
    $templates[] = array(
        'template_id' => null,
        'category_id' => null,
        'name' => '',
        'body' => "%%content%%\n%%link%%",
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    );
    foreach ($templates as $row) {
?>
        <form
            id="form_template"
            action="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>"
            method="POST"
            <?php echo $row['deleted_at'] ? 'class="deleted"' : '' ?>
        >
            <input type="hidden" name="table" value="template">
            <input type="hidden" name="template_id" value="<?php echo $row['template_id']; ?>">
        <?php if ($row['template_id'] === null) { ?>
            <div>
                <span class="item-label">ID:</span>
                自動採番
            </div>
            <input type="hidden" name="new" value="y">
        <?php } else { ?>
            <input type="hidden" name="new" value="n">
            <div>
                <span class="item-label">ID:</span>
                <?php echo $row['template_id']; ?>
            </div>
        <?php } ?>
            <div>
                <span class="item-label">カテゴリ:</span>
        <?php
            if (strval($row['category_id']) === '0') {
                ?><input type="hidden" name="category_id" value="0"><?php
                foreach ($categories as $item) {
                    if (strval($item['category_id']) === strval($row['category_id'])) {
                        echo htmlspecialchars($item['name']);
                    }
                }
            } else {
                ?><select name="category_id"><?php

                foreach ($categories as $item) {
                    if (!$item['deleted_at'] && strval($item['category_id']) !== '0') {
                        $selected = $item['category_id'] == $row['category_id'] ? ' selected' : '';
                        echo '<option value="' . $item['category_id'] . '"' . $selected . '>' . htmlspecialchars($item['name']) . '</option>';
                    }
                }

                ?></select><?php
            }
        ?>
            </div>
            <div>
                <span class="item-label">名称:</span>
                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($row['name']); ?>"
                    required
                >
            </div>
            <div>
                <span class="item-label">書式:</span>
                <textarea
                    name="body"
                    rows="5"
                    required
                ><?php echo htmlspecialchars($row['body']); ?></textarea>
            </div>
        <?php if ($row['template_id'] !== null) { ?>
            <div>
                <span class="item-label">作成:</span>
                <code><?php echo toLocalDate($row['created_at']); ?></code>
            </div>
            <div>
                <span class="item-label">更新:</span>
                <code><?php echo toLocalDate($row['updated_at']); ?></code>
            </div>
            <div>
                <span class="item-label">削除:</span>
                <input
                    type="checkbox"
                    name="deleted"
                    value="1"
                    <?php echo $row['deleted_at'] ? 'checked' : ''; ?>
                    <?php echo $row['category_id'] == 0 ? 'disabled' : ''; ?>
                >
                <code><?php echo toLocalDate($row['deleted_at']); ?></code>
            </div>
        <?php } ?>
            <div>
                <button type="submit">保存</button>
                <button type="reset">リセット</button>
            </div>
        </form>
<?php
    }
?>
        <h2>カテゴリー</h2>
        <div>ID は WordPress のカテゴリーの ID です。ID: 0 は手動投稿です。</div>
        <div>「更新のみ」の場合、個々の記事のタイトルを含まない更新のお知らせを投稿します。</div>
        <div>「優先度」は小さな値が優先です。優先度 <code>0</code> は手動投稿専用です。それ以外のカテゴリーは <code>1</code> 以上の値を設定してください。</div>

<?php
    $categories[] = array(
        'category_id' => null,
        'name' => '',
        'update_only' => 0,
        'priority' => 1,
        'checked_at' => null,
        'created_at' => null,
        'updated_at' => null,
        'deleted_at' => null,
    );
    foreach ($categories as $row) {
?>
        <form
            id="form_category"
            action="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>"
            method="POST"
            <?php echo $row['deleted_at'] ? 'class="deleted"' : '' ?>
        >
            <input type="hidden" name="table" value="category">
            <input type="hidden" name="category_id" value="<?php echo $row['category_id']; ?>">
        <?php if ($row['category_id'] === null) { ?>
            <div>
                <span class="item-label">ID:</span>
                <input
                    type="number"
                    name="category_id"
                    value=""
                    required
                    min="1"
                >
            </div>
            <input type="hidden" name="new" value="y">
        <?php } else { ?>
            <input type="hidden" name="new" value="n">
            <div>
                <span class="item-label">ID:</span>
                <?php echo $row['category_id']; ?>
            </div>
        <?php } ?>
            <div>
                <span class="item-label">名称:</span>
                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($row['name']); ?>"
                    required
                >
            </div>
            <div>
                <span class="item-label">更新のみ:</span>
                <input
                    type="checkbox"
                    name="update_only"
                    value="1"
                    <?php echo $row['update_only'] ? 'checked' : ''; ?>
                    <?php echo strval($row['category_id']) === '0' ? 'disabled' : ''; ?>
                >
            </div>
            <div>
                <span class="item-label">優先度:</span>
                <input
                    type="number"
                    name="priority"
                    value="<?php echo $row['priority']; ?>"
                    min="0"
                    required
                    <?php echo strval($row['category_id']) === '0' ? 'disabled' : ''; ?>
                >
            </div>
        <?php if ($row['category_id'] !== null) { ?>
            <div>
                <span class="item-label">処理日時:</span>
                <code><?php echo toLocalDate($row['checked_at']); ?></code>
            </div>
            <div>
                <span class="item-label">作成:</span>
                <code><?php echo toLocalDate($row['created_at']); ?></code>
            </div>
            <div>
                <span class="item-label">更新:</span>
                <code><?php echo toLocalDate($row['updated_at']); ?></code>
            </div>
            <div>
                <span class="item-label">削除:</span>
                <input
                    type="checkbox"
                    name="deleted"
                    value="1"
                    <?php echo $row['deleted_at'] ? 'checked' : ''; ?>
                    <?php echo strval($row['category_id']) === '0' ? 'disabled' : ''; ?>
                >
                <code><?php echo toLocalDate($row['deleted_at']); ?></code>
            </div>
        <?php } ?>
            <div>
                <button type="submit">保存</button>
                <button type="reset">リセット</button>
            </div>
        </form>
<?php
    }
?>
        <div class="footer">
            <a href="https://github.com/MichinobuMaeda/squirrelwheel" target="_blank" rel="noopener noreferrer">
                squirrelwheel
            </a>
            Copyright 2022, 2023 Michinobu Maeda.
        </div>
    </body>
</html>
<?php
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    logging('start');

    try {
        $pdo = new PDO('sqlite:' . $db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $ts = date('c');

        logging(json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($_POST['table'] == 'category') {

            $new = $_POST['new'];
            $category_id = intval($_POST['category_id']);
            $name = trim($_POST['name']);
            $update_only = (isset($_POST['update_only']) && $_POST['update_only'] == '1') ? 1 : 0;
            $priority = intval($_POST['priority']);
            $deleted_at = (isset($_POST['deleted']) && $_POST['deleted'] == '1') ? $ts : '';

            if ($new == 'y') {
                $stmt = $pdo->prepare(<<<END
INSERT INTO category (
       category_id
     , name
     , update_only
     , priority
     , checked_at
     , created_at
     , updated_at
) VALUES (
       :category_id
     , :name
     , :update_only
     , :priority
     , :checked_at
     , :created_at
     , :updated_at
)
END
                );
                $stmt->execute([
                    'category_id' => $category_id,
                    'name' => $name,
                    'update_only' => $update_only,
                    'priority' => $priority,
                    'checked_at' => $ts,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            } else {
                $stmt = $pdo->prepare(<<<END
UPDATE category SET
       name        = :name
     , update_only = :update_only
     , priority    = :priority
     , updated_at  = :updated_at
     , deleted_at  = :deleted_at
 WHERE category_id = :category_id
END
                );
                $stmt->execute([
                    'category_id' => $category_id,
                    'name' => $name,
                    'update_only' => $update_only,
                    'priority' => $priority,
                    'updated_at' => $ts,
                    'deleted_at' => $deleted_at,
                ]);
            }

        } else if ($_POST['table'] == 'template') {

            $new = $_POST['new'];
            $template_id = intval($_POST['template_id']);
            $category_id = intval($_POST['category_id']);
            $name = trim($_POST['name']);
            $body = trim($_POST['body']);
            $deleted_at = (isset($_POST['deleted']) && $_POST['deleted'] == '1') ? $ts : '';

            if ($new == 'y') {
                $stmt = $pdo->prepare(<<<END
INSERT INTO template (
       category_id
     , name
     , body
     , created_at
     , updated_at
) VALUES (
       :category_id
     , :name
     , :body
     , :created_at
     , :updated_at
)
END
                );
                $stmt->execute([
                    'category_id' => $category_id,
                    'name' => $name,
                    'body' => $body,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            } else {
                $stmt = $pdo->prepare(<<<END
UPDATE template SET
       category_id = :category_id
     , name        = :name
     , body        = :body
     , updated_at  = :updated_at
     , deleted_at  = :deleted_at
 WHERE template_id = :template_id
END
                );
                $stmt->execute([
                    'template_id' => $template_id,
                    'category_id' => $category_id,
                    'name' => $name,
                    'body' => $body,
                    'updated_at' => $ts,
                    'deleted_at' => $deleted_at,
                ]);
            }

        } else if ($_POST['table'] == 'message') {

            $new = $_POST['new'];
            $message_id = intval($_POST['message_id']);
            $content = trim($_POST['content']);
            $link = trim($_POST['link']);
            $scheduled_after = date('c', strtotime($_POST['scheduled_after']));
            $deleted_at = (isset($_POST['deleted']) && $_POST['deleted'] == '1') ? $ts : '';

            if ($new == 'y') {
                $stmt = $pdo->prepare(<<<END
INSERT INTO message (
       template_id
     , content
     , link
     , scheduled_after
     , created_at
     , updated_at
) VALUES (
       0
     , :content
     , :link
     , :scheduled_after
     , :created_at
     , :updated_at
)
END
                );
                $stmt->execute([
                    'content' => $content,
                    'link' => $link,
                    'scheduled_after' => $scheduled_after,
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);
            } else {
                $stmt = $pdo->prepare(<<<END
UPDATE message SET
       content     = :content
     , link        = :link
     , scheduled_after = :scheduled_after
     , updated_at  = :updated_at
     , deleted_at  = :deleted_at
 WHERE message_id = :message_id
END
                );
                $stmt->execute([
                    'message_id' => $message_id,
                    'content' => $content,
                    'link' => $link,
                    'scheduled_after' => $scheduled_after,
                    'updated_at' => $ts,
                    'deleted_at' => $deleted_at,
                ]);
            }
        }

        logging('end');
        header('Location: ' . $_SERVER['REQUEST_URI']);

    } catch (Exception $e) {
        logging($e->getMessage());
        header('Location: ' . $_SERVER['REQUEST_URI'] . '?error=' . $e->getMessage());
    }
} else {
    http_response_code(405);
}

function toLocalDate($iso) {
    return $iso ? date('Y-m-d H:i', strtotime($iso)) : '';
}

function logging($message) {
    global $error_log_type, $error_log_dest, $user_id;
    $ts = date('c');
    error_log($ts . ' ' . $user_id . ' ' . $message . "\n", $error_log_type, $error_log_dest);
}

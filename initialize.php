<?php
require_once('conf.php');

try {
    touch($db_path);
    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec(<<<END
CREATE TABLE IF NOT EXISTS category (
       category_id INTEGER NOT NULL PRIMARY KEY
     , name        TEXT    NOT NULL UNIQUE
     , update_only INTEGER NOT NULL DEFAULT 0
     , priority    INTEGER NOT NULL DEFAULT 1
     , checked_at  TEXT
     , created_at  TEXT
     , updated_at  TEXT
     , deleted_at  TEXT
) WITHOUT ROWID
END
    );

    $pdo->exec(<<<END
CREATE TABLE IF NOT EXISTS template (
      template_id INTEGER NOT NULL PRIMARY KEY
    , category_id INTEGER NOT NULL
    , name        TEXT    NOT NULL UNIQUE
    , body        TEXT    NOT NULL
    , created_at  TEXT
    , updated_at  TEXT
    , deleted_at  TEXT
    , FOREIGN KEY (category_id) REFERENCES category (category_id)
)
END
    );

    $pdo->exec(<<<END
CREATE TABLE IF NOT EXISTS message (
       message_id  INTEGER NOT NULL PRIMARY KEY
     , template_id INTEGER NOT NULL
     , content     TEXT
     , link        TEXT
     , scheduled_after TEXT NOT NULL DEFAULT '2000-01-01T00:00:00.000Z'
     , sent_at     TEXT
     , created_at  TEXT
     , updated_at  TEXT
     , deleted_at  TEXT
     , FOREIGN KEY (template_id) REFERENCES template (template_id)
)
END
    );

    $stmt = $pdo->query("SELECT category_id FROM category where category_id = 0");
    $row = $stmt->fetch();

    if (!$row) {
        $ts = date('c');
        $pdo->exec(<<<END
INSERT INTO category (
       category_id
     , name
     , update_only
     , priority
     , checked_at
     , created_at
     , updated_at
) VALUES (
       0
     , 'MANUAL'
     , 0
     , 0
     , '$ts'
     , '$ts'
     , '$ts'
);
END
        );
    }

    $stmt = $pdo->query("SELECT template_id FROM template where template_id = 0");
    $row = $stmt->fetch();

    if (!$row) {
        $ts = date('c');
        $pdo->exec(<<<END
INSERT INTO template (
      template_id
    , category_id
    , name
    , body
    , created_at
    , updated_at
) VALUES (
      0
    , 0
    , 'MANUAL'
    , '%%content%%
%%link%%'
    , '$ts'
    , '$ts'
);
END
        );
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

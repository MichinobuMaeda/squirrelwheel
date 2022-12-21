<?php
// 認証されていない場合のリダイレクト先
$url_unauthorized = 'https://example.com/admin';

// 権限のあるユーザIDの取得
function get_authorized_user_id() {
    return 'testuser';
}

// SQLiteのデータファイルのパス
$db_path = __DIR__ . '/data/database.sqlite3';

// 管理者機能のタイトル
$title = 'squirrelwheel';

// デフォルトのリンクURL
$default_link = 'https://example.com';

// カテゴリーのURL
$url_category = 'https://example.com/?cat=';

// タイムゾーン
date_default_timezone_set('Asia/Tokyo');

// ログの設定: https://www.php.net/manual/ja/function.error-log.php
$error_log_type = 3;
$error_log_dir = __DIR__ . '/log';

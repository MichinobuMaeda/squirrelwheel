<?php
// 認証されていない場合のリダイレクト先
$url_unauthorized = 'https://example.com';

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

// タイムゾーン
date_default_timezone_set('Asia/Tokyo');

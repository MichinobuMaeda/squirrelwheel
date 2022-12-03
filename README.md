# squirrelwheel

## 1. 概要

WordPress の feed を取得して、更新情報を Twitter に投稿する。
投稿のタイミングを自動的に分散させる。
投稿のテンプレートを編集することができる。

手動の投稿も可能。その場合は、投稿日時の指定が可能。

## 2. 開発環境

```bash
$ git --version
git version 2.38.1

$ php --version
PHP 8.1.4 ... ...

$ composer --version
Composer version 2.4.4 ... ...

$ node --version
v16.18.1

$ git clone git@github.com:MichinobuMaeda/squirrelwheel.git
$ cd squirrelwheel
$ touch database/database.sqlite
$ cp .env.local .env
$ composer install
$ php artisan serve
```

## 3. 仕様

```mermaid
classDiagram
class Category {
    bigInteger categoryId
    text name
    boolean updateOnly
    timestamp updated
}
class Message {
    bigIncrements id
    bigInteger templateId
    timestamp scheduledAfter
    timestamp sentAt
    text url
    text content
}
class Template {
    bigIncrements id
    bigInteger categoryId
    text name
    text body
}
Message o-- Template
Category --o Template
```

### 3.1. 定時のジョブ

- 全ての `Category` の Atom feed を `?cat=[categoryId]&feed=atom` から取得する。
- `Category::updated` より `/feed/updated` が後の場合、
    - `Category::updateOnly = true` の場合、
        - `Category::templateId` の `Template` を選択する。[1]
        - `Message` を作成する。
            - `templateId` は選択した `Template`
            - `scheduledAfter` は指定無し
            - `url` は指定無し
            - `content` は指定無し
    - `Category::updateOnly = true` ではない場合、
        - すべての項目 `/feed/entry` について、
            - `Category::updated` より `/feed/entry/updated` が後の場合、
                - `Category::templateId` の `Template` を選択する。[1]
                - `Message` を作成する。
                    - `templateId` は選択した `Template`
                    - `scheduledAfter` は指定無し
                    - `url` は `/feed/entry/id`
                    - `content` は `/feed/entry/title`
    - `/feed/updated` を `Category::updated` に保存する。
- 現在の日付の `sentAt` の `Message` が無い場合、
    - `sentAt` が未記入 で `scheduledAfter` が 現在の日時以降ではない `Message`について、
        - 投稿する。[2]
        - `sentAt` に現在の日時を保存する。
        - 1件処理したら以降の `Message` はスキップ。

[1] `Category` に対応する `Template` が複数ある場合はランダムに選択する。

[2] `scheduledAfter` が 現在の日時以前の `Message` を優先する。

### 3.2. `Category` の編集

- `categoryId`
    - 制約: 必須、一意、整数値
    - デフォルト値: `0` -- 手動投稿
- `name`
    - 制約: 必須、一意
- `updateOnly`
- `updated`
    - 制約: 必須
    - デフォルト値: 10日前

### 3.3. `Template` の編集

- `name`
    - 制約: 必須、一意
- `categoryId`
    - 制約: 必須、存在する `Category` から選択
- `body`
    - 制約: 必須

`Message` の `url`, `content` の埋め込み場所は
`Template` の `body` に `%%url%%`, `%%content%%` と記載する。

### 3.4. 手動投稿の編集

- `templateId`
    - 制約: 必須、 `categoryId = 0` の `Template` から選択
- `content`
- `scheduledAfter`

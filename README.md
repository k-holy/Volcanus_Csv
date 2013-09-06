#Volcanus_Csv

CSV形式ファイルの入出力処理を簡潔に行うためのPHPクラスライブラリです。
出力時に利用できる Volcanus\Csv\Writer および、入力時に利用できる Volcanus\Csv\Reader によって構成されています。

特に、データベースから取得したデータのCSV形式でのエクスポートや、CSV形式ファイルからのデータベースへのインポートにおいて、簡潔かつ柔軟に対応できることを目標として作成しました。

読み込みのみであれば [k-holy/volcanus-csv-parser](https://github.com/k-holy/volcanus-csv-parser) の方が使いやすいかもしれません。


##対応環境

* PHP 5.3以降
* mbstring拡張

##依存ライブラリ

1.1.0よりConfigurationクラスを同梱のものに置き換えましたので、依存ライブラリはありません。


##Volcanus\Csv\Writer

###特徴

* 区切り文字、囲み文字、エスケープ文字に任意の1文字を指定できます。
* データの入力エンコーディングおよびCSVの出力エンコーディングを指定することで、自動でエンコーディング変換されます。
* SplFileObjectへの出力を前提としており、 組み込みプロトコル/ラッパーによる一時データのメモリへの出力に対応しています。[php:// - Manual](http://jp2.php.net/manual/ja/wrappers.php.php)
* 入力データとして配列または Traversable 実装オブジェクトを指定することで、逐次出力を行えます。
* CSVのフィールド設定に無名関数を指定することで、連想配列および ArrayAccess 実装オブジェクトから任意の要素を加工したフィールドを出力できます。
* 必要であれば、生成したCSVの内容に合わせたレスポンスヘッダ（Content-Type, Content-Disposition, Content-Length）を出力できます。（ダウンロード時のファイル名も指定できます）

###使い方

```php
<?php

$file = new \SplFileObject('php://temp', 'r+');

$writer = \Volcanus\Csv\Writer(array(
    'inputEncoding'    => 'UTF-8',
    'outputEncoding'   => 'SJIS',
    'writeHeaderLine'  => true,
    'responseFilename' => 'users.csv',
));

$writer->fields(array(
    array('id'   , 'ユーザーID'),
    array('name' , '名前'),
));

$db = new \PDO('sqlite:/path/to/database');
$statement = $db->query('SELECT id, name FROM users', \PDO::FETCH_ASSOC);

// データベースから取得した結果をCSVに変換してファイルに書き込む
$writer->file = $file
$writer->write($statement);

// レスポンスヘッダとCSVを出力
$writer->send();

```

###注意点

SplFileObjectを前提としていますが、CSVの加工は独自の処理を行なっています。
そのため、Volcanus\Csv\Writer で出力する場合、[SplFileObject::setCsvControl()](http://jp2.php.net/manual/ja/splfileobject.setcsvcontrol.php) で設定した値は利用されません。


##Volcanus\Csv\Reader

###特徴

* 区切り文字、囲み文字、エスケープ文字に任意の1文字を指定できます。
* もちろん、囲み文字による改行を含むフィールドにも対応しています。
* CSVの入力エンコーディングおよびデータの出力エンコーディングを指定することで、自動でエンコーディング変換されます。
* SplFileObjectからの入力を前提としており、ファイルシステムの他にも様々な組み込みプロトコル/ラッパーが利用できます。[サポートするプロトコル/ラッパー - Manual](http://jp2.php.net/manual/ja/wrappers.php)
* フィルタとして無名関数を指定することで、1レコード分のCSVをフェッチする際に任意の処理を実行できます。任意のオブジェクトへの変換、バリデーション、データベースへの保存などです。
* 一括読み込み時に、読み込んだCSVレコードの件数と、取得したCSVレコードの件数をそれぞれ参照することができます。これを利用して、フィルタの無名関数において「1件目のデータはヘッダ行とみなして無視」「何件目のデータでエラーが発生したかを通知」といったことが可能です。

###使い方

```php
<?php

$file = new \SplFileObject('php://temp', 'r+');

$writer = \Volcanus\Csv\Writer(array(
    'inputEncoding'    => 'UTF-8',
    'outputEncoding'   => 'SJIS',
    'writeHeaderLine'  => true,
    'responseFilename' => 'users.csv',
));

$writer->fields(array(
    array('id'   , 'ユーザーID'),
    array('name' , '名前'),
));

$db = new \PDO('sqlite:/path/to/database');
$statement = $db->query('SELECT id, name FROM users', \PDO::FETCH_ASSOC);

// データベースから取得した結果をCSVに変換してファイルに書き込む
$writer->file = $file
$writer->write($statement);

$reader = new \Volcanus\Csv\Reader(array(
    'inputEncoding'  => 'SJIS',
    'outputEncoding' => 'UTF-8',
));

// CSVファイル1レコード毎のフィルタを定義
$reader->appendFilter(function($item) use ($reader) {
    // 1件目はヘッダ行なので除外する。
    // FALSEを返すとparsedのみカウントされ、fetchedはカウントされない。
    if ($reader->parsed === 1) {
        return false;
    }
    if ($reader->fetched > 10000) {
        throw new \RuntimeException('件数多すぎ');
    }
    $user = array(
        'id'   => $item[0],
        'name' => $item[1],
    );
    return sprintf('<li>[%s]%s</li>',
        htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($item[1], ENT_QUOTES, 'UTF-8')
    );
});

$writer->file = file;

// CSVファイルを読み込んでHTML出力
echo sprintf('<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
</head>
<body>
<ul>%s</ul>
</body>
</html>', implode("\n", $reader->fetchAll()));

```

###注意点

SplFileObjectを前提としていますが、CSVの加工は独自の処理を行なっています。
そのため、Volcanus\Csv\Reader でファイルを解析する場合、[SplFileObject::setCsvControl()](http://jp2.php.net/manual/ja/splfileobject.setcsvcontrol.php) で設定した値は利用されません。
また、[fgetcsv()](http://jp2.php.net/manual/ja/function.fgetcsv.php) , [SplFileObject::fgetcsv()](http://jp2.php.net/manual/ja/splfileobject.fgetcsv.php) , [str_getcsv()](http://jp2.php.net/manual/ja/function.str-getcsv.php) といった標準の関数とは異なる結果を返す可能性があります。

セキュリティのため、復帰・改行・水平タブ・スペース以外の制御コードを自動で削除します。
そのため（ないとは思いますが）、バイナリデータには対応していません。


##その他の情報

* FileMakerのCSVは、フィールド内の改行コードが垂直タブに変換されるという独自仕様です。特に対応策は施していませんが、フィルタ系の機能で str_replace("\x0b", "\r\n", $value) のように変換すれば一応対応できるはずです。
* str_getcsv()の第4引数（エスケープ文字）の正常動作を確認でき次第、そちらによる解析をデフォルト設定にするかも。

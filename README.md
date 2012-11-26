#Volcanus_Csv

CSV形式ファイルの入出力処理を簡潔に行うためのPHPクラスライブラリです。
出力時に利用できる Volcanus\Csv\Writer および、入力時に利用できる Volcanus\Csv\Reader によって構成されています。

特に、データベースから取得したデータのCSV形式でのエクスポートや、CSV形式ファイルからのデータベースへのインポートにおいて、簡潔かつ柔軟に対応できることを目標として作成しました。


##対応環境

* PHP 5.3以降
* mbstring必須


##Volcanus\Csv\Writer

###特徴

* 区切り文字、囲み文字、エスケープ文字に任意の1文字を指定できます。
* データの入力エンコーディングおよびCSVの出力エンコーディングを指定することで、自動でエンコーディング変換されます。
* 入力データとして配列または Traversable 実装オブジェクトを指定することで、逐次出力を行えます。
* SplFileObjectへの出力を前提としており、 組み込みプロトコル/ラッパーによる一時データのメモリへの出力に対応しています。[php:// - Manual](http://jp2.php.net/manual/ja/wrappers.php.php)
* レスポンスヘッダ（Content-Type, Content-Disposition, Content-Length）を自動出力します。（ダウンロード時のファイル名も指定できます）
* CSVのフィールド設定に無名関数を指定することで、連想配列および ArrayAccess 実装オブジェクトから任意の要素を加工したフィールドを出力できます。

###注意点

SplFileObjectを前提としていますが、CSVの加工は独自の処理を行なっています。
そのため、Volcanus\Csv\Writer で出力する場合、[SplFileObject::setCsvControl()](http://jp2.php.net/manual/ja/splfileobject.setcsvcontrol.php) で設定した値は利用されません。


##Volcanus\Csv\Reader

###特徴

* 区切り文字、囲み文字、エスケープ文字に任意の1文字を指定できます。
* もちろん、囲み文字による改行を含むフィールドにも対応しています。
* CSVの入力エンコーディングおよびデータの出力エンコーディングを指定することで、自動でエンコーディング変換されます。
* SplFileObjectからの入力を前提としており、ファイルシステムの他にも様々な組み込みプロトコル/ラッパーが利用できます。[サポートするプロトコル/ラッパー - Manual](http://jp2.php.net/manual/ja/wrappers.php)
* 一括読み込み時に、ヘッダ行があるCSVファイルを想定し、1行目を無視するよう設定できます。
* 一括読み込み時に、空行を無視するかどうかを指定できます。
* フィルタとして無名関数を指定することで、1レコード分のCSVをフェッチする際に任意の処理を実行できます。たとえば任意のオブジェクトへの変換、バリデーション、データベースへの保存などです。

###注意点

SplFileObjectを前提としていますが、CSVの加工は独自の処理を行なっています。
そのため、Volcanus\Csv\Reader でファイルを解析する場合、[SplFileObject::setCsvControl()](http://jp2.php.net/manual/ja/splfileobject.setcsvcontrol.php) で設定した値は利用されません。
また、[fgetcsv()](http://jp2.php.net/manual/ja/function.fgetcsv.php) , [SplFileObject::fgetcsv()](http://jp2.php.net/manual/ja/splfileobject.fgetcsv.php) , [str_getcsv()](http://jp2.php.net/manual/ja/function.str-getcsv.php) といった標準の関数とは異なる結果を返す可能性があります。

セキュリティのため、復帰・改行・水平タブ・スペース以外の制御コードを自動で削除します。
そのため（ないとは思いますが）、バイナリデータには対応していません。


##その他の情報

* FileMakerのCSVは、フィールド内の改行コードが垂直タブに変換されるという独自仕様です。特に対応策は施していませんが、フィルタ系の機能で str_replace("\x0b", "\r\n", $value) のように変換すれば一応対応できるはずです。
* str_getcsv()の第4引数（エスケープ文字）の正常動作を確認でき次第、そちらによる解析をデフォルト設定にするかも。

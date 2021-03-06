<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv;

/**
 * Writer
 *
 * @author k.holy74@gmail.com
 */
class Writer
{

    /**
     * @var \Volcanus\Csv\Configuration 設定値のコレクション
     */
    private $config;

    /**
     * @var \Volcanus\Csv\Configuration フィールド設定のコレクション
     */
    private $fields;

    /**
     * @var Configuration フィールド名のコレクション
     */
    private $labels;

    /**
     * @var \SplFileObject 出力対象ファイル
     */
    private $file;

    /**
     * @var \Volcanus\Csv\Builder CSVビルダ
     */
    private $builder;

    /**
     * @const int エンコーディング方法: プレーンShift-JIS
     */
    const PLAIN_SJIS = 1;

    /**
     * @const int エンコーディング方法: Percent-Encoding
     */
    const PERCENT_ENCODING = 2;

    /**
     * @const int エンコーディング方法: RFC 2047()
     */
    const RFC2047 = 3;

    /**
     * constructor
     *
     * @param array $configurations 設定オプション
     */
    public function __construct(array $configurations = [])
    {
        $this->initialize($configurations);
    }

    /**
     * オブジェクトを初期化します。
     *
     * @param array $configurations 設定オプション
     * @return $this
     */
    public function initialize(array $configurations = [])
    {
        $this->config = new Configuration([
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '"',
            'enclose' => false,
            'newLine' => "\r\n",
            'inputEncoding' => mb_internal_encoding(),
            'outputEncoding' => mb_internal_encoding(),
            'writeHeaderLine' => false,
            'responseFilename' => null,
            'responseFilenameEncoding' => null,
        ]);
        if (!empty($configurations)) {
            foreach ($configurations as $name => $value) {
                $this->config[$name] = $value;
            }
        }
        $this->fields = new Configuration();
        $this->labels = new Configuration();
        $this->file = null;
        $this->builder = new Builder();
        return $this;
    }

    /**
     * 引数1の場合は指定された設定の値を返します。
     * 引数2の場合は指定された設置の値をセットして$thisを返します。
     *
     * delimiter       : フィールドの区切り文字 ※1文字のみ対応
     * enclosure       : フィールドの囲み文字 ※1文字のみ対応
     * escape          : フィールドに含まれる囲み文字のエスケープ文字 ※1文字のみ対応
     * enclose         : 出力時に全てのフィールドに囲み文字を付与するかどうか
     * newLine         : 改行文字
     * inputEncoding   : 入力文字コード（データの文字コード）
     * outputEncoding  : 出力文字コード（CSVファイルの文字コード）
     * writeHeaderLine : ヘッダ行を出力するかどうか
     * responseFilename: レスポンス出力時のファイル名
     * responseFilenameEncoding: レスポンス出力時のファイル名のエンコード方法
     *                           PLAIN_SJIS | PERCENT_ENCODING | RFC2047
     *
     * @param string $name 設定名
     * @return mixed 設定値 または $this
     */
    public function config($name)
    {
        switch (func_num_args()) {
            case 1:
                return $this->config[$name];
            case 2:
                $value = func_get_arg(1);
                if (isset($value)) {
                    switch ($name) {
                        case 'delimiter':
                        case 'enclosure':
                        case 'escape':
                            if (!is_string($value)) {
                                throw new \InvalidArgumentException(
                                    sprintf('The config parameter "%s" only accepts string.', $name));
                            }
                            if (strlen($value) > 1) {
                                throw new \InvalidArgumentException(
                                    sprintf('The config parameter "%s" accepts one character.', $name));
                            }
                            break;
                        case 'enclose':
                        case 'writeHeaderLine':
                            if (is_int($value) || ctype_digit($value)) {
                                $value = (bool)$value;
                            }
                            if (!is_bool($value)) {
                                throw new \InvalidArgumentException(
                                    sprintf('The config parameter "%s" only accepts boolean.', $name));
                            }
                            break;
                        case 'newLine':
                        case 'inputEncoding':
                        case 'outputEncoding':
                        case 'responseFilename':
                            if (!is_string($value)) {
                                throw new \InvalidArgumentException(
                                    sprintf('The config parameter "%s" only accepts string.', $name));
                            }
                            break;
                        case 'responseFilenameEncoding':
                            if (!in_array($value, [
                                self::PLAIN_SJIS,
                                self::PERCENT_ENCODING,
                                self::RFC2047,
                            ])) {
                                throw new \InvalidArgumentException(
                                    sprintf('The config parameter "%s" invalid value "%s".', $name, $value));
                            }
                            break;
                    }
                    $this->config[$name] = $value;
                }
                return $this;
        }
        throw new \InvalidArgumentException('Invalid argument count.');
    }

    /**
     * 1レコード分のフィールド配列をCSV形式の文字列に変換し、
     * 文字コードの変換および改行を付与して返します。
     *
     * @param array|\Traversable $fields 1レコード分のフィールド配列
     * @return string CSVの1レコード分の文字列
     */
    public function build($fields)
    {

        $outputEncoding = $this->config->offsetGet('outputEncoding');
        $inputEncoding = $this->config->offsetGet('inputEncoding');

        $line = $this->builder->build($fields,
            $this->config->offsetGet('delimiter'),
            $this->config->offsetGet('enclosure'),
            $this->config->offsetGet('escape'),
            $this->config->offsetGet('enclose'),
            $inputEncoding
        );

        if (isset($outputEncoding)) {
            if (!isset($inputEncoding)) {
                $line = mb_convert_encoding($line, $outputEncoding, 'auto');
            } elseif (strcmp($outputEncoding, $inputEncoding) !== 0) {
                $line = mb_convert_encoding($line, $outputEncoding, $inputEncoding);
            }
        }

        return $line . $this->config->offsetGet('newLine');
    }

    /**
     * 引数1の場合は指定されたフィールドインデックスの名前を返します。
     * 引数2の場合は指定されたフィールドインデックスの名前をセットして$thisを返します。
     *
     * @param int $index フィールドインデックス
     * @param string (optional) $name フィールド名
     * @return mixed 設定値 または $this
     */
    public function label($index /* [,$name] */)
    {
        switch (func_num_args()) {
            case 1:
                return $this->labels->offsetGet($index);
            case 2:
                $name = func_get_arg(1);
                if (!is_string($name)) {
                    throw new \InvalidArgumentException(
                        sprintf('The label for index:%d is not a string.', $index));
                }
                $this->labels->define($index, $name);
                return $this;
        }
        throw new \InvalidArgumentException('Invalid argument count.');
    }

    /**
     * フィールドのヘッダ行をCSV形式の文字列で返します。
     *
     * @return string
     */
    public function buildHeaderLine()
    {
        return $this->build($this->labels->getIterator());
    }

    /**
     * CSVのフィールドを設定します。
     *
     * @param int $index フィールドインデックス
     * @param string|callable $filter フィールドの列名 または 値を生成するコールバック
     * @param string $name フィールド名
     * @return $this
     */
    public function field($index, $filter = null, $name = null)
    {

        if (!isset($filter)) {
            $filter = (string)$index;
        }

        if (!is_string($filter) && !is_callable($filter)) {
            throw new \InvalidArgumentException(
                sprintf('The filter for index:%d accepts string or callable.', $index));
        }

        $this->fields->define($index, $filter);

        if (isset($name)) {
            $this->label($index, $name);
        }

        return $this;
    }

    /**
     * フィールドの設定を元に1レコード分の配列をフィールド配列に変換して返します。
     *
     * @param array|object $record 1レコード分の配列
     * @return array 1レコード分の配列
     */
    public function buildFields($record)
    {

        if (!is_array($record) && !is_object($record)) {
            throw new \InvalidArgumentException(
                sprintf('The record accepts an array or object. invalid type:"%s"', gettype($record)));
        }

        $fields = [];
        foreach ($this->fields as $filter) {
            $fields[] = (is_string($filter))
                ? $this->getField($record, $filter)
                : $filter($record);
        }
        return $fields;
    }

    private function getField($record, $filter)
    {
        $arrayAccess = (is_array($record) || $record instanceof \ArrayAccess);
        // プロパティを4段階まで辿る ※例: entry.prefecture.branch.barnchName
        $props = explode('.', $filter);
        if (isset($props[1])) {
            if (isset($props[2])) {
                if (isset($props[3])) {
                    if ($arrayAccess && isset($record[$props[0]][$props[1]][$props[2]][$props[3]])) {
                        return $record[$props[0]][$props[1]][$props[2]][$props[3]];
                    } elseif (isset($record->{$props[0]}->{$props[1]}->{$props[2]}->{$props[3]})) {
                        return $record->{$props[0]}->{$props[1]}->{$props[2]}->{$props[3]};
                    }
                } elseif ($arrayAccess && isset($record[$props[0]][$props[1]][$props[2]])) {
                    return $record[$props[0]][$props[1]][$props[2]];
                } elseif (isset($record->{$props[0]}->{$props[1]}->{$props[2]})) {
                    return $record->{$props[0]}->{$props[1]}->{$props[2]};
                }
            } elseif ($arrayAccess && isset($record[$props[0]][$props[1]])) {
                return $record[$props[0]][$props[1]];
            } elseif (isset($record->{$props[0]}->{$props[1]})) {
                return $record->{$props[0]}->{$props[1]};
            }
        } elseif ($arrayAccess && isset($record[$props[0]])) {
            return $record[$props[0]];
        } elseif (isset($record->{$props[0]})) {
            return $record->{$props[0]};
        }
        return null;
    }

    /**
     * CSVのフィールドを配列から設定します。
     *
     * @param array $fields フィールド設定の配列
     * @return $this
     */
    public function fields($fields)
    {

        if (!is_array($fields) && !($fields instanceof \Traversable)) {
            throw new \InvalidArgumentException(
                sprintf('Fields accepts an array or Traversable. invalid type:"%s"', gettype($fields)));
        }

        foreach ($fields as $index => $field) {
            if (is_string($field)) {
                $this->field($index, $field);
            } else {
                if (is_array($field)) {
                    $this->field($index,
                        (isset($field[0])) ? $field[0] : null,
                        (isset($field[1])) ? $field[1] : null
                    );
                } else {
                    throw new \InvalidArgumentException(
                        sprintf('Field accepts a string or an array. invalid type:"%s"', gettype($field)));
                }
            }
        }

        return $this;
    }

    /**
     * 1レコード分の配列をCSV文字列に変換して返します。
     *
     * @param array|object $record 1レコード分の配列
     * @return string 1レコード分のCSV文字列
     */
    public function buildContentLine($record)
    {
        return $this->build($this->buildFields($record));
    }

    /**
     * ファイルオブジェクトをセットします。
     *
     * @param \SplFileObject $file
     * @return $this
     */
    public function setFile(\SplFileObject $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * ファイルオブジェクトを返します。
     *
     * @return \SplFileObject
     */
    public function getFile()
    {
        if (!isset($this->file)) {
            throw new \RuntimeException('File is not set.');
        }
        return $this->file;
    }

    /**
     * データをCSVに変換してファイルに出力します。
     *
     * @param array|\Traversable $records レコード
     * @return $this
     */
    public function write($records)
    {

        if (!isset($this->file)) {
            throw new \RuntimeException('File is not set.');
        }

        if (!is_array($records) && !($records instanceof \Traversable)) {
            throw new \InvalidArgumentException(
                sprintf('Records accepts an array or Traversable. invalid type:"%s"', gettype($records)));
        }

        if ($this->config->offsetGet('writeHeaderLine')) {
            $this->file->fwrite($this->buildHeaderLine());
        }

        foreach ($records as $record) {
            $this->file->fwrite($this->buildContentLine($record));
        }
        return $this;
    }

    /**
     * ファイルに書き込まれたCSVデータの内容を出力します。
     */
    public function flush()
    {
        if (!isset($this->file)) {
            throw new \RuntimeException('File is not set.');
        }
        $this->file->rewind();
        $this->file->fpassthru();
        $this->file->rewind();
    }

    /**
     * ファイルに書き込まれたCSVデータの内容を返します。
     *
     * @return string CSVデータの内容
     */
    public function content()
    {
        ob_start();
        $this->flush();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * ファイルに書き込まれたCSVデータのバイト数を返します。
     *
     * @return int CSVデータのバイト数
     */
    public function contentLength()
    {
        if (!isset($this->file)) {
            throw new \RuntimeException('File is not set.');
        }
        $status = $this->file->fstat();
        return $status['size'];
    }

    /**
     * レスポンスヘッダの生成を行います。
     *
     * @param array $headers レスポンスヘッダの配列
     * @return array レスポンスヘッダの配列
     */
    public function buildResponseHeaders(array $headers = [])
    {

        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/octet-stream';
        }

        if (!isset($headers['Content-Disposition'])) {
            $headers['Content-Disposition'] = 'attachment';
        }

        $filename = $this->config->offsetGet('responseFilename');
        $filenameEncoding = $this->config->offsetGet('responseFilenameEncoding');
        if (isset($filename)) {
            if (!isset($filenameEncoding)) {
                $headers['Content-Disposition'] .= sprintf('; filename="%s"', $filename);
            } else {
                switch ($filenameEncoding) {
                    case self::PLAIN_SJIS:
                        $headers['Content-Disposition'] .= sprintf('; filename="%s"', mb_convert_encoding($filename, 'SJIS-win'));
                        break;
                    case self::PERCENT_ENCODING:
                        $headers['Content-Disposition'] .= sprintf('; filename=%s', rawurlencode($filename));
                        break;
                    case self::RFC2047:
                        $headers['Content-Disposition'] .= sprintf('; filename="=?UTF-8?B?%s?="', base64_encode($filename));
                        break;
                }
                $headers['Content-Disposition'] .= sprintf("; filename*=utf-8''%s", rawurlencode($filename));
            }
        }

        $headers['Content-Length'] = $this->contentLength();

        return $headers;
    }

    /**
     * レスポンスを送信します。
     *
     * @param array $headers レスポンスヘッダの配列
     */
    public function send(array $headers = [])
    {
        $headers = $this->buildResponseHeaders($headers);
        foreach ($headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }
        $this->flush();
    }

    /**
     * magic setter
     *
     * @param string $name 設定名
     * @param mixed $value 設定値
     * @return mixed
     */
    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . ucfirst($name))) {
            return $this->{'set' . ucfirst($name)}($value);
        }
        $this->config($name, $value);
        return $this;
    }

    /**
     * magic getter
     *
     * @param string $name 設定名
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return $this->{'get' . ucfirst($name)}();
        }
        return $this->config($name);
    }

}

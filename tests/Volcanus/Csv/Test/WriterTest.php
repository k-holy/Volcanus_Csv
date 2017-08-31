<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv\Test;

use Volcanus\Csv\Writer;

/**
 * WriterTest
 *
 * @author k.holy74@gmail.com
 */
class WriterTest extends \PHPUnit\Framework\TestCase
{

    public function testDefaultConfigParameter()
    {
        $writer = new \Volcanus\Csv\Writer();
        $this->assertEquals(',', $writer->delimiter);
        $this->assertEquals('"', $writer->enclosure);
        $this->assertEquals('"', $writer->escape);
        $this->assertFalse($writer->enclose);
        $this->assertEquals("\r\n", $writer->newLine);
        $this->assertEquals(mb_internal_encoding(), $writer->inputEncoding);
        $this->assertEquals(mb_internal_encoding(), $writer->outputEncoding);
        $this->assertFalse($writer->writeHeaderLine);
        $this->assertNull($writer->responseFilename);
    }

    public function testConstructWithConfigParameters()
    {
        $writer = new \Volcanus\Csv\Writer([
            'delimiter' => "\t",
            'enclosure' => "'",
            'escape' => '\\',
            'enclose' => true,
            'newLine' => "\n",
            'inputEncoding' => 'EUC-JP',
            'outputEncoding' => 'SJIS-win',
            'writeHeaderLine' => true,
            'responseFilename' => 'test.csv',
        ]);
        $this->assertEquals("\t", $writer->delimiter);
        $this->assertEquals("'", $writer->enclosure);
        $this->assertEquals('\\', $writer->escape);
        $this->assertTrue($writer->enclose);
        $this->assertEquals("\n", $writer->newLine);
        $this->assertEquals('EUC-JP', $writer->inputEncoding);
        $this->assertEquals('SJIS-win', $writer->outputEncoding);
        $this->assertTrue($writer->writeHeaderLine);
        $this->assertEquals('test.csv', $writer->responseFilename);
    }

    public function testSetConfig()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->config('delimiter', "\t");
        $this->assertEquals("\t", $writer->delimiter);
    }

    public function testGetConfig()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->delimiter = "\t";
        $this->assertEquals("\t", $writer->config('delimiter'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->config('NOT-DEFINED-CONFIG', true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetConfigRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->config('NOT-DEFINED-CONFIG');
    }

    public function testSetDelimiter()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->delimiter = "\t";
        $this->assertEquals("\t", $writer->delimiter);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDelimiterRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->delimiter = [];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDelimiterRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->delimiter = ',,';
    }

    public function testSetEnclosure()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->enclosure = "'";
        $this->assertEquals("'", $writer->enclosure);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEnclosureRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->enclosure = [];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEnclosureRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->enclosure = '""';
    }

    public function testSetEscape()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->escape = '\\';
        $this->assertEquals('\\', $writer->escape);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEscapeRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->escape = [];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEscapeRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->escape = '\\\\';
    }

    public function testSetEnclose()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->enclose = true;
        $this->assertTrue($writer->enclose);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEncloseRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->enclose = 'true';
    }

    public function testSetNewLine()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->newLine = "\n";
        $this->assertEquals("\n", $writer->newLine);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNewLineRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->newLine = [];
    }

    public function testSetInputEncoding()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->inputEncoding = 'EUC-JP';
        $this->assertEquals('EUC-JP', $writer->inputEncoding);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInputEncodingRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->inputEncoding = [];
    }

    public function testSetOutputEncoding()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->outputEncoding = 'SJIS-win';
        $this->assertEquals('SJIS-win', $writer->outputEncoding);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetOutputEncodingRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->outputEncoding = [];
    }

    public function testSetWriteHeaderLine()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->writeHeaderLine = true;
        $this->assertTrue($writer->writeHeaderLine);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetWriteHeaderLineRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->writeHeaderLine = 'true';
    }

    public function testSetResponseFilename()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->responseFilename = 'test.csv';
        $this->assertEquals('test.csv', $writer->responseFilename);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetResponseFilenameRaiseInvalidArgumentException()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->responseFilename = [];
    }

    public function testBuild()
    {
        $writer = new \Volcanus\Csv\Writer();
        $this->assertEquals("1,田中\r\n",
            $writer->build(['1', '田中']));
    }

    public function testBuildIncludesDelimiter()
    {
        $writer = new \Volcanus\Csv\Writer();
        $this->assertEquals("1,\"田中,\"\r\n",
            $writer->build(['1', '田中,']));
    }

    public function testBuildWithEncoding()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->inputEncoding = 'UTF-8';
        $writer->outputEncoding = 'SJIS';
        $fields = ['1', 'ソ十貼能表暴予'];
        $this->assertEquals(
            mb_convert_encoding("1,ソ十貼能表暴予\r\n", 'SJIS', 'UTF-8'),
            $writer->build($fields)
        );
    }

    public function testBuildWithEnclose()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->enclose = true;
        $this->assertEquals("\"1\",\"田中\"\r\n",
            $writer->build(['1', '田中']));
    }

    public function testLabel()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->label(0, 'ユーザーID');
        $writer->label(1, 'ユーザー名');
        $this->assertEquals('ユーザーID', $writer->label(0));
        $this->assertEquals('ユーザー名', $writer->label(1));
    }

    public function testBuildHeaderLine()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->label(0, 'ユーザーID');
        $writer->label(1, 'ユーザー名');
        $this->assertEquals("ユーザーID,ユーザー名\r\n", $writer->buildHeaderLine());
    }

    public function testFieldAndBuildFields()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->field(0, 'surname');
        $writer->field(1, 'firstname');
        $writer->field(2, 'age');
        $this->assertEquals(['田中', '一郎', '22'],
            $writer->buildFields([
                'id' => '1',
                'surname' => '田中',
                'firstname' => '一郎',
                'age' => '22',
            ])
        );
        $this->assertEquals(['山田', '花子', null],
            $writer->buildFields([
                'id' => '2',
                'surname' => '山田',
                'firstname' => '花子',
            ])
        );
    }

    public function testFieldAndBuildFieldsByObject()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->field(0, 'surname');
        $writer->field(1, 'firstname');
        $writer->field(2, 'age');

        $user = new \stdClass();
        $user->id = 1;
        $user->surname = '田中';
        $user->firstname = '一郎';
        $user->age = 22;
        $this->assertEquals(['田中', '一郎', '22'],
            $writer->buildFields($user)
        );

        $user = new \stdClass();
        $user->id = 2;
        $user->surname = '山田';
        $user->firstname = '花子';
        $this->assertEquals(['山田', '花子', null],
            $writer->buildFields($user)
        );
    }

    public function testFieldAndBuildContentLine()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->field(0, 'surname');
        $writer->field(1, 'firstname');
        $writer->field(2, 'age');
        $this->assertEquals("田中,一郎,22\r\n",
            $writer->buildContentLine([
                'id' => '1',
                'surname' => '田中',
                'firstname' => '一郎',
                'age' => '22',
            ])
        );
        $this->assertEquals("山田,花子,\r\n",
            $writer->buildContentLine([
                'id' => '2',
                'surname' => '山田',
                'firstname' => '花子',
            ])
        );
    }

    public function testFieldWithLabel()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->field(0, 'surname', '姓');
        $writer->field(1, 'firstname', '名');
        $writer->field(2, 'age', '年齢');
        $this->assertEquals('姓', $writer->label(0));
        $this->assertEquals('名', $writer->label(1));
        $this->assertEquals('年齢', $writer->label(2));
    }

    public function testFieldsWithLabel()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->fields([
            ['surname', '姓'],
            ['firstname', '名'],
            ['age', '年齢'],
        ]);
        $this->assertEquals('姓', $writer->label(0));
        $this->assertEquals('名', $writer->label(1));
        $this->assertEquals('年齢', $writer->label(2));
    }

    public function testFieldsAndBuildFieldsWithCallback()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->fields([
            [
                function ($item) {
                    return $item['surname'] . $item['firstname'];
                },
                'ユーザー名',
            ],
            [
                function ($item) {
                    return (isset($item['age'])) ? $item['age'] : '不詳';
                },
                '年齢',
            ],
            ['prefecture.name', '出身地'],
        ]);
        $this->assertEquals(['田中一郎', '22', '兵庫県'],
            $writer->buildFields([
                'id' => '1',
                'surname' => '田中',
                'firstname' => '一郎',
                'age' => '22',
                'prefecture' => [
                    'code' => '28',
                    'name' => '兵庫県',
                ],
            ])
        );
        $this->assertEquals(['山田花子', '不詳', '大阪府'],
            $writer->buildFields([
                'id' => '2',
                'surname' => '山田',
                'firstname' => '花子',
                'prefecture' => [
                    'code' => '27',
                    'name' => '大阪府',
                ],
            ])
        );
    }

    public function testFieldsAndBuildFieldsByObjectWithCallback()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->fields([
            [
                function ($user) {
                    return $user->surname . $user->firstname;
                },
                'ユーザー名',
            ],
            [
                function ($user) {
                    return (isset($user->age)) ? $user->age : '不詳';
                },
                '年齢',
            ],
            ['prefecture.name', '出身地'],
        ]);

        $user = new \stdClass();
        $user->id = 1;
        $user->surname = '田中';
        $user->firstname = '一郎';
        $user->age = 22;
        $user->prefecture = new \stdClass();
        $user->prefecture->name = '兵庫県';
        $user->prefecture->code = '28';
        $this->assertEquals(['田中一郎', '22', '兵庫県'],
            $writer->buildFields($user)
        );

        $user = new \stdClass();
        $user->id = 2;
        $user->surname = '山田';
        $user->firstname = '花子';
        $user->prefecture = new \stdClass();
        $user->prefecture->name = '大阪府';
        $user->prefecture->code = '27';
        $this->assertEquals(['山田花子', '不詳', '大阪府'],
            $writer->buildFields($user)
        );
    }

    public function testSetFile()
    {
        $writer = new \Volcanus\Csv\Writer();
        $file = new \SplFileObject('php://memory', 'r+');
        $writer->setFile($file);
        $this->assertSame($file, $writer->getFile());
        $this->assertSame($file, $writer->file);
    }

    public function testWriteAndContent()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->field(0);
        $writer->field(1);
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        $this->assertEquals("1,田中\r\n", $writer->content());
    }

    public function testWriteAndFlush()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->field(0);
        $writer->field(1);
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        ob_start();
        $writer->flush();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals("1,田中\r\n", $content);
    }

    public function testWriteAndContentWithHeaderLine()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->label(0, 'ユーザーID');
        $writer->label(1, 'ユーザー名');
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->writeHeaderLine = true;
        $writer->write([
            ['1', '田中'],
        ]);
        $this->assertStringStartsWith("ユーザーID,ユーザー名\r\n", $writer->content());
    }

    public function testBuildResponseHeaders()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        $headers = $writer->buildResponseHeaders();
        $this->assertEquals($headers['Content-Type'], 'application/octet-stream');
        $this->assertEquals($headers['Content-Disposition'], 'attachment');
        $this->assertEquals($headers['Content-Length'], $writer->contentLength());
    }

    public function testBuildResponseHeadersWithResponseFilename()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        $writer->responseFilename = 'test.csv';
        $headers = $writer->buildResponseHeaders();
        $this->assertEquals($headers['Content-Type'], 'application/octet-stream');
        $this->assertEquals($headers['Content-Disposition'], 'attachment; filename="test.csv"');
        $this->assertEquals($headers['Content-Length'], $writer->contentLength());
    }

    public function testBuildResponseHeadersWithMultibyteResponseFilenameAndResponseFilenameEncodingSjisPlainAndRfc2231()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        $writer->responseFilename = 'ソ十貼能表暴予.csv';
        $writer->responseFilenameEncoding = Writer::PLAIN_SJIS;
        $headers = $writer->buildResponseHeaders();
        $this->assertEquals($headers['Content-Type'], 'application/octet-stream');
        $this->assertEquals($headers['Content-Disposition'],
            sprintf('attachment; filename="%s"; filename*=utf-8\'\'%s',
                mb_convert_encoding('ソ十貼能表暴予.csv', 'SJIS-win'),
                rawurlencode('ソ十貼能表暴予.csv')
            ));
        $this->assertEquals($headers['Content-Length'], $writer->contentLength());
    }

    public function testBuildResponseHeadersWithMultibyteResponseFilenameAndResponseFilenameEncodingPercentEncodingAndRfc2231()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        $writer->responseFilename = 'ソ十貼能表暴予.csv';
        $writer->responseFilenameEncoding = Writer::PERCENT_ENCODING;
        $headers = $writer->buildResponseHeaders();
        $this->assertEquals($headers['Content-Type'], 'application/octet-stream');
        $this->assertEquals($headers['Content-Disposition'],
            sprintf('attachment; filename=%s; filename*=utf-8\'\'%s',
                rawurlencode('ソ十貼能表暴予.csv'),
                rawurlencode('ソ十貼能表暴予.csv')
            ));
        $this->assertEquals($headers['Content-Length'], $writer->contentLength());
    }

    public function testBuildResponseHeadersWithMultibyteResponseFilenameAndResponseFilenameEncodingRfc2047AndRfc2231()
    {
        $writer = new \Volcanus\Csv\Writer();
        $writer->file = new \SplFileObject('php://memory', '+r');
        $writer->write([
            ['1', '田中'],
        ]);
        $writer->responseFilename = 'ソ十貼能表暴予.csv';
        $writer->responseFilenameEncoding = Writer::RFC2047;
        $headers = $writer->buildResponseHeaders();
        $this->assertEquals($headers['Content-Type'], 'application/octet-stream');
        $this->assertEquals($headers['Content-Disposition'],
            sprintf('attachment; filename="=?UTF-8?B?%s?="; filename*=utf-8\'\'%s',
                base64_encode('ソ十貼能表暴予.csv'),
                rawurlencode('ソ十貼能表暴予.csv')
            ));
        $this->assertEquals($headers['Content-Length'], $writer->contentLength());
    }

}

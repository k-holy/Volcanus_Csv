<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Csv\Test;

use Volcanus\Csv\Reader;

/**
 * ReaderTest
 *
 * @author k.holy74@gmail.com
 */
class ReaderTest extends \PHPUnit\Framework\TestCase
{

    public function testDefaultConfigParameter()
    {
        $reader = new \Volcanus\Csv\Reader();
        $this->assertEquals(',', $reader->delimiter);
        $this->assertEquals('"', $reader->enclosure);
        $this->assertEquals('"', $reader->escape);
        $this->assertEquals(mb_internal_encoding(), $reader->inputEncoding);
        $this->assertEquals(mb_internal_encoding(), $reader->outputEncoding);
        $this->assertTrue($reader->parseByPcre);
    }

    public function testConstructWithConfigParameters()
    {
        $reader = new \Volcanus\Csv\Reader([
            'delimiter' => "\t",
            'enclosure' => "'",
            'escape' => '\\',
            'inputEncoding' => 'SJIS-win',
            'outputEncoding' => 'EUC-JP',
            'parseByPcre' => false,
        ]);
        $this->assertEquals("\t", $reader->delimiter);
        $this->assertEquals("'", $reader->enclosure);
        $this->assertEquals('\\', $reader->escape);
        $this->assertEquals('SJIS-win', $reader->inputEncoding);
        $this->assertEquals('EUC-JP', $reader->outputEncoding);
        $this->assertFalse($reader->parseByPcre);
    }


    public function testSetConfig()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->config('delimiter', "\t");
        $this->assertEquals("\t", $reader->delimiter);
    }

    public function testGetConfig()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = "\t";
        $this->assertEquals("\t", $reader->config('delimiter'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->config('NOT-DEFINED-CONFIG', true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetConfigRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->config('NOT-DEFINED-CONFIG');
    }

    public function testSetDelimiter()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = "\t";
        $this->assertEquals("\t", $reader->delimiter);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDelimiterRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = [];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDelimiterRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = ',,';
    }

    public function testSetEnclosure()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->enclosure = "'";
        $this->assertEquals("'", $reader->enclosure);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEnclosureRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->enclosure = [];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEnclosureRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->enclosure = '""';
    }

    public function testSetEscape()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->escape = '\\';
        $this->assertEquals('\\', $reader->escape);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEscapeRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->escape = [];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetEscapeRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->escape = '\\\\';
    }

    public function testSetInputEncoding()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->inputEncoding = 'SJIS-win';
        $this->assertEquals('SJIS-win', $reader->inputEncoding);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInputEncodingRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->inputEncoding = [];
    }

    public function testSetOutputEncoding()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->outputEncoding = 'EUC-JP';
        $this->assertEquals('EUC-JP', $reader->outputEncoding);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetOutputEncodingRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->outputEncoding = [];
    }

    public function testSetParseByPcre()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->parseByPcre = false;
        $this->assertFalse($reader->parseByPcre);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParseByPcreRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->parseByPcre = 'false';
    }

    public function testFilter()
    {
        $reader = new \Volcanus\Csv\Reader();

        $filter1 = function ($item) {
            $item[0] = sprintf('%02d', $item[0]);
            return $item;
        };

        $filter2 = function ($item) {
            $user = new \Stdclass();
            $user->id = $item[0];
            return $user;
        };

        $reader->filter(0, $filter1);
        $reader->filter(1, $filter2);

        $this->assertEquals($filter1, $reader->filter(0));
        $this->assertEquals($filter2, $reader->filter(1));
    }

    public function testAppendFilter()
    {
        $reader = new \Volcanus\Csv\Reader();

        $filter1 = function ($item) {
            $item[0] = sprintf('%02d', $item[0]);
            return $item;
        };

        $filter2 = function ($item) {
            $user = new \Stdclass();
            $user->id = $item[0];
            return $user;
        };

        $reader->appendFilter($filter1);
        $reader->appendFilter($filter2);

        $this->assertEquals($filter1, $reader->filter(0));
        $this->assertEquals($filter2, $reader->filter(1));
    }

    public function testApplyFilters()
    {
        $reader = new \Volcanus\Csv\Reader();

        $reader->filter(0, function ($item) {
            $user = new \Stdclass();
            $user->id = $item[0];
            $user->surname = $item[1];
            $user->firstname = $item[2];
            $user->age = (int)$item[3];
            return $user;
        });

        $user = $reader->applyFilters(['1', '田中', '一郎', '22']);

        $this->assertEquals('1', $user->id);
        $this->assertEquals('田中', $user->surname);
        $this->assertEquals('一郎', $user->firstname);
        $this->assertEquals(22, $user->age);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testApplyFiltersRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $user = $reader->applyFilters('This is not an array and not a traversable.');
    }

    public function testConvert()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['1', '田中'],
            $reader->convert("1,田中\r\n"));
    }

    public function testConvertIgnoreNullByte()
    {
        $reader = new \Volcanus\Csv\Reader();
        $this->assertEquals(['1', '田中'],
            $reader->convert("1,田中\x00"));
    }

    public function testConvertEncloseDelimiter()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['1', '田中,'],
            $reader->convert('1,"田中,"'));
    }

    public function testConvertEscapedEnclosure()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['1', '田中"'],
            $reader->convert('1,"田中"""'));
    }

    public function testConvertEnclosedCarriageReturnAndLineFeed()
    {
        $reader = new \Volcanus\Csv\Reader();
        $this->assertEquals(['1', "田中\r\n"],
            $reader->convert("1,\"田中\r\n\""));
    }

    public function testConvertOnlySpaceField()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals([' ', '1', '田中'],
            $reader->convert(' ,1,田中'));
    }

    public function testConvertNotClosedEnclosure()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['"', '1', '田中'],
            $reader->convert('",1,田中'));
    }

    public function testConvertNotClosedEnclosureByParseStrGetCsv()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->parseByPcre = false;
        $this->assertEquals([',1,田中'],
            $reader->convert('",1,田中'));

    }

    public function testConvertNotOpenedEnclosure()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['', '"', '1', '田中'],
            $reader->convert(',","1","田中"'));
    }

    public function testConvertNotOpenedEnclosureByParseStrGetCsv()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->parseByPcre = false;
        $this->assertEquals(['', ',1"', '田中'],
            $reader->convert(',","1","田中"'));
    }

    public function testConvertNotClosedEnclosureAndSpaceBeforeDelimiter()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['" ', '"1" ', '田中"'],
            $reader->convert('" ,"1" ,"田中""'));
    }

    public function testConvertNotClosedEnclosureAndSpaceBeforeDelimiterByParseStrGetCsv()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->parseByPcre = false;

        $this->assertEquals([' ,1" ', '田中"'],
            $reader->convert('" ,"1" ,"田中""'));
    }

    public function testConvertNotClosedEnclosureAndSpaceAfterDelimiter()
    {
        $reader = new \Volcanus\Csv\Reader();

        $this->assertEquals(['"', ' "1"', ' "田中"'],
            $reader->convert('", "1", "田中""'));
    }

    public function testConvertNotClosedEnclosureAndSpaceAfterDelimiterByParseStrGetCsv()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->parseByPcre = false;

        $this->assertEquals([', 1"', '田中"'],
            $reader->convert('", "1", "田中""'));
    }

    public function testConvertTabSeparatedValues()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = "\t";
        $reader->enclosure = '"';

        $this->assertEquals(['1', '田中'],
            $reader->convert("1\t\"田中\""));
    }

    public function testConvertTabSeparatedValuesAndEscapedEnclosure()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = "\t";
        $reader->enclosure = '"';
        $reader->escape = '\\';

        $this->assertEquals(['1', '田中"'],
            $reader->convert("1\t\"田中\\\"\""));
    }

    public function testConvertTabSeparatedValuesAndEscapedEnclosureByStrGetCsv()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->delimiter = "\t";
        $reader->enclosure = '"';
        $reader->escape = '\\';
        // str_getcsv()はescape対応NG (PHP5.4.8現在)
        $reader->parseByPcre = false;

        $this->assertNotEquals(['1', '田中"'],
            $reader->convert("1\t\"田中\\\"\""));
    }

    public function testConvertWithEncoding()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->inputEncoding = 'UTF-8';
        $reader->outputEncoding = 'SJIS';

        $fields = ['1', 'ソ十貼能表暴予'];
        mb_convert_variables('SJIS', 'UTF-8', $fields);

        $this->assertEquals($fields,
            $reader->convert('1,ソ十貼能表暴予'));
    }

    public function testSetFile()
    {
        $reader = new \Volcanus\Csv\Reader();
        $file = new \SplFileObject('php://temp', '+r');
        $reader->setFile($file);
        $this->assertSame($file, $reader->getFile());
        $this->assertSame($file, $reader->file);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetFileRaiseInvalidArgumentException()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->setFile([]);
    }

    public function testParse()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,田中,一郎,22\r\n");
        $reader->file->rewind();

        $this->assertEquals(['1', '田中', '一郎', '22'],
            $reader->parse());
    }

    public function testParseEnclosedCarriageReturnAndLineFeed()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,\"田\r\n中\",\"一\r\n郎\",22\r\n");
        $reader->file->rewind();

        $this->assertEquals(['1', "田\r\n中", "一\r\n郎", '22'],
            $reader->parse());
    }

    public function testFetchAll()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,田中\r\n");
        $reader->file->fwrite("2,山田\r\n");
        $reader->file->fwrite("3,鈴木\r\n");
        $reader->file->rewind();

        $results = $reader->fetchAll();
        $this->assertEquals(['1', '田中'], $results[0]);
        $this->assertEquals(['2', '山田'], $results[1]);
        $this->assertEquals(['3', '鈴木'], $results[2]);
    }

    public function testFetchAllWithFilter()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,田中,一郎,22\r\n");
        $reader->file->fwrite("2,山田,老人,91\r\n");
        $reader->file->fwrite("3,田中,次郎,45\r\n");
        $reader->file->fwrite("4,佐藤,ウメ,95\r\n");
        $reader->file->rewind();

        $reader->appendFilter(function ($item) {
            $user = new \Stdclass();
            $user->id = $item[0];
            $user->surname = $item[1];
            $user->firstname = $item[2];
            $user->age = (int)$item[3];
            return $user;
        });

        $results = $reader->fetchAll();

        // 1st record
        $user = $results[0];
        $this->assertEquals('1', $user->id);
        $this->assertEquals('田中', $user->surname);
        $this->assertEquals('一郎', $user->firstname);
        $this->assertEquals(22, $user->age);

        // 2nd record
        $user = $results[1];
        $this->assertEquals('2', $user->id);
        $this->assertEquals('山田', $user->surname);
        $this->assertEquals('老人', $user->firstname);
        $this->assertEquals(91, $user->age);

        // 3rd record
        $user = $results[2];
        $this->assertEquals('3', $user->id);
        $this->assertEquals('田中', $user->surname);
        $this->assertEquals('次郎', $user->firstname);
        $this->assertEquals(45, $user->age);

        // 4th record
        $user = $results[3];
        $this->assertEquals('4', $user->id);
        $this->assertEquals('佐藤', $user->surname);
        $this->assertEquals('ウメ', $user->firstname);
        $this->assertEquals(95, $user->age);
    }

    public function testFetchAllWithFilterAndEncoding()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,ソ十貼能表暴予\r\n");
        $reader->file->rewind();

        $reader->appendFilter(function ($item) {
            $test = new \Stdclass();
            $test->id = $item[0];
            $test->text = $item[1];
            return $test;
        });

        $reader->inputEncoding = 'UTF-8';
        $reader->outputEncoding = 'SJIS';

        $results = $reader->fetchAll();

        $test = $results[0];
        $this->assertEquals('1', $test->id);
        $this->assertEquals(mb_convert_encoding('ソ十貼能表暴予', 'SJIS', 'UTF-8'), $test->text);
    }

    public function testFetchAllWithSomeFilters()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,田中,一郎,22\r\n");
        $reader->file->fwrite("2,山田,老人,91\r\n");
        $reader->file->fwrite("3,田中,次郎,45\r\n");
        $reader->file->fwrite("4,佐藤,ウメ,95\r\n");
        $reader->file->rewind();

        $reader->appendFilter(function ($item) {
            $user = new \Stdclass();
            $user->id = $item[0];
            $user->surname = $item[1];
            $user->firstname = $item[2];
            $user->age = (int)$item[3];
            return $user;
        });

        // ageが90以上の老人のみ抽出
        $reader->appendFilter(function ($user) {
            if ($user->age > 90) {
                return $user;
            }
            return false;
        });

        $results = $reader->fetchAll();

        // 1st record
        $elder = $results[0];
        $this->assertEquals('2', $elder->id);
        $this->assertEquals('山田', $elder->surname);
        $this->assertEquals('老人', $elder->firstname);
        $this->assertGreaterThan(90, $elder->age);

        // 2nd record
        $elder = $results[1];
        $this->assertEquals('4', $elder->id);
        $this->assertEquals('佐藤', $elder->surname);
        $this->assertEquals('ウメ', $elder->firstname);
        $this->assertGreaterThan(90, $elder->age);

    }


    public function testFetchAllThenParsedAndFetched()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,田中\r\n");
        $reader->file->fwrite("2,山田\r\n");
        $reader->file->fwrite("3,鈴木\r\n");
        $reader->file->rewind();

        $this->assertEquals(0, $reader->parsed);
        $this->assertEquals(0, $reader->fetched);

        $reader->fetchAll();
        $this->assertEquals(3, $reader->parsed);
        $this->assertEquals(3, $reader->fetched);
    }

    public function testFetchAllThenParsedAndFetchedWithFilter()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("ユーザーID,ユーザー名\r\n");
        $reader->file->fwrite("1,田中\r\n");
        $reader->file->fwrite("2,山田\r\n");
        $reader->file->fwrite("3,鈴木\r\n");
        $reader->file->rewind();

        $reader->appendFilter(function ($item) use ($reader) {
            if ($reader->parsed === 1) {
                return false;
            }
            return $item;
        });

        $reader->fetchAll();
        $this->assertEquals(4, $reader->parsed);
        $this->assertEquals(3, $reader->fetched);
    }

    public function testRewind()
    {
        $reader = new \Volcanus\Csv\Reader();
        $reader->file = new \SplFileObject('php://temp', '+r');
        $reader->file->fwrite("1,田中,一郎,22\r\n");
        $reader->file->rewind();

        $reader->fetchAll();
        $this->assertEquals(1, $reader->parsed);
        $this->assertEquals(1, $reader->fetched);

        $reader->rewind();
        $this->assertEquals(0, $reader->parsed);
        $this->assertEquals(0, $reader->fetched);
        $this->assertEquals(0, $reader->file->ftell());
    }

}

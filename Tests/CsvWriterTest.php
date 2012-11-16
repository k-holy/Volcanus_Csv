<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2012 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
namespace Volcanus\Csv\Tests;

use Volcanus\Csv\CsvWriter;

/**
 * CsvWriterTest
 *
 * @author k.holy74@gmail.com
 */
class CsvWriterTest extends \PHPUnit_Framework_TestCase
{

	public function testDefaultConfigParameter()
	{
		$writer = new CsvWriter();
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
		$writer = new CsvWriter(array(
			'delimiter'       => "\t",
			'enclosure'       => "'",
			'escape'          => '\\',
			'enclose'         => true,
			'newLine'         => "\n",
			'inputEncoding'   => 'EUC-JP',
			'outputEncoding'  => 'SJIS-win',
			'writeHeaderLine' => true,
			'responseFilename' => 'test.csv',
		));
		$this->assertEquals("\t", $writer->delimiter);
		$this->assertEquals("'" , $writer->enclosure);
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
		$writer = new CsvWriter();
		$writer->config('delimiter', "\t");
		$this->assertEquals("\t", $writer->delimiter);
	}

	public function testGetConfig()
	{
		$writer = new CsvWriter();
		$writer->delimiter = "\t";
		$this->assertEquals("\t", $writer->config('delimiter'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetConfigRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->config('NOT-DEFINED-CONFIG', true);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetConfigRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->config('NOT-DEFINED-CONFIG');
	}

	public function testSetDelimiter()
	{
		$writer = new CsvWriter();
		$writer->delimiter = "\t";
		$this->assertEquals("\t", $writer->delimiter);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new CsvWriter();
		$writer->delimiter = ',,';
	}

	public function testSetEnclosure()
	{
		$writer = new CsvWriter();
		$writer->enclosure = "'";
		$this->assertEquals("'", $writer->enclosure);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new CsvWriter();
		$writer->delimiter = '""';
	}

	public function testSetEscape()
	{
		$writer = new CsvWriter();
		$writer->escape = '\\';
		$this->assertEquals('\\', $writer->escape);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->escape = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new CsvWriter();
		$writer->escape = '\\\\';
	}

	public function testSetEnclose()
	{
		$writer = new CsvWriter();
		$writer->enclose = true;
		$this->assertTrue($writer->enclose);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEncloseRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->enclose = 'true';
	}

	public function testSetNewLine()
	{
		$writer = new CsvWriter();
		$writer->newLine = "\n";
		$this->assertEquals("\n", $writer->newLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetNewLineRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->newLine = array();
	}

	public function testSetInputEncoding()
	{
		$writer = new CsvWriter();
		$writer->inputEncoding = 'EUC-JP';
 		$this->assertEquals('EUC-JP', $writer->inputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetInputEncodingRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->inputEncoding = array();
	}

	public function testSetOutputEncoding()
	{
		$writer = new CsvWriter();
		$writer->outputEncoding = 'SJIS-win';
 		$this->assertEquals('SJIS-win', $writer->outputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetOutputEncodingRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->outputEncoding = array();
	}

	public function testSetWriteHeaderLine()
	{
		$writer = new CsvWriter();
		$writer->writeHeaderLine = true;
 		$this->assertTrue($writer->writeHeaderLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetWriteHeaderLineRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->writeHeaderLine = 'true';
	}

	public function testSetContentFilename()
	{
		$writer = new CsvWriter();
		$writer->responseFilename = 'test.csv';
 		$this->assertEquals('test.csv', $writer->responseFilename);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetContentFilenameRaiseInvalidArgumentException()
	{
		$writer = new CsvWriter();
		$writer->responseFilename = array();
	}

	public function testBuildField()
	{
		$writer = new CsvWriter();
		$field = '田中';
		$this->assertEquals("田中", $writer->buildField($field));
	}

	public function testBuildFieldIncludesDelimiter()
	{
		$writer = new CsvWriter();
		$field = '田中,';
		$this->assertEquals("\"田中,\"", $writer->buildField($field));
	}

	public function testBuildFieldIncludesCarriageReturnAndLineFeed()
	{
		$writer = new CsvWriter();
		$field = "田中\r\n\r\n以上";
		$this->assertEquals("\"田中\r\n\r\n以上\"", $writer->buildField($field));
	}

	public function testBuildFieldIncludesCarriageReturn()
	{
		$writer = new CsvWriter();
		$field = "田中\r\r以上";
		$this->assertEquals("\"田中\r\r以上\"", $writer->buildField($field));
	}

	public function testBuildFieldIncludesLineFeed()
	{
		$writer = new CsvWriter();
		$field = "田中\n\n以上";
		$this->assertEquals("\"田中\n\n以上\"", $writer->buildField($field));
	}

	public function testBuildFieldEscapeIncludesEnclosure()
	{
		$writer = new CsvWriter();
		$field = '田中"';
		$this->assertEquals('"田中"""', $writer->buildField($field));

		$writer->enclosure = '"';
		$writer->escape = '\\';
		$this->assertEquals('"田中\""', $writer->buildField($field));
	}

	public function testBuildFieldEscapeIncludesRepetitionOfEnclosure()
	{
		$writer = new CsvWriter();
		$field = '"田"中""';
		$this->assertEquals('"""田""中"""""', $writer->buildField($field));

		$writer->enclosure = '"';
		$writer->escape = '\\';
		$this->assertEquals('"\"田\"中\"\""', $writer->buildField($field));
	}

	public function testBuildFieldWithConvertEncoding()
	{
		$writer = new CsvWriter();
		$writer->inputEncoding = 'UTF-8';
		$writer->outputEncoding = 'SJIS';
		$field = 'ソ十貼能表暴予';
		$this->assertEquals(mb_convert_encoding($field, 'SJIS', 'UTF-8'), $writer->buildField($field));
	}

	public function testBuildLine()
	{
		$writer = new CsvWriter();
		$this->assertEquals("1,田中\r\n",
			$writer->buildLine(array('1', '田中')));
	}

	public function testBuildLineIncludesDelimiter()
	{
		$writer = new CsvWriter();
		$this->assertEquals("1,\"田中,\"\r\n",
			$writer->buildLine(array('1', '田中,')));
	}

	public function testBuildLineWithConvertEncoding()
	{
		$writer = new CsvWriter();
		$writer->inputEncoding = 'UTF-8';
		$writer->outputEncoding = 'SJIS';
		$fields = array('1', 'ソ十貼能表暴予');
		$this->assertEquals(mb_convert_encoding("1,ソ十貼能表暴予\r\n", 'SJIS', 'UTF-8'), $writer->buildLine($fields));
	}

	public function testBuildLineWithEnclose()
	{
		$writer = new CsvWriter();
		$writer->enclose = true;
		$this->assertEquals("\"1\",\"田中\"\r\n",
			$writer->buildLine(array('1', '田中')));
	}

	public function testFieldName()
	{
		$writer = new CsvWriter();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$this->assertEquals('ユーザーID', $writer->fieldName(0));
		$this->assertEquals('ユーザー名', $writer->fieldName(1));
	}

	public function testBuildHeaderLine()
	{
		$writer = new CsvWriter();
		$writer->field(0, 'ユーザーID');
		$writer->field(1, 'ユーザー名');
		$this->assertEquals("ユーザーID,ユーザー名\r\n", $writer->buildHeaderLine());
	}

	public function testFieldFilter()
	{
		$writer = new CsvWriter();
		$idFilter = function($item) {
			return $item['id'];
		};
		$nameFilter = function($item) {
			return $item['surname'] . $item['firstname'];
		};
		$writer->fieldFilter(0, $idFilter);
		$writer->fieldFilter(1, $nameFilter);
		$this->assertEquals($idFilter  , $writer->fieldFilter(0));
		$this->assertEquals($nameFilter, $writer->fieldFilter(1));
	}

	public function testBuildFields()
	{
		$writer = new CsvWriter();
		$writer->field(0, 'ユーザー名', function($item) {
			return $item['surname'] . $item['firstname'];
		});
		$writer->field(1, '年齢', function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		});
		$this->assertEquals(array('田中一郎', '22'),
			$writer->buildFields(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
			))
		);
		$this->assertEquals(array('山田花子', '不詳'),
			$writer->buildFields(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
			))
		);
	}

	public function testBuildContentLine()
	{
		$writer = new CsvWriter();
		$writer->field(0, 'ユーザー名', function($item) {
			return $item['surname'] . $item['firstname'];
		});
		$writer->field(1, '年齢', function($item) {
			return (isset($item['age'])) ? $item['age'] : '不詳';
		});
		$this->assertEquals("田中一郎,22\r\n",
			$writer->buildContentLine(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
		)));
		$this->assertEquals("山田花子,不詳\r\n",
			$writer->buildContentLine(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
		)));
	}

	public function testOpen()
	{
		$writer = new CsvWriter();
		$this->assertInstanceOf('\SplFileObject', $writer->open('php://memory'));
	}

	public function testClose()
	{
		$writer = new CsvWriter();
		$writer->open('php://memory');
		$this->assertInstanceOf('\SplFileObject', $writer->close());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCloseRaiseRuntimeExceptionWhenFileIsNotOpen()
	{
		$writer = new CsvWriter();
		$writer->close();
	}

	public function testWrite()
	{
		$writer = new CsvWriter();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertGreaterThan(1, $writer->contentLength());
	}

	public function testWriteAndContent()
	{
		$writer = new CsvWriter();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertEquals("1,田中\r\n", $writer->content());
	}

	public function testWriteAndContentWithHeaderLine()
	{
		$writer = new CsvWriter();
		$writer->writeHeaderLine = true;
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertStringStartsWith("ユーザーID,ユーザー名\r\n", $writer->content());
	}

	public function testWriteAndContentLength()
	{
		$writer = new CsvWriter();
		$writer->fieldName(0, 'ユーザーID');
		$writer->fieldName(1, 'ユーザー名');
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertEquals(strlen("1,田中\r\n"), $writer->contentLength());
	}

	public function testBuildResponseHeaders()
	{
		$writer = new CsvWriter();
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], 'application/octet-stream');
		$this->assertEquals($headers['Content-Disposition'], 'attachement');
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

	public function testBuildResponseHeadersWithResponseFilename()
	{
		$writer = new CsvWriter();
		$writer->responseFilename = 'test.csv';
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], 'application/octet-stream; name="test.csv"');
		$this->assertEquals($headers['Content-Disposition'], 'attachement; filename="test.csv"');
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

	public function testBuildResponseHeadersWithMultibyteResponseFilename()
	{
		$writer = new CsvWriter();
		$writer->responseFilename = 'ソ十貼能表暴予.csv';
		$writer->open('php://temp');
		$writer->write(array(
			array('1', '田中'),
		));
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], sprintf('application/octet-stream; name="%s"', mb_convert_encoding('ソ十貼能表暴予.csv', 'SJIS-win')));
		$this->assertEquals($headers['Content-Disposition'], sprintf('attachement; filename="%s"', mb_convert_encoding('ソ十貼能表暴予.csv', 'SJIS-win')));
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

}

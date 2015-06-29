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
class WriterTest extends \PHPUnit_Framework_TestCase
{

	public function testDefaultConfigParameter()
	{
		$writer = new Writer();
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
		$writer = new Writer(array(
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
		$writer = new Writer();
		$writer->config('delimiter', "\t");
		$this->assertEquals("\t", $writer->delimiter);
	}

	public function testGetConfig()
	{
		$writer = new Writer();
		$writer->delimiter = "\t";
		$this->assertEquals("\t", $writer->config('delimiter'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetConfigRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->config('NOT-DEFINED-CONFIG', true);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetConfigRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->config('NOT-DEFINED-CONFIG');
	}

	public function testSetDelimiter()
	{
		$writer = new Writer();
		$writer->delimiter = "\t";
		$this->assertEquals("\t", $writer->delimiter);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->delimiter = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetDelimiterRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new Writer();
		$writer->delimiter = ',,';
	}

	public function testSetEnclosure()
	{
		$writer = new Writer();
		$writer->enclosure = "'";
		$this->assertEquals("'", $writer->enclosure);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->enclosure = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEnclosureRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new Writer();
		$writer->enclosure = '""';
	}

	public function testSetEscape()
	{
		$writer = new Writer();
		$writer->escape = '\\';
		$this->assertEquals('\\', $writer->escape);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->escape = array();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEscapeRaiseInvalidArgumentExceptionWhenTwoOrMoreCharactersAreSpecified()
	{
		$writer = new Writer();
		$writer->escape = '\\\\';
	}

	public function testSetEnclose()
	{
		$writer = new Writer();
		$writer->enclose = true;
		$this->assertTrue($writer->enclose);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetEncloseRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->enclose = 'true';
	}

	public function testSetNewLine()
	{
		$writer = new Writer();
		$writer->newLine = "\n";
		$this->assertEquals("\n", $writer->newLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetNewLineRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->newLine = array();
	}

	public function testSetInputEncoding()
	{
		$writer = new Writer();
		$writer->inputEncoding = 'EUC-JP';
 		$this->assertEquals('EUC-JP', $writer->inputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetInputEncodingRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->inputEncoding = array();
	}

	public function testSetOutputEncoding()
	{
		$writer = new Writer();
		$writer->outputEncoding = 'SJIS-win';
 		$this->assertEquals('SJIS-win', $writer->outputEncoding);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetOutputEncodingRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->outputEncoding = array();
	}

	public function testSetWriteHeaderLine()
	{
		$writer = new Writer();
		$writer->writeHeaderLine = true;
 		$this->assertTrue($writer->writeHeaderLine);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetWriteHeaderLineRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->writeHeaderLine = 'true';
	}

	public function testSetResponseFilename()
	{
		$writer = new Writer();
		$writer->responseFilename = 'test.csv';
 		$this->assertEquals('test.csv', $writer->responseFilename);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetResponseFilenameRaiseInvalidArgumentException()
	{
		$writer = new Writer();
		$writer->responseFilename = array();
	}

	public function testBuild()
	{
		$writer = new Writer();
		$this->assertEquals("1,田中\r\n",
			$writer->build(array('1', '田中')));
	}

	public function testBuildIncludesDelimiter()
	{
		$writer = new Writer();
		$this->assertEquals("1,\"田中,\"\r\n",
			$writer->build(array('1', '田中,')));
	}

	public function testBuildWithEncoding()
	{
		$writer = new Writer();
		$writer->inputEncoding = 'UTF-8';
		$writer->outputEncoding = 'SJIS';
		$fields = array('1', 'ソ十貼能表暴予');
		$this->assertEquals(
			mb_convert_encoding("1,ソ十貼能表暴予\r\n", 'SJIS', 'UTF-8'),
			$writer->build($fields)
		);
	}

	public function testBuildWithEnclose()
	{
		$writer = new Writer();
		$writer->enclose = true;
		$this->assertEquals("\"1\",\"田中\"\r\n",
			$writer->build(array('1', '田中')));
	}

	public function testLabel()
	{
		$writer = new Writer();
		$writer->label(0, 'ユーザーID');
		$writer->label(1, 'ユーザー名');
		$this->assertEquals('ユーザーID', $writer->label(0));
		$this->assertEquals('ユーザー名', $writer->label(1));
	}

	public function testBuildHeaderLine()
	{
		$writer = new Writer();
		$writer->label(0, 'ユーザーID');
		$writer->label(1, 'ユーザー名');
		$this->assertEquals("ユーザーID,ユーザー名\r\n", $writer->buildHeaderLine());
	}

	public function testFieldAndBuildFields()
	{
		$writer = new Writer();
		$writer->field(0, 'surname');
		$writer->field(1, 'firstname');
		$writer->field(2, 'age');
		$this->assertEquals(array('田中', '一郎', '22'),
			$writer->buildFields(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
			))
		);
		$this->assertEquals(array('山田', '花子', null),
			$writer->buildFields(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
			))
		);
	}

	public function testFieldAndBuildFieldsByObject()
	{
		$writer = new Writer();
		$writer->field(0, 'surname');
		$writer->field(1, 'firstname');
		$writer->field(2, 'age');

		$user = new \stdClass();
		$user->id = 1;
		$user->surname = '田中';
		$user->firstname = '一郎';
		$user->age = 22;
		$this->assertEquals(array('田中', '一郎', '22'),
			$writer->buildFields($user)
		);

		$user = new \stdClass();
		$user->id = 2;
		$user->surname = '山田';
		$user->firstname = '花子';
		$this->assertEquals(array('山田', '花子', null),
			$writer->buildFields($user)
		);
	}

	public function testFieldAndBuildContentLine()
	{
		$writer = new Writer();
		$writer->field(0, 'surname');
		$writer->field(1, 'firstname');
		$writer->field(2, 'age');
		$this->assertEquals("田中,一郎,22\r\n",
			$writer->buildContentLine(array(
				'id'        => '1',
				'surname'   => '田中',
				'firstname' => '一郎',
				'age'       => '22',
			))
		);
		$this->assertEquals("山田,花子,\r\n",
			$writer->buildContentLine(array(
				'id'        => '2',
				'surname'   => '山田',
				'firstname' => '花子',
			))
		);
	}

	public function testFieldWithLabel()
	{
		$writer = new Writer();
		$writer->field(0, 'surname'  , '姓');
		$writer->field(1, 'firstname', '名');
		$writer->field(2, 'age'      , '年齢');
		$this->assertEquals('姓'  , $writer->label(0));
		$this->assertEquals('名'  , $writer->label(1));
		$this->assertEquals('年齢', $writer->label(2));
	}

	public function testFieldsWithLabel()
	{
		$writer = new Writer();
		$writer->fields(array(
			array('surname'  , '姓'),
			array('firstname', '名'),
			array('age'      , '年齢'),
		));
		$this->assertEquals('姓'  , $writer->label(0));
		$this->assertEquals('名'  , $writer->label(1));
		$this->assertEquals('年齢', $writer->label(2));
	}

	public function testFieldsAndBuildFieldsWithCallback()
	{
		$writer = new Writer();
		$writer->fields(array(
			array(function($item) {
					return $item['surname'] . $item['firstname'];
				}, 'ユーザー名'
			),
			array(function($item) {
					return (isset($item['age'])) ? $item['age'] : '不詳';
				}, '年齢'
			)
		));
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

	public function testFieldsAndBuildFieldsByObjectWithCallback()
	{
		$writer = new Writer();
		$writer->fields(array(
			array(function($user) {
					return $user->surname . $user->firstname;
				}, 'ユーザー名'
			),
			array(function($user) {
					return (isset($user->age)) ? $user->age : '不詳';
				}, '年齢'
			)
		));

		$user = new \stdClass();
		$user->id = 1;
		$user->surname = '田中';
		$user->firstname = '一郎';
		$user->age = 22;
		$this->assertEquals(array('田中一郎', '22'),
			$writer->buildFields($user)
		);

		$user = new \stdClass();
		$user->id = 2;
		$user->surname = '山田';
		$user->firstname = '花子';
		$this->assertEquals(array('山田花子', '不詳'),
			$writer->buildFields($user)
		);
	}

	public function testSetFile()
	{
		$writer = new Writer();
		$file = new \SplFileObject('php://memory', 'r+');
		$writer->setFile($file);
		$this->assertSame($file, $writer->getFile());
		$this->assertSame($file, $writer->file);
	}

	public function testWriteAndContent()
	{
		$writer = new Writer();
		$writer->field(0);
		$writer->field(1);
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertEquals("1,田中\r\n", $writer->content());
	}

	public function testWriteAndFlush()
	{
		$writer = new Writer();
		$writer->field(0);
		$writer->field(1);
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
		ob_start();
		$writer->flush();
		$content = ob_get_contents();
		ob_end_clean();
		$this->assertEquals("1,田中\r\n", $content);
	}

	public function testWriteAndContentWithHeaderLine()
	{
		$writer = new Writer();
		$writer->label(0, 'ユーザーID');
		$writer->label(1, 'ユーザー名');
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->writeHeaderLine = true;
		$writer->write(array(
			array('1', '田中'),
		));
		$this->assertStringStartsWith("ユーザーID,ユーザー名\r\n", $writer->content());
	}

	public function testBuildResponseHeaders()
	{
		$writer = new Writer();
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], 'application/octet-stream');
		$this->assertEquals($headers['Content-Disposition'], 'attachment');
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

	public function testBuildResponseHeadersWithResponseFilename()
	{
		$writer = new Writer();
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
		$writer->responseFilename = 'test.csv';
		$headers = $writer->buildResponseHeaders();
		$this->assertEquals($headers['Content-Type'], 'application/octet-stream');
		$this->assertEquals($headers['Content-Disposition'], 'attachment; filename="test.csv"');
		$this->assertEquals($headers['Content-Length'], $writer->contentLength());
	}

	public function testBuildResponseHeadersWithMultibyteResponseFilenameAndResponseFilenameEncodingSjisPlainAndRfc2231()
	{
		$writer = new Writer();
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
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
		$writer = new Writer();
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
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
		$writer = new Writer();
		$writer->file = new \SplFileObject('php://memory', '+r');
		$writer->write(array(
			array('1', '田中'),
		));
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

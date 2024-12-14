<?php

namespace SMW\Tests;

use SMW\SetupCheck;
use SMW\Tests\PHPUnitCompat;
use ReflectionClass;

/**
 * @covers \SMW\SetupCheck
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 3.1
 *
 * @author mwjames
 */
class SetupCheckTest extends \PHPUnit\Framework\TestCase {

	use PHPUnitCompat;

	private $setupFile;

	protected function setUp(): void {
		parent::setUp();

		// Don't know why this is undefined but it is required by
		// testReadFromFile_InvalidJSON_ThrowsException()
		if ( !defined( 'SMW_PHPUNIT_DIR' ) ) {
			define( 'SMW_PHPUNIT_DIR', __DIR__ );
		}

		$this->setupFile = $this->getMockBuilder( '\SMW\SetupFile' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			SetupCheck::class,
			new SetupCheck( [] )
		);

		$this->assertInstanceOf(
			SetupCheck::class,
			SetupCheck::newFromDefaults()
		);
	}

	public function testHasError() {
		$this->setupFile->expects( $this->any() )
			->method( 'inMaintenanceMode' )
			->will( $this->returnValue( true ) );

		$instance = new SetupCheck( [], $this->setupFile );

		$this->assertInternalType(
			'boolean',
			$instance->hasError()
		);
	}

	public function testIsCli() {
		$instance = new SetupCheck( [], $this->setupFile );

		$this->assertInternalType(
			'boolean',
			$instance->isCli()
		);
	}

	public function testReadFromFile_ThrowsException() {
		$instance = new SetupCheck( [], $this->setupFile );

		$this->expectException( '\SMW\Exception\FileNotReadableException' );
		$instance->readFromFile( 'File' );
	}

	public function testReadFromFile_InvalidJSON_ThrowsException() {
		$instance = new SetupCheck( [], $this->setupFile );

		$this->expectException( '\RuntimeException' );
		$instance->readFromFile( SMW_PHPUNIT_DIR . '/Fixtures/invalid.json' );
	}

	public function testIsError() {
		$instance = SetupCheck::newFromDefaults(
			$this->setupFile
		);

		$instance->setErrorType(
			SetupCheck::ERROR_SCHEMA_INVALID_KEY
		);

		$this->assertTrue(
			$instance->isError( SetupCheck::ERROR_SCHEMA_INVALID_KEY )
		);
	}

	public function testUnknownErrorType_ThrowsException() {
		$instance = SetupCheck::newFromDefaults(
			$this->setupFile
		);

		$instance->setErrorType( 'foo' );

		$this->expectException( '\RuntimeException' );
		$instance->getError();
	}

	/**
	 * @dataProvider errorTypeProvider
	 */
	public function testGetError_CliOutput( $errorType ) {
		$this->setupFile->expects( $this->any() )
			->method( 'getMaintenanceMode' )
			->will( $this->returnValue( [ 'Foo' => 'bar' ] ) );

		$instance = new SetupCheck(
			[
				'wgScriptPath' => 'foo'
			],
			$this->setupFile
		);

		$instance->setErrorMessage( 'foo_bar' );
		$instance->setTraceString( 'trace_string' );

		$instance->setErrorType(
			$errorType
		);

		$this->assertInternalType(
			'string',
			$instance->getError( true )
		);
	}

	/**
	 * @dataProvider errorTypeProvider
	 */
	public function testGetError_NoCliHTMLOutput( $errorType ) {
		$this->setupFile->expects( $this->any() )
			->method( 'getMaintenanceMode' )
			->will( $this->returnValue( [ 'Foo' => 'bar' ] ) );

		$instance = new SetupCheck(
			[
				'wgScriptPath' => 'foo'
			],
			$this->setupFile
		);

		$instance->setErrorMessage( 'foo_bar' );
		$instance->setTraceString( 'trace_string' );

		$instance->disableHeader();

		$instance->setErrorType(
			$errorType
		);

		$this->assertStringContainsString(
			'<!DOCTYPE html>',
			$instance->getError( false )
		);
	}

	public function errorTypeProvider() {
		$reflectionClass = new ReflectionClass(
			SetupCheck::class
		);

		foreach ( $reflectionClass->getConstants() as $constant ) {
			yield [ $constant ];
		}
	}

}

<?php

namespace SMW\Tests\ParserFunctions;

use ParserOutput;
use SMW\MediaWiki\Renderer\WikitextTemplateRenderer;
use SMW\ParameterProcessorFactory;
use SMW\ParserFunctions\SetParserFunction;
use SMW\Services\ServicesFactory as ApplicationFactory;
use SMW\Tests\PHPUnitCompat;
use SMW\Tests\TestEnvironment;
use Title;

/**
 * @covers \SMW\ParserFunctions\SetParserFunction
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 1.9
 *
 * @author mwjames
 */
class SetParserFunctionTest extends \PHPUnit\Framework\TestCase {

	use PHPUnitCompat;

	private $testEnvironment;
	private $semanticDataValidator;

	protected function setUp(): void {
		parent::setUp();

		$this->testEnvironment = new TestEnvironment();
		$this->semanticDataValidator = $this->testEnvironment->getUtilityFactory()->newValidatorFactory()->newSemanticDataValidator();
	}

	protected function tearDown(): void {
		$this->testEnvironment->tearDown();
		parent::tearDown();
	}

	public function testCanConstruct() {
		$parserData = $this->getMockBuilder( '\SMW\ParserData' )
			->disableOriginalConstructor()
			->getMock();

		$messageFormatter = $this->getMockBuilder( '\SMW\MessageFormatter' )
			->disableOriginalConstructor()
			->getMock();

		$templateRenderer = $this->getMockBuilder( '\SMW\MediaWiki\Renderer\WikitextTemplateRenderer' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SMW\ParserFunctions\SetParserFunction',
			new SetParserFunction( $parserData, $messageFormatter, $templateRenderer )
		);
	}

	/**
	 * @dataProvider setParserProvider
	 */
	public function testParse( array $params ) {
		$parserData = ApplicationFactory::getInstance()->newParserData(
			Title::newFromText( __METHOD__ ),
			new ParserOutput()
		);

		$messageFormatter = $this->getMockBuilder( '\SMW\MessageFormatter' )
			->disableOriginalConstructor()
			->getMock();

		$messageFormatter->expects( $this->any() )
			->method( 'addFromArray' )
			->willReturnSelf();

		$messageFormatter->expects( $this->once() )
			->method( 'getHtml' )
			->willReturn( 'Foo' );

		$templateRenderer = $this->getMockBuilder( '\SMW\MediaWiki\Renderer\WikitextTemplateRenderer' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SetParserFunction(
			$parserData,
			$messageFormatter,
			$templateRenderer
		);

		$this->assertIsArray(

			$instance->parse( ParameterProcessorFactory::newFromArray( $params ) )
		);
	}

	/**
	 * @dataProvider setParserProvider
	 */
	public function testInstantiatedPropertyValues( array $params, array $expected ) {
		$parserData = ApplicationFactory::getInstance()->newParserData(
			Title::newFromText( __METHOD__ ),
			new ParserOutput()
		);

		$messageFormatter = $this->getMockBuilder( '\SMW\MessageFormatter' )
			->disableOriginalConstructor()
			->getMock();

		$messageFormatter->expects( $this->any() )
			->method( 'addFromArray' )
			->willReturnSelf();

		$templateRenderer = $this->getMockBuilder( '\SMW\MediaWiki\Renderer\WikitextTemplateRenderer' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new SetParserFunction(
			$parserData,
			$messageFormatter,
			$templateRenderer
		);

		$instance->parse( ParameterProcessorFactory::newFromArray( $params ) );

		$this->semanticDataValidator->assertThatPropertiesAreSet(
			$expected,
			$parserData->getSemanticData()
		);
	}

	public function testTemplateSupport() {
		$params = [ 'Foo=bar', 'Foo=foobar', 'BarFoo=9001', 'template=FooTemplate' ];

		$expected = [
			'errors' => 0,
			'propertyCount'  => 2,
			'propertyLabels' => [ 'Foo', 'BarFoo' ],
			'propertyValues' => [ 'Bar', '9001', 'Foobar' ]
		];

		$parserData = ApplicationFactory::getInstance()->newParserData(
			Title::newFromText( __METHOD__ ),
			new ParserOutput()
		);

		$messageFormatter = $this->getMockBuilder( '\SMW\MessageFormatter' )
			->disableOriginalConstructor()
			->getMock();

		$messageFormatter->expects( $this->any() )
			->method( 'addFromArray' )
			->willReturnSelf();

		$templateRenderer = new WikitextTemplateRenderer();

		$instance = new SetParserFunction(
			$parserData,
			$messageFormatter,
			$templateRenderer
		);

		$instance->parse(
			ParameterProcessorFactory::newFromArray( $params )
		);

		$this->semanticDataValidator->assertThatPropertiesAreSet(
			$expected,
			$parserData->getSemanticData()
		);
	}

	public function setParserProvider() {
		// #0 Single data set
		// {{#set:
		// |Foo=bar
		// }}
		$provider[] = [
			[ 'Foo=bar' ],
			[
				'errors' => 0,
				'propertyCount'  => 1,
				'propertyLabels' => 'Foo',
				'propertyValues' => 'Bar'
			]
		];

		// #1 Empty data set
		// {{#set:
		// |Foo=
		// }}
		$provider[] = [
			[ 'Foo=' ],
			[
				'errors' => 0,
				'propertyCount'  => 0,
				'propertyLabels' => '',
				'propertyValues' => ''
			]
		];

		// #2 Multiple data set
		// {{#set:
		// |BarFoo=9001
		// |Foo=bar
		// }}
		$provider[] = [
			[ 'Foo=bar', 'BarFoo=9001' ],
			[
				'errors' => 0,
				'propertyCount'  => 2,
				'propertyLabels' => [ 'Foo', 'BarFoo' ],
				'propertyValues' => [ 'Bar', '9001' ]
			]
		];

		// #3 Multiple data set with an error record
		// {{#set:
		// |_Foo=9001 --> will raise an error
		// |Foo=bar
		// }}
		$provider[] = [
			[ 'Foo=bar', '_Foo=9001' ],
			[
				'errors' => 1,
				'propertyCount'  => 2,
				'strictPropertyValueMatch' => false,
				'propertyKeys' => [ 'Foo', '_ERRC' ],
				'propertyValues' => [ 'Bar' ]
			]
		];

		return $provider;
	}

}

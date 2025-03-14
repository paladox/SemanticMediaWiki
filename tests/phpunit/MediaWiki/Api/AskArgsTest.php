<?php

namespace SMW\Tests\MediaWiki\Api;

use SMW\MediaWiki\Api\AskArgs;
use SMW\Services\ServicesFactory as ApplicationFactory;
use SMW\Tests\PHPUnitCompat;
use SMW\Tests\Utils\MwApiFactory;

/**
 * @covers \SMW\MediaWiki\Api\AskArgs
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 1.9
 *
 * @author mwjames
 */
class AskArgsTest extends \PHPUnit\Framework\TestCase {

	use PHPUnitCompat;

	private $apiFactory;
	private $applicationFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->apiFactory = new MwApiFactory();
		$this->applicationFactory = ApplicationFactory::getInstance();
	}

	protected function tearDown(): void {
		ApplicationFactory::clear();

		parent::tearDown();
	}

	public function testCanConstruct() {
		$instance = new AskArgs(
			$this->apiFactory->newApiMain( [] ),
			'askargs'
		);

		$this->assertInstanceOf(
			'SMW\MediaWiki\Api\AskArgs',
			$instance
		);
	}

	/**
	 * @dataProvider queryDataProvider
	 */
	public function testExecuteOnStore( array $query, array $expected ) {
		$results = $this->apiFactory->doApiRequest( [
			'action'     => 'askargs',
			'conditions' => $query['conditions'],
			'printouts'  => $query['printouts'],
			'parameters' => $query['parameters'],
		] );

		$this->assertIsArray( $results );

		if ( isset( $expected['error'] ) ) {
			return $this->assertArrayHasKey( 'error', $results );
		}

		$this->assertEquals(
			$expected,
			$results['query']['printrequests']
		);
	}

	public function testExecuteOnMockStore() {
		$requestParameters = [
			'conditions' => 'Foo::+',
			'printouts'  => 'Bar',
			'parameters' => 'sort=asc'
		];

		$expected = [
			'query-continue-offset' => 10,
			'query' => [
				'results' => [
					'Foo' => [
						'printouts' => [ 'lula' => [ 'lila' ] ]
					]
				],
				'printrequests' => [ 'Bar' ],
				'meta' => [ 'count' => 5, 'offset' => 5 ]
			]
		];

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->atLeastOnce() )
			->method( 'getQueryResult' )
			->willReturnCallback( [ $this, 'mockStoreQueryResultCallback' ] );

		$this->applicationFactory->registerObject( 'Store', $store );

		$instance = new AskArgs(
			$this->apiFactory->newApiMain( $requestParameters ),
			'askargs'
		);

		$instance->execute();

		$result = $instance->getResult()->getResultData();

		// This came with 1.25, no idea what this suppose to be
		unset( $result['_type'] );

		$this->assertIsArray( $result );
		$this->assertEquals( $expected, $result );
	}

	public function mockStoreQueryResultCallback( $query ) {
		$result = '';

		if ( $query->getQueryString() === '[[Foo::+]]' ) {
			$result = [
				'results' => [
					'Foo' => [
						'printouts' => [ 'lula' => [ 'lila' ] ]
					]
				],
				'printrequests' => [ 'Bar' ],
				'meta' => [ 'count' => 5, 'offset' => 5 ]
			];
		}

		$queryResult = $this->getMockBuilder( '\SMW\Query\QueryResult' )
			->disableOriginalConstructor()
			->getMock();

		$queryResult->expects( $this->atLeastOnce() )
			->method( 'toArray' )
			->willReturn( $result );

		$queryResult->expects( $this->atLeastOnce() )
			->method( 'hasFurtherResults' )
			->willReturn( true );

		$queryResult->expects( $this->atLeastOnce() )
			->method( 'getErrors' )
			->willReturn( [] );

		return $queryResult;
	}

	public function queryDataProvider() {
		return [

			// #0 Query producing an error result
			[
				[
					'conditions' => '[[Modification date::+]]',
					'printouts'  => null,
					'parameters' => null
				],
				[
					'error'      => true
				]
			],

			// #1 Query producing an error result
			[
				[
					'conditions' => '[[Modification date::+]]',
					'printouts'  => null,
					'parameters' => 'limit=10'
				],
				[
					'error'      => true
				]
			],

			// #2 Query producing an error result
			[
				[
					'conditions' => '[[Modification date::+]]',
					'printouts'  => 'Modification date',
					'parameters' => 'limit=10'
				],
				[
					'error'      => true
				]
			],

			// #3 Query producing a return result
			[
				[
					'conditions' => 'Modification date::+',
					'printouts'  => null,
					'parameters' => null
				],
				[
					[
						'label' => '',
						'typeid' => '_wpg',
						'mode' => 2,
						'format' => false,
						'key' => '',
						'redi' => ''
					]
				]
			],

			// #4 Query producing a return result
			[
				[
					'conditions' => 'Modification date::+',
					'printouts'  => 'Modification date',
					'parameters' => null
				],
				[
					[
						'label' => '',
						'typeid' => '_wpg',
						'mode' => 2,
						'format' => false,
						'key' => '',
						'redi' => ''
					],
					[
						'label' => 'Modification date',
						'typeid' => '_dat',
						'mode' => 1,
						'format' => '',
						'key' => '_MDAT',
						'redi' => ''
					]
				]
			],

			// #5 Query producing a return result
			[
				[
					'conditions' => 'Modification date::+',
					'printouts'  => 'Modification date',
					'parameters' => 'limit=1'
				],
				[
					[
						'label' => '',
						'typeid' => '_wpg',
						'mode' => 2,
						'format' => false,
						'key' => '',
						'redi' => ''
					],
					[
						'label' => 'Modification date',
						'typeid' => '_dat',
						'mode' => 1,
						'format' => '',
						'key' => '_MDAT',
						'redi' => ''
					]
				]
			],
		];
	}
}

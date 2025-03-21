<?php

namespace SMW\Tests\SQLStore\EntityStore;

use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SQLStore\EntityStore\PrefetchCache;
use SMW\Tests\PHPUnitCompat;

/**
 * @covers \SMW\SQLStore\EntityStore\PrefetchCache
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 3.1
 *
 * @author mwjames
 */
class PrefetchCacheTest extends \PHPUnit\Framework\TestCase {

	use PHPUnitCompat;

	private $store;
	private $prefetchItemLookup;
	private $requestOptions;

	protected function setUp(): void {
		$this->store = $this->getMockBuilder( '\SMW\SQLStore\SQLStore' )
			->disableOriginalConstructor()
			->getMock();

		$this->prefetchItemLookup = $this->getMockBuilder( '\SMW\SQLStore\EntityStore\PrefetchItemLookup' )
			->disableOriginalConstructor()
			->getMock();

		$this->requestOptions = $this->getMockBuilder( '\SMW\RequestOptions' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			PrefetchCache::class,
			new PrefetchCache( $this->store, $this->prefetchItemLookup )
		);
	}

	public function testCacheAndFetch() {
		$property = new DIProperty( 'Foo' );
		$subject = DIWikiPage::newFromText( __METHOD__ );

		$expected = [
			DIWikiPage::newFromText( 'Bar' )
		];

		$idTable = $this->getMockBuilder( '\SMW\SQLStore\EntityStore\EntityIdManager' )
			->disableOriginalConstructor()
			->getMock();

		$idTable->expects( $this->atLeastOnce() )
			->method( 'getSMWPageID' )
			->with( __METHOD__ )
			->willReturn( 42 );

		$this->store->expects( $this->atLeastOnce() )
			->method( 'getObjectIds' )
			->willReturn( $idTable );

		$this->prefetchItemLookup->expects( $this->atLeastOnce() )
			->method( 'getPropertyValues' )
			->willReturn( [ 42 => [ DIWikiPage::newFromText( 'Bar' ) ] ] );

		$instance = new PrefetchCache(
			$this->store,
			$this->prefetchItemLookup
		);

		$instance->prefetch( [ $subject ], $property, $this->requestOptions );

		$this->assertEquals(
			$expected,
			$instance->getPropertyValues( $subject, $property, $this->requestOptions )
		);
	}

}

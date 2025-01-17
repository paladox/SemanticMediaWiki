<?php

namespace SMW\Tests\Listener\EventListener\EventListeners;

use Onoi\EventDispatcher\DispatchContext;
use SMW\DIWikiPage;
use SMW\Listener\EventListener\EventListeners\InvalidatePropertySpecificationLookupCacheEventListener;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Listener\EventListener\EventListeners\InvalidatePropertySpecificationLookupCacheEventListener
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 3.2
 *
 * @author mwjames
 */
class InvalidatePropertySpecificationLookupCacheEventListenerTest extends \PHPUnit\Framework\TestCase {

	private $specificationLookup;
	private $spyLogger;

	protected function setUp(): void {
		parent::setUp();

		$this->spyLogger = TestEnvironment::newSpyLogger();

		$this->specificationLookup = $this->getMockBuilder( '\SMW\Property\SpecificationLookup' )
			->disableOriginalConstructor()
			->getMock();
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			InvalidatePropertySpecificationLookupCacheEventListener::class,
			new InvalidatePropertySpecificationLookupCacheEventListener( $this->specificationLookup )
		);
	}

	public function testExecute_OnSubject() {
		$context = DispatchContext::newFromArray(
			[
				'subject' => DIWikiPage::newFromText( __METHOD__ ),
				'context' => 'Bar'
			]
		);

		$this->specificationLookup->expects( $this->once() )
			->method( 'invalidateCache' );

		$instance = new InvalidatePropertySpecificationLookupCacheEventListener(
			$this->specificationLookup
		);

		$instance->setLogger(
			$this->spyLogger
		);

		$instance->execute( $context );
	}

}

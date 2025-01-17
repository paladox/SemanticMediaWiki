<?php

namespace SMW\Tests\MediaWiki\Jobs;

use SMW\DIWikiPage;
use SMW\MediaWiki\Jobs\FulltextSearchTableUpdateJob;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\MediaWiki\Jobs\FulltextSearchTableUpdateJob
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 2.5
 *
 * @author mwjames
 */
class FulltextSearchTableUpdateJobTest extends \PHPUnit\Framework\TestCase {

	private $testEnvironment;

	protected function setUp(): void {
		parent::setUp();

		$this->testEnvironment = new TestEnvironment();

		$this->testEnvironment->registerObject(
			'Store',
			$this->getMockBuilder( '\SMW\SQLStore\SQLStore' )->getMockForAbstractClass()
		);
	}

	protected function tearDown(): void {
		$this->testEnvironment->tearDown();
		parent::tearDown();
	}

	public function testCanConstruct() {
		$title = $this->getMockBuilder( 'Title' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'SMW\MediaWiki\Jobs\FulltextSearchTableUpdateJob',
			new FulltextSearchTableUpdateJob( $title )
		);
	}

	/**
	 * @dataProvider parametersProvider
	 */
	public function testJobRun( $parameters ) {
		$subject = DIWikiPage::newFromText( __METHOD__ );

		$instance = new FulltextSearchTableUpdateJob(
			$subject->getTitle(),
			$parameters
		);

		$this->assertTrue(
			$instance->run()
		);
	}

	public function parametersProvider() {
		return [
			[
				'diff' => [
					'slot:id' => 'itemName#123#extraData',
					1,
					2
				]
			],
			[
				'diff' => [
					'slot:id' => 'itemName#123#extraData#additionalInfo',
					1,
					2
				]
			],
			[
				'diff' => [
					1,
					2
				]
			]
		];
	}

}

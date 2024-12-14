<?php

namespace SMW\Tests\MediaWiki\Specials\Ask;

use SMW\MediaWiki\Specials\Ask\DownloadLinksWidget;
use SMW\Tests\PHPUnitCompat;

/**
 * @covers \SMW\MediaWiki\Specials\Ask\DownloadLinksWidget
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 3.0
 *
 * @author mwjames
 */
class DownloadLinksWidgetTest extends \PHPUnit\Framework\TestCase {

	use PHPUnitCompat;

	public function testOnNull() {
		$this->assertEmpty(
			DownloadLinksWidget::downloadLinks( null )
		);
	}

	public function testLinks() {
		$infolink = $this->getMockBuilder( '\SMWInfolink' )
			->disableOriginalConstructor()
			->getMock();

		$infolink->expects( $this->atLeastOnce() )
			->method( 'setParameter' );

		$this->assertStringContainsString(
			'<div id="ask-export-links" class="smw-ask-downloadlinks export-links">',
			DownloadLinksWidget::downloadLinks( $infolink )
		);
	}

}

<?php

namespace SMW\Tests\SPARQLStore\Exception;

use SMW\SPARQLStore\Exception\HttpEndpointConnectionException;

/**
 * @covers \SMW\SPARQLStore\Exception\HttpEndpointConnectionException
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 2.0
 *
 * @author mwjames
 */
class HttpEndpointConnectionExceptionTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf(
			HttpEndpointConnectionException::class,
			new HttpEndpointConnectionException( 'Foo', 'Bar', 'Que' )
		);
	}

}

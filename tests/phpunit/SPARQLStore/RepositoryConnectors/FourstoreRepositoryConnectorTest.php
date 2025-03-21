<?php

namespace SMW\Tests\SPARQLStore\RepositoryConnectors;

use SMW\SPARQLStore\RepositoryConnectors\FourstoreRepositoryConnector;

/**
 * @covers \SMW\SPARQLStore\RepositoryConnectors\FourstoreRepositoryConnector
 * @group semantic-mediawiki
 *
 * @license GPL-2.0-or-later
 * @since 3.0
 *
 * @author mwjames
 */
class FourstoreRepositoryConnectorTest extends ElementaryRepositoryConnectorTest {

	public function getRepositoryConnectors() {
		return [
			FourstoreRepositoryConnector::class
		];
	}

}

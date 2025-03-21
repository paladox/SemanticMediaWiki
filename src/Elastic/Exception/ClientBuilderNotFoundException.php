<?php

namespace SMW\Elastic\Exception;

use RuntimeException;

/**
 * @license GPL-2.0-or-later
 * @since 3.0
 *
 * @author mwjames
 */
class ClientBuilderNotFoundException extends RuntimeException {

	/**
	 * @since 3.0
	 */
	public function __construct() {
		parent::__construct(
			"The \Elasticsearch\ClientBuilder class is missing, please see https://www.semantic-mediawiki.org/wiki/Help:ElasticStore/ClientBuilder!"
		);
	}

}

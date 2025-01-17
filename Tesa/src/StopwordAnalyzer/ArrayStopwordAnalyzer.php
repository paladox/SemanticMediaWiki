<?php

namespace Onoi\Tesa\StopwordAnalyzer;

/**
 * @license GPL-2.0-or-later
 * @since 0.1
 *
 * @author mwjames
 */
class ArrayStopwordAnalyzer implements StopwordAnalyzer {

	/**
	 * Any change to the content of its data files should be reflected in a
	 * version change (the version number does not necessarily correlate with
	 * the library version)
	 */
	const VERSION = '0.1';

	/**
	 * @var Cdb
	 */
	private $stopwords;

	/**
	 * @since 0.1
	 *
	 * @param array $stopwords
	 */
	public function __construct( array $stopwords = [] ) {
		$this->stopwords = array_flip( $stopwords );
	}

	/**
	 * @since 0.1
	 *
	 * @param string $word
	 *
	 * @return bool
	 */
	public function isStopWord( $word ) {
		return isset( $this->stopwords[$word] );
	}

}

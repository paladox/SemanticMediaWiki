<?php

namespace SMW\Query;

use SMW\Property\Annotator;

/**
 * Specifying the ProfileAnnotator interface
 *
 * @ingroup SMW
 *
 * @license GPL-2.0-or-later
 * @since 1.9
 *
 * @author mwjames
 */
interface ProfileAnnotator extends Annotator {

	/**
	 * Returns the query meta data property
	 *
	 * @since 1.9
	 *
	 * @return DIProperty
	 */
	public function getProperty();

	/**
	 * Returns the query meta data container
	 *
	 * @since 1.9
	 *
	 * @return DIContainer
	 */
	public function getContainer();

}

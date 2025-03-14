<?php

namespace SMW\Query;

use SMW\DataValueFactory;
use SMW\DataValues\PropertyValue;
use SMW\DIProperty;

/**
 * @license GPL-2.0-or-later
 * @since 2.1
 *
 * @author mwjames
 */
class PrintRequestFactory {

	/**
	 * @since 2.1
	 *
	 * @param DIProperty $property
	 *
	 * @return PrintRequest
	 */
	public function newFromProperty( DIProperty $property ) {
		$propertyValue = DataValueFactory::getInstance()->newDataValueByType( PropertyValue::TYPE_ID );
		$propertyValue->setDataItem( $property );

		$instance = new PrintRequest(
			PrintRequest::PRINT_PROP,
			$propertyValue->getWikiValue(),
			$propertyValue
		);

		return $instance;
	}

	/**
	 * @see PrintRequest::newFromText
	 *
	 * @since 2.4
	 *
	 * @param string $text
	 * @param bool $showMode = false
	 * @param bool $asCanonicalLabel = false
	 *
	 * @return PrintRequest|null
	 */
	public function newFromText( $text, $showMode = false, $asCanonicalLabel = false ) {
		return PrintRequest::newFromText( $text, $showMode, $asCanonicalLabel );
	}

	/**
	 * @since 3.0
	 *
	 * @param string $label
	 * @param array $parameters
	 *
	 * @return PrintRequest
	 */
	public function newThisPrintRequest( $label = '', array $parameters = [] ) {
		return new PrintRequest( PrintRequest::PRINT_THIS, $label, null, false, $parameters );
	}

}

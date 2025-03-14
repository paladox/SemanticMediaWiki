<?php

namespace SMW\Tests\Utils\Validators;

use SMW\Exporter\Element\ExpResource;
use SMWExpData as ExpData;

/**
 * @license GPL-2.0-or-later
 * @since   2.0
 *
 * @author mwjames
 */
class ExportDataValidator extends \PHPUnit\Framework\Assert {

	/**
	 * @since 2.0
	 *
	 * @param mixed $expectedProperties
	 * @param ExpData $exportData
	 */
	public function assertThatExportDataContainsProperty( $expectedProperties, ExpData $exportData ) {
		$expProperties = $exportData->getProperties();

		$this->assertNotEmpty( $expProperties );

		$expectedProperties = is_array( $expectedProperties ) ? $expectedProperties : [ $expectedProperties ];
		$expectedToCount  = count( $expectedProperties );
		$actualComparedToCount = 0;

		$assertThatExportDataContainsProperty = false;

		foreach ( $expProperties as $expProperty ) {
			foreach ( $expectedProperties as $expectedProperty ) {
				if ( $expectedProperty->getHash() === $expProperty->getHash() ) {
					$actualComparedToCount++;
					$assertThatExportDataContainsProperty = true;
				}
			}
		}

		$this->assertTrue( $assertThatExportDataContainsProperty );

		$this->assertEquals(
			$expectedToCount,
			$actualComparedToCount
		);
	}

	/**
	 * @since 2.0
	 *
	 * @param mixed $expectedResources
	 * @param ExpResource $selectedElement
	 * @param ExpData $exportData
	 */
	public function assertThatExportDataContainsResource( $expectedResources, ExpResource $selectedElement, ExpData $exportData ) {
		$expElements = $exportData->getValues( $selectedElement );

		$this->assertNotEmpty( $expElements );

		$expectedResources = is_array( $expectedResources ) ? $expectedResources : [ $expectedResources ];
		$expectedToCount  = count( $expectedResources );
		$actualComparedToCount = 0;

		$assertThatExportDataContainsResource = false;

		foreach ( $expElements as $expElement ) {
			foreach ( $expectedResources as $expectedResource ) {
				if ( $expectedResource->getHash() === $expElement->getHash() ) {
					$actualComparedToCount++;
					$assertThatExportDataContainsResource = true;
				}
			}
		}

		$this->assertTrue(
			$assertThatExportDataContainsResource,
			'Asserts that a resource is set'
		);

		$this->assertEquals(
			$expectedToCount,
			$actualComparedToCount,
			'Asserts that all listed resources are set'
		);
	}

}

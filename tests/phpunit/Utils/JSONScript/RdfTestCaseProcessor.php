<?php

namespace SMW\Tests\Utils\JSONScript;

use MediaWikiIntegrationTestCase;
use SMW\Exporter\ExporterFactory;

/**
 * @group semantic-mediawiki
 * @group Database
 * @group medium
 *
 * @license GPL-2.0-or-later
 * @since 2.3
 *
 * @author mwjames
 */
class RdfTestCaseProcessor extends MediaWikiIntegrationTestCase {

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var StringValidator
	 */
	private $stringValidator;

	/**
	 * @var RunnerFactory
	 */
	private $runnerFactory;

	/**
	 * @var bool
	 */
	private $debug = false;

	/**
	 * @param Store
	 * @param StringValidator
	 */
	public function __construct( $store, $stringValidator, $runnerFactory ) {
		$this->store = $store;
		$this->stringValidator = $stringValidator;
		$this->runnerFactory = $runnerFactory;
	}

	/**
	 * @since  2.2
	 */
	public function setDebugMode( $debugMode ) {
		$this->debug = $debugMode;
	}

	public function process( array $case ) {
		// Allows for data to be re-read from the DB instead of being fetched
		// from the store-id-cache
		if ( isset( $case['store']['clear-cache'] ) && $case['store']['clear-cache'] ) {
			$this->store->clear();
		}

		if ( isset( $case['dumpRDF'] ) ) {
			$this->assertDumpRdfOutputForCase( $case );
		}

		if ( isset( $case['exportcontroller'] ) ) {
			$this->assertExportControllerOutputForCase( $case );
		}
	}

	private function assertDumpRdfOutputForCase( $case ) {
		$maintenanceRunner = $this->runnerFactory->newMaintenanceRunner( '\SMW\Maintenance\dumpRDF' );
		$maintenanceRunner->setQuiet();

		$maintenanceRunner->setOptions( $case['dumpRDF']['parameters'] );
		$maintenanceRunner->run();

		$this->assertOutputForCase(
			$case,
			$maintenanceRunner->getOutput()
		);
	}

	private function assertExportControllerOutputForCase( $case ) {
		$exporterFactory = new ExporterFactory();

		if ( isset( $case['exportcontroller']['syntax'] ) && $case['exportcontroller']['syntax'] === 'turtle' ) {
			$serializer = $exporterFactory->newTurtleSerializer();
		} else {
			$serializer = $exporterFactory->newRDFXMLSerializer();
		}

		$exportController = $exporterFactory->newExportController( $serializer );
		$exportController->enableBacklinks( $case['exportcontroller']['parameters']['backlinks'] );

		ob_start();

		if ( isset( $case['exportcontroller']['print-pages'] ) ) {
			$exportController->printPages(
				$case['exportcontroller']['print-pages'],
				(int)$case['exportcontroller']['parameters']['recursion'],
				$case['exportcontroller']['parameters']['revisiondate']
			);
		}

		if ( isset( $case['exportcontroller']['wiki-info'] ) ) {
			$exportController->printWikiInfo();
		}

		$output = ob_get_clean();

		$this->assertOutputForCase( $case, $output );
	}

	private function assertOutputForCase( $case, $output ) {
		if ( $this->debug ) {
			print_r( $output );
		}

		if ( isset( $case['assert-output']['to-contain'] ) ) {
			$this->stringValidator->assertThatStringContains(
				$case['assert-output']['to-contain'],
				$output,
				$case['about']
			);
		}

		if ( isset( $case['assert-output']['not-contain'] ) ) {
			$this->stringValidator->assertThatStringNotContains(
				$case['assert-output']['not-contain'],
				$output,
				$case['about']
			);
		}
	}

}

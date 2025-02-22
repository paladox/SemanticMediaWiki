<?php

namespace SMW\Query\Result;

use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDataItem;

/**
 * This class records selected entities used in a QueryResult by the time the
 * ResultArray creates an object instance which avoids unnecessary work in the
 * QueryResultDependencyListResolver (in terms of recursive processing of the
 * QueryResult) to find related "column" entities (those related to a
 * printrequest).
 *
 * @license GPL-2.0-or-later
 * @since 2.4
 *
 * @author mwjames
 */
class ItemJournal {

	private $dataItems = [];
	private $properties = [];

	/**
	 * @since 2.4
	 *
	 * @return SMWDataItem[]
	 */
	public function getEntityList() {
		return $this->dataItems;
	}

	/**
	 * @since 3.0
	 *
	 * @return DIProperty[]
	 */
	public function getPropertyList() {
		return $this->properties;
	}

	/**
	 * @since 2.4
	 */
	public function prune() {
		$this->dataItems = [];
		$this->properties = [];
	}

	/**
	 * @since 2.4
	 *
	 * @param SMWDataItem $dataItem
	 */
	public function recordItem( SMWDataItem $dataItem ) {
		if ( $dataItem instanceof DIWikiPage ) {
			$this->dataItems[$dataItem->getHash()] = $dataItem;
		}
	}

	/**
	 * @since 2.4
	 *
	 * @param DIProperty|null $property
	 */
	public function recordProperty( ?DIProperty $property = null ) {
		if ( $property !== null ) {
			$this->properties[$property->getKey()] = $property;
		}
	}

}

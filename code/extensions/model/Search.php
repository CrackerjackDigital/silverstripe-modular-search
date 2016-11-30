<?php
namespace Modular\Extensions\Model;

/**
 * ModelExtension
 *
 * @package Modular\Search
 */
class Search extends \Modular\ModelExtension {
	const SearchIndexPrefix = 'Fulltext';

	// only classes matched here via ModelTag.relatedByClassName will be returned
	// matches may be made by glob style wild-cards, e.g. '*Page' will match classes ending in 'Page'
	private static $search_classes = [
		'SiteTree' => true,
		'File'     => true
	];

	private static $fulltext_fields = [
		# e.g.
		# 'Title' => true
	];

	// optionally configured fields when extension is added,
	// e.g. in a yaml file:
	//  Model:
	//    extensions:
	//      - Modular\Extensions\Model\Search('Title,Content')
	//
	/** @var string csv seperated list of fields set in ctor */
	protected $fulltextFields = '';

	public function __construct($csvFieldNames = '') {
		parent::__construct();
		if ($csvFieldNames) {
			$this->fulltextFields = $csvFieldNames;
		}
	}

	/**
	 * Return configured class names (keys) from config.search_classes which have a truthish value
	 *
	 * @return array
	 */
	public static function search_classes() {
		return array_keys(array_filter(static::config()->get('search_classes'))) ?: [];
	}

	/**
	 * Search results for most Models is the model itself, override if something different for
	 * example if the search target is the Page which owns a File who's content is being searched in
	 * which case the Page should be returned in an ArrayList so it can be merged.
	 *
	 * @return \ArrayList
	 */
	public function SearchTargets() {
		return new \ArrayList([
			$this()
		]);
	}

	protected function searchIndex() {
		return static::SearchIndexPrefix . str_replace(',', '', $this->fulltextFields());
	}

	protected function fulltextFields() {
		return trim(
			"$this->fulltextFields," .
			implode(',', array_keys(array_filter(
				$this->config()->get('fulltext_fields') ?: []
			))),
			', '
		);
	}

	/**
	 * Add full text search field indexes.
	 * @param null $class
	 * @param null $extension
	 * @return array
	 */

	public function extraStatics($class = null, $extension = null) {
		// just get the enabled ones, field names are keys, if value is false then skip it
		$fulltextFields = $this->fulltextFields();

		$statics = parent::extraStatics($class, $extension) ?: [];
		if ($fulltextFields) {
			$searchIndex = $this->searchIndex();

			$statics = array_merge(
				$statics,
				[
					'create_table_options' => [
						'MySQLDatabase' => 'ENGINE=MyISAM'
					],
					'indexes'              => [
						$searchIndex => [
							'type'  => 'fulltext',
							'name'  => $searchIndex,
							'value' => $fulltextFields,
						],
					]
				]
			);
		}
		return $statics;
	}
}

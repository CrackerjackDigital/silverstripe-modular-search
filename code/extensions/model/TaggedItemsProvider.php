<?php
namespace Modular\Extensions\Model;

use Modular\Fields\ModelTag;
use Modular\GridList\Interfaces\ItemsProvider;
use Modular\Models\Tag;

/**
 * Provides items which match 'tags' query paramter as ModelTags - tags can be a csv list of ModelTags.
 *
 * @package Modular\Search
 */
class SearchTags extends Search implements ItemsProvider {
	// only classes matching here by ModelTag.relatedByClassName will be included
	// e.g. '*Page' will be all classes ending in 'Page'
	private static $search_classes = [
		# 'SiteTree',
		# 'File',
	];

	public function provideGridListItems() {
		$results = new \ArrayList();

		/** @var Service $service */
		$service = \Injector::inst()->get('SearchService');

		if ($tags = array_filter(explode(',', $service->constraint(Constraints::TagsVar)))) {
			$allTags = Tag::get();
			$searchClasses = $this->config()->get('search_classes');

			foreach ($tags as $tag) {
				/** @var ModelTag $tag */
				if ($tag = $allTags->find(ModelTag::field_name(), $tag)) {
					// merge all related classes that end in 'Page' for this tag
					$results->merge($tag->relatedByClassName($searchClasses));
				}
			}
		}
		return $results;
	}
}
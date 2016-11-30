<?php
namespace Modular\Extensions\Model;

class SearchSiteTree extends Search {
	private static $fulltext_fields = [
		'Title'   => 'PartialMatchFilter',
		'Content' => 'PartialMatchFilter',
	];
}
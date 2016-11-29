<?php
namespace Modular\Extensions\Model;

class SearchFiles extends Search  {
	private static $fulltext_fields = [
		'Title'   => 'PartialMatchFilter',
	];

}
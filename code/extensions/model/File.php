<?php
namespace Modular\Extensions\Model;

class SearchFile extends Search  {
	private static $fulltext_fields = [
		'Title'   => 'PartialMatchFilter',
	];

}
<?php
return [
	'parse' => function($content) {
		$name = trim($content);

		if (empty($name)) {
			return null;
		}

		return [
			'group' => preg_replace('/(\s+)/', '_', $content)
		];
	},

	'path'   => 'local',
	'method' => 'insert'
];

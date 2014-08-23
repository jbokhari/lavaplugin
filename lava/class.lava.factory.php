<?php
/**
 * Class LavaFactory creates a LavaOption based on the type specified
 * @package Lava
 * @author Jameel Bokhari
 * @license GPL22
 */
final class LavaFactory {
	static public $no = -1;
	static function create($prefix, array $options ){
		self::$no++;
		$type = $options['type'];
		if (!$type)
			return;
		require_once "class.lava.plugin.option." . $type . ".php";
		$object = "LavaOption_{$type}";
		// require_once "class.lava.plugin.option.str.php";
		// require_once "class.lava.plugin.option.url.php";
		// require_once "class.lava.plugin.option.array.php";
		// require_once "class.lava.plugin.option.sortable.php";
		// require_once "class.lava.plugin.option.textarea.php";
		// require_once "class.lava.plugin.option.int.php";
		// require_once "class.lava.plugin.option.image.php";
		// require_once "class.lava.plugin.option.email.php";
		// require_once "class.lava.plugin.option.bool.php";
		// require_once "class.lava.plugin.option.color.php";
		// require_once "class.lava.plugin.option.repeater.php";
		return new $object($prefix, $options, self::$no);
	}
	// static function create_subfield($prefix, array $options ){
	// 	$type = $options['type'];
	// 	$options['name'] = $options['name'] . "[]";
	// 	if (!$type){	
	// 		return;
	// 	}
	// 	require_once "class.lava.plugin.option." . $type . ".php";
	// 	$object = "LavaOption_{$type}";
	// 	return new $object($prefix, $options);
	// }
}
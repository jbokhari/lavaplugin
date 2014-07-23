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
		$object = "LavaOption_{$type}";
		return new $object($prefix, $options, self::$no);
	}
}
<?php
/**
 * Class LavaFactory creates a LavaOption based on the type specified
 * @package Lava
 * @version 2.2
 * @author Jameel Bokhari
 * @license GPL22
 */
final class LavaFactory {
	static function create($prefix, array $options ){
		$type = $options['type'];
		if (!$type)
			return false;
		$object = "LavaOption_{$type}";
		return new $object($prefix, $options);
	}
}
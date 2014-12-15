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
		$no = self::$no;
		if ( isset( $options['subfield'] ) ) {
			$no .= "_{$options['subfield']}";
		}
		$return = new $object($prefix, $options, $no, $plugin);
		return $return;

	}
}
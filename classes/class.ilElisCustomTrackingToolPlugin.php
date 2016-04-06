<?php

include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");

/**
 * Example user interface plugin
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 *
 */
class ilElisCustomTrackingToolPlugin extends ilUserInterfaceHookPlugin
{
	private static $object;
	
	function getPluginName()
	{
		return "ElisCustomTrackingTool";
	}
	
	static function _includeClass($a_class)
	{
		self::_getInstance()->includeClass($a_class);
	}
	
	static function _getInstance()
	{
		if(!is_object(self::$object))
		{
			self::$object = new self();
		}
		return self::$object;
	}
	
	static function _getDir()
	{
		return substr(self::_getInstance()->getDirectory(),2);
	}
}
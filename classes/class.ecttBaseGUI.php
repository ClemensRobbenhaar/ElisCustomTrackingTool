<?php
require_once('Modules/OrgUnit/classes/class.ilObjOrgUnitTree.php');

abstract class ecttBaseGUI
{
	private static $user_cache = array();
	private static $course_cache = array();
	private static $category_cache = array();
	private static $session_cache = array();
	protected $default_cmd;

	private $link_target = null;

	protected $cmd = null;

	final public function __construct($link_target, $cmd = '')
	{
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttContentGUI.php');

		$this->link_target = $link_target;

		$this->cmd = $cmd;
		
		$cmd = $cmd.'Cmd';
		if( !strlen($this->cmd) || !method_exists($this, $cmd) )
		{
			$this->cmd = $this->default_cmd;
		}

		$cmd = $this->cmd.'Cmd';
		if( !strlen($this->cmd) || !method_exists($this, $cmd) )
		{
			throw new Exception(
				'Error: no command method provided for defined '.
				'default command! ('.$this->default_cmd.')'
			);
		}
		// build tabs
		$this->initTabs();

		// prepare output gui
		$this->content = new ecttContentGUI();

		$this->__init();
	}

	abstract protected function __init();

	abstract protected function __getTabs();

	/**
	 * @global ilTabsGUI $ilTabs
	 */
	private function initTabs()
	{
		/**
		 * @var ilTabsGUI $ilTabs
		 */
		global $ilTabs;

		$tabs = $this->__getTabs();

		foreach($tabs as $tab)
		{
			if( !isset($tab['cmd']) || !strlen($tab['cmd'])  )
			{
				if(!isset($tab['backcmd']) || !strlen($tab['backcmd']))
				{
					throw new Exception(
						'Error: no cmd parameter for tabs link given!'
					);
				}
			}

			if( !isset($tab['langvar']) || !strlen($tab['langvar']) )
			{
				throw new Exception(
					'Error: no label value for tabs title given!'
				);
			}

			if( isset($tab['backcmd']) && strlen($tab['backcmd']) )
			{
				$link = $this->getLinkTarget($tab['backcmd']);
				$title = $this->txt($tab['langvar']);
				$ilTabs->setBackTarget($title, $link);
				break;
			}
			else
			{
				$link = $this->getLinkTarget($tab['cmd']);

				$ilTabs->addTab($tab['langvar'],$this->txt($tab['langvar']), $link);

				if($tab['cmd'] == $this->getCmd())
					$ilTabs->activateTab($tab['langvar']);
			}
		}
	}

	public function executeCommand()
	{
		// build command method name
		$command_method = $this->cmd.'Cmd';

		// run command
		$this->$command_method();

		// process errors
		$this->processErrors();

		// print output
		$this->content->show();
	}

	protected function processErrors()
	{
		return;
	}

	public function getCmd()
	{
		return $this->cmd;
	}

	public function getLinkTarget($cmd = null, $add_rtoken = false)
	{
		global $ilCtrl;
		
		$target = $this->link_target;

		if( $cmd !== null && strlen($cmd) )
		{
			$target .= '&cmd='.$cmd;

		}

		if($add_rtoken)
		{
			$target = $ilCtrl->appendRequestTokenParameterString($target);
		}

		return $target;
	}


	/**
	 * @return ilObjUser $user
	 */
	public static function getUserByUserId($user_id)
	{
		if( !isset(self::$user_cache[$user_id]) )
		{
			$user = ilObjectFactory::getInstanceByObjId($user_id, false);
			if( !($user instanceof ilObjUser) )
			{
				throw new Exception(
					'Error: could not find user with id "'.$user_id.'"'
				);
			}
			self::$user_cache[$user_id] = $user;
		}

		return self::$user_cache[$user_id];
	}

	/**
	 * @return ilObjCourse $course
	 */
	public static function getCourseByCourseId($course_id)
	{
		if( !isset(self::$course_cache[$course_id]) )
		{
			$course = ilObjectFactory::getInstanceByObjId($course_id, false);
			if( !($course instanceof ilObjCourse) )
			{
				throw new Exception(
					'Error: could not find course with id "'.$course_id.'"'
				);
			}
			self::$course_cache[$course_id] = $course;
		}

		return self::$course_cache[$course_id];
	}

	/**
	 * @return ilObjCategory $category
	 */
	public static function getCategoryByCategoryId($category_id)
	{
		if( !isset(self::$category_cache[$category_id]) )
		{
			$category = ilObjectFactory::getInstanceByObjId($category_id, false);
			if( !($category instanceof ilObjCategory) && !($category instanceof ilObjRootFolder) )
			{
				throw new Exception(
					'Error: could not find category with id "'.$category_id.'"'
				);
			}
			self::$category_cache[$category_id] = $category;
		}

		return self::$category_cache[$category_id];
	}

	/**
	 * @return ilObjSession $session
	 */
	public static function getSessionBySessionId($session_id)
	{
		if( !isset(self::$session_cache[$session_id]) )
		{
			$session = ilObjectFactory::getInstanceByObjId($session_id, false);
			if( !($session instanceof ilObjSession) )
			{
				throw new Exception(
					'Error: could not find session with id "'.$session_id.'"'
				);
			}
			self::$session_cache[$session_id] = $session;
		}

		return self::$session_cache[$session_id];
	}
	
	public function txt($a_txt)
	{
		return ilElisCustomTrackingToolPlugin::_getInstance()->txt($a_txt);
	}
}

?>

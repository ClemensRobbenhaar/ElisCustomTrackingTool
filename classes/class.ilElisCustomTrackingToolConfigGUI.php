<?php
include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

/**
 * Example configuration class
 *
 */
class ilElisCustomTrackingToolConfigGUI extends ilPluginConfigGUI
{
	private $cmd_class = null;
	private $default_cmd_class = 'ecttViewComTrackGUI';
	private $link_target;

	function __construct()
	{
		if( isset($_GET['class']) && strlen($_GET['class']) )
		{
			$this->cmd_class = $_GET['class'];
		}
		else
		{
			$this->cmd_class = $this->default_cmd_class;
		}

		$this->link_target = $this->buildLinkTarget($this->cmd_class);
	}

	/**
	 * Handles all commmands, default is "configure"
	 */
	function performCommand($cmd)
	{
		global $lng, $ilMainMenu,$tpl;

		$this->lng = $lng;

		switch($this->cmd_class)
		{
			case 'ecttViewComTrackGUI':

				$ilMainMenu->setActive('ectt_view_com_track');

				/*$this->setContentHeader(
					$this->lng->txt('ectt_menu_item')
				);*/
				ilElisCustomTrackingToolPlugin::_includeClass('class.ecttBaseGUI.php');
				ilElisCustomTrackingToolPlugin::_includeClass('class.ecttViewComTrackGUI.php');

				$gui = new ecttViewComTrackGUI($this->link_target, $cmd);
				$gui->executeCommand();

				break;
		}

		global $ilBench;
		$ilBench->save();
		$tpl->setTitle(ilElisCustomTrackingToolPlugin::_getInstance()->txt('tracking'));
	}

	private function buildLinkTarget($cmd_class, $cmd = '')
	{
		global $ilCtrl;
		$ilCtrl->setParameter($this,'class', $cmd_class);
		return $ilCtrl->getLinkTarget($this, $cmd);
	}

}
<?php

class ecttComTrackingTableGUI extends ecttTableGUI
{
	protected $ROW_TEMPLATE = 'tpl.ectt_com_tracking_table_row.html';
	protected $link_target;
	public function  __construct($link_target, $parent_cmd)
	{
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttTableGUI.php');
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttViewComTrack.php');
		
		$this->link_target = $link_target;
		$this->parent_cmd = $parent_cmd;

		global $ilCtrl;
		parent::__construct($link_target, $parent_cmd);

		$this->addColumn($this->txt("ectt_client_id"), "client_id", "10%");
		$this->addColumn($this->txt("ectt_com_type"), "com_type", "10%");
		$this->addColumn($this->txt("ectt_usr_login"), "usr_login", "10%");
		$this->addColumn($this->txt("ectt_com_content"), "com_content", "60%");
		$this->addColumn($this->txt("ectt_com_time"), "time", "10%");
		$this->setEnableHeader(true);
		$this->setRowTemplate($this->ROW_TEMPLATE, ilElisCustomTrackingToolPlugin::_getDir());
		$this->setFormAction($this->link_target.'&cmd=post');
		$this->setPrefix('communication_tracking');
		$this->setDefaultOrderField('client_id');
		$this->setTitle($this->txt('ectt_communication_tracking_list'));
	}

	protected function fillRow($a_set)
	{
		$time = ilDatePresentation::formatDate(new ilDateTime($a_set['time'], IL_CAL_UNIX));

		$this->tpl->setVariable("CLIENT_ID", $a_set['client_id']);
		$this->tpl->setVariable("COM_TYPE", $a_set['com_type']);
		$this->tpl->setVariable("CLIENT_USER", $a_set['usr_login']);
		$this->tpl->setVariable("COM_CONTENT", $a_set['com_content']);
		$this->tpl->setVariable("TIME", $time);
	}

	protected function formatField($field, $content)
	{

	}
	protected function getFieldClass($field)
	{

	}
	protected function initColumns($cols)
	{

	}
}

?>

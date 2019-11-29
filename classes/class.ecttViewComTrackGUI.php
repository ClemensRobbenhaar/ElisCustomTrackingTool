<?php

class ecttViewComTrackGUI extends ecttBaseGUI
{
	protected $default_cmd  = 'viewCommunicationTracking';
	private $toolbar;

	/**
	 * @var ecttViewComTrack
	 */
	private $viewComTrack	= null;

	protected function __getTabs()
	{
		global $ilTabs, $rbacreview, $ilUser;

		if( $rbacreview->isAssigned($ilUser->getId(), SYSTEM_ROLE_ID) )
		{
			$admin = true;
		}
		else
		{
			$admin = false;
		}

		$tabs = array();

		$tabs[] = array(
			'cmd' => 'viewCommunicationTracking',
			'langvar' => 'ectt_com_track'
		);

		$tabs[] = array(
			'cmd' => 'viewObjectTracking',
			'langvar' => 'ectt_obj_track'
		);

		return $tabs;
	}

	protected function __init()
	{
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttBaseGUI.php');
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttViewComTrack.php')	;
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttTableGUI.php');
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttObjTrackingTableGUI.php');
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttComTrackingTableGUI.php');
		ilElisCustomTrackingToolPlugin::_includeClass('class.ecttTrackingToolbarGUI.php');
		
		//$root_unit_id = ilOrgUnitTree::ROOT_UNIT_ID;
		$org_unit_tree = ilObjOrgUnitTree::_getInstance();
		$param = $this->parseInputParameters();
		
		$this->initViewComTrackModel($org_unit_tree, $param['filter']);
		$this->initTrackingToolbar();
	}

	protected function viewCommunicationTrackingCmd()
	{
		$table = new ecttComTrackingTableGUI(
				$this->getLinkTarget('post', true), 'viewCommunicationTracking'
		);

		$table->setExternalSegmentation(true);
		$table->setExternalSorting(true);
		$table->determineOffsetAndOrder();

		$orderfield	= $table->getOrderField();
		$orderdir	= $table->getOrderDirection();
		$offset		= $table->getOffset();

		$rows = $this->viewComTrack->getNumberOfRows(
				'filterCommunicationTrackingData', $orderfield,	$orderdir, $offset
		);

		$data = $this->viewComTrack->getCommunicationTrackingData(
				$orderfield, $orderdir, $offset
		);

		$table->setData($data);
		$table->setMaxCount($rows);

		$this->toolbar->setViewCmd('viewCommunicationTracking');
		$this->toolbar->setExportCmd('exportCommunicationTracking');
		$this->content->setTopContent($this->toolbar->getHTML());
		$this->content->setCenterContent($table->getHTML());
	}

	protected function viewObjectTrackingCmd()
	{
		$table = new ecttObjTrackingTableGUI(
			$this->getLinkTarget('post', true), 'viewObjectTracking'
		);
		
		$table->setExternalSegmentation(true);
		$table->setExternalSorting(true);
		$table->determineOffsetAndOrder();

		$orderfield = $table->getOrderField();
		$orderdir = $table->getOrderDirection();
		$offset = $table->getOffset();

		$rows = $this->viewComTrack->getNumberOfRows(
				'filterObjectTrackingData', $orderfield,
				$orderdir, $offset
		);

		$data = $this->viewComTrack->getObjectTrackingData(
				$orderfield, $orderdir, $offset
		);

		$table->setData($data);
		$table->setMaxCount($rows);

		$this->toolbar->setViewCmd('viewObjectTracking');
		$this->toolbar->setExportCmd('exportObjectTracking');
		$this->content->setTopContent($this->toolbar->getHTML());
		$this->content->setCenterContent($table->getHTML());
	}

	private function formatUnixTime($ut,$with_time = false)
	{
		global $lng;
		$format = $lng->txt('lang_dateformat');
		if($with_time)
		{
			$format .= (' '.$lng->txt('lang_timeformat_no_sec'));
		}
		return date($format,$ut);
	}

	protected function exportObjectTrackingCmd($iso = false)
	{
		$data = $this->viewComTrack->getObjectTrackingData();

		include_once "./Services/Utilities/classes/class.ilCSVWriter.php";
		include_once "./Services/Utilities/classes/class.ilUtil.php";

		$csv = new ilCSVWriter();
		$csv->setSeparator(";");

		foreach($data as $object)
		{
			$time = $this->formatUnixTime($object['time'], true);

			if($iso == true)
			{
				$object['client_id'] = utf8_decode($object['client_id']);
				$object['obj_title'] = utf8_decode($object['obj_title']);
				$object['usr_ip']	 = utf8_decode($object['usr_ip']);
				$time				 = utf8_decode($time);
			}
			
			$csv->addColumn($object['client_id']);
			$csv->addColumn($object['obj_title']);
			$csv->addColumn($object['usr_ip']);
			$csv->addColumn($time);
			$csv->addRow();
		}

		$a_data = $csv->getCSVString();
		$a_filename = 'tracked_objects.csv';
		$mime = 'text/comma-separated-values';
		
		if($iso == true) $charset = 'ISO-8859-1';
		else $charset = 'UTF-8';

		ilUtil::deliverData($a_data, $a_filename, $mime, $charset);
	}

	protected function exportCommunicationTrackingCmd($iso = false)
	{
		$data = $this->viewComTrack->getCommunicationTrackingData();

		include_once "./Services/Utilities/classes/class.ilCSVWriter.php";
		include_once "./Services/Utilities/classes/class.ilUtil.php";

		$csv = new ilCSVWriter();
		$csv->setSeparator(";");

		foreach($data as $object)
		{
			$time = $this->formatUnixTime($object['time'], true);

			if($iso == true)
			{
				$object['client_id']	= utf8_decode($object['client_id']);
				$object['com_type']		= utf8_decode($object['com_type']);
				$object['usr_login']	= utf8_decode($object['usr_login']);
				$object['com_content']	= utf8_decode($object['com_content']);
				$time					= utf8_decode($time);
			}

			$csv->addColumn($object['client_id']);
			$csv->addColumn($object['com_type']);
			$csv->addColumn($object['usr_login']);
			$csv->addColumn($object['com_content']);
			$csv->addColumn($time);
			$csv->addRow();
		}

		$a_data = $csv->getCSVString();
		$a_filename = 'tracked_objects.csv';
		$mime = 'text/comma-separated-values';

		if($iso == true) $charset = 'ISO-8859-1';
		else $charset = 'UTF-8';

		ilUtil::deliverData($a_data, $a_filename, $mime, $charset);
	}

	private function initViewComTrackModel($org_unit_tree, $filters)
	{
		$this->viewComTrack = new ecttViewComTrack($org_unit_tree);

		$this->viewComTrack->setFilteredObject(
				$filters[ecttTrackingToolbarGUI::FILTER_OBJECT]
		);

		$this->viewComTrack->setFilteredComType(
				$filters[ecttTrackingToolbarGUI::FILTER_COM_TYPE]
		);

		$this->viewComTrack->setFilteredClient(
				$filters[ecttTrackingToolbarGUI::FILTER_CLIENT]
		);

		$this->viewComTrack->setContentSearchtext(
				$filters[ecttTrackingToolbarGUI::FILTER_CONTENT_SEARCH]
		);

		$this->viewComTrack->setUsernameSearchtext(
				$filters[ecttTrackingToolbarGUI::FILTER_USERNAME_SEARCH]
		);

		$this->viewComTrack->setFilteredPeriodActive(
			$filters[ecttTrackingToolbarGUI::FILTER_PERIOD]['active']
		);

		$this->viewComTrack->setFilteredPeriodStart(
			$filters[ecttTrackingToolbarGUI::FILTER_PERIOD]['from']
		);

		$this->viewComTrack->setFilteredPeriodEnd(
			$filters[ecttTrackingToolbarGUI::FILTER_PERIOD]['to']
		);
	}

	private function parseInputParameters()
	{
		if( isset($_POST['filter']) && is_array($_POST['filter']) )
		{
			$_SESSION[$prefix]['filter'] = $_POST['filter'];
		}

		if( !is_array($_SESSION[$prefix]['filter']) )
		{
			$_SESSION[$prefix]['filter'] = array();
		}

		$selected_filter_settings = $_SESSION[$prefix]['filter'];

		// build and return parameter array

		$parameter = array(
			'filter' => $selected_filter_settings,
		);

		return $parameter;
	}

	private function initTrackingToolbar()
	{
		$this->toolbar = new ecttTrackingToolbarGUI(
			$this->getLinkTarget('post', true)
		);

		// object filter

		if( $this->getCmd() == 'viewObjectTracking')
		{
			$name = ecttTrackingToolbarGUI::FILTER_OBJECT;

			$this->toolbar->enableFilter($name);

			foreach($this->viewComTrack->getInvolvedObjects() as $object_id => $object_name)
			{
				$title = $object_name;

				$this->toolbar->addFilterOption(
					$name, $object_id, $title
				);
			}

			$this->toolbar->setFilterOptionSelected(
				$name, $this->viewComTrack->getFilteredObject()
			);
		}

		// communication-type filter

		if( $this->getCmd() == 'viewCommunicationTracking')
		{
			$name = ecttTrackingToolbarGUI::FILTER_COM_TYPE;

			$this->toolbar->enableFilter($name);

			foreach($this->viewComTrack->getInvolvedComTypes() as $com_type_id => $com_type_name)
			{
				$title = $com_type_name;

				$this->toolbar->addFilterOption(
					$name, $com_type_id, $title
				);
			}

			$this->toolbar->setFilterOptionSelected(
				$name, $this->viewComTrack->getFilteredComType()
			);
		}

		// Client Filter

		if( $this->getCmd() == 'viewCommunicationTracking')
		{
			$name = ecttTrackingToolbarGUI::FILTER_CLIENT;
			$this->toolbar->enableFilter($name);

			foreach($this->viewComTrack->getInvolvedClients() as $client)
			{
				$this->toolbar->addFilterOption(
					$name, $client, $client
				);
			}

			$this->toolbar->setFilterOptionSelected(
				$name, $this->viewComTrack->getFilteredClient()
			);
		}

		// Content-Search Filter

		if( $this->getCmd() == 'viewCommunicationTracking')
		{
			$name = ecttTrackingToolbarGUI::FILTER_CONTENT_SEARCH;
			$this->toolbar->enableFilter($name);

			$this->toolbar->setFilterSearchtext(
					$name, $this->viewComTrack->getContentSearchtext()
			);
		}

		// Username-Search Filter

		if( $this->getCmd() == 'viewCommunicationTracking')
		{
			$name = ecttTrackingToolbarGUI::FILTER_USERNAME_SEARCH;
			$this->toolbar->enableFilter($name);

			$this->toolbar->setFilterSearchtext(
					$name, $this->viewComTrack->getUsernameSearchtext()
			);
		}

		// period filter input

		$name = ecttTrackingToolbarGUI::FILTER_PERIOD;

		$this->toolbar->enableFilter($name);

		$this->toolbar->setFilterOptionSelected(
			$name, $this->viewComTrack->getFilteredPeriod()
		);
	}
}

?>

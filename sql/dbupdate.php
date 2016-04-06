<#1>
<?php
/**
 * @var ilDB $ilDB
 */
if (!$ilDB->tableExists("track_obj_data"))
{
	$ilDB->createTable("track_obj_data",
					   array(
						   "track_id" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   ),
						   "ref_id" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   ),
						   "obj_id" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   ),
						   "obj_type" => array(
							   "type" => "text", "length" => 50, "notnull" => true
						   ),
						   "obj_title" => array(
							   "type" => "text", "length" => 100, "notnull" => true
						   ),
						   "usr_id" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   ),
						   "usr_login" => array(
							   "type" => "text", "length" => 100, "notnull" => true
						   ),
						   "usr_ip" => array(
							   "type" => "text", "length" => 100, "notnull" => true
						   ),
						   "client_id" => array(
							   "type" => "text", "length" => 100, "notnull" => true
						   ),
						   "time" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   )
					   )
	);

	$ilDB->addPrimaryKey("track_obj_data", array("track_id"));
	$ilDB->addIndex('track_obj_data',array('obj_type'),'i1');
	$ilDB->addIndex('track_obj_data',array('time'),'i2');
	$ilDB->addIndex('track_obj_data',array('obj_type','time'),'i3');
	$ilDB->createSequence("track_obj_data");
}
?>
<#2>
<?php
if (!$ilDB->tableExists("track_com_data"))
{
	$ilDB->createTable("track_com_data",
					   array(
						   "track_id" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   ),
						   "usr_id" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   ),
						   "usr_login" => array(
							   "type" => "text", "length" => 100, "notnull" => true
						   ),
						   "client_id" => array(
							   "type" => "text", "length" => 100, "notnull" => true
						   ),
						   "com_content" => array(
							   "type" => "clob", "notnull" => false
						   ),
						   "com_type" => array(
							   "type" => "text", "length" => 20, "notnull" => true
						   ),
						   "time" => array(
							   "type" => "integer", "length" => 4, "notnull" => true
						   )
					   )
	);

	$ilDB->addPrimaryKey("track_com_data", array("track_id"));
	$ilDB->createSequence("track_com_data");
}

?>

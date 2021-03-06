<?php

/**
 * @Project NUKEVIET 3.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2010 - 2012 VINADES.,JSC. All rights reserved
 * @Createdate Sun, 08 Apr 2012 00:00:00 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

/**
 * valid_name_config()
 *
 * @param mixed $array_name
 * @return
 */
function valid_name_config( $array_name )
{
	$array_retutn = array();
	foreach( $array_name as $v )
	{
		$v = trim( $v );
		if( ! empty( $v ) and preg_match( "/^[a-z0-9\-\.\_]+$/", $v ) )
		{
			$array_retutn[] = $v;
		}
	}
	return $array_retutn;
}

$array_config = array();
$array_config_define = array();
if( $nv_Request->isset_request( 'submit', 'post' ) )
{
	$array_config_define['dir_forum'] = $nv_Request->get_string( 'dir_forum', 'post', 0 );
	if( ! is_dir( NV_ROOTDIR . '/' . $array_config_define['dir_forum'] . '/nukeviet' ) )
	{
		$array_config_define['dir_forum'] = '';
	}
	$array_config_define['nv_unickmin'] = $nv_Request->get_int( 'nv_unickmin', 'post', 3 );
	$array_config_define['nv_unickmax'] = $nv_Request->get_int( 'nv_unickmax', 'post', 100 );
	$array_config_define['nv_upassmin'] = $nv_Request->get_int( 'nv_upassmin', 'post', 5 );
	$array_config_define['nv_upassmax'] = $nv_Request->get_int( 'nv_upassmax', 'post', 255 );

	foreach( $array_config_define as $config_name => $config_value )
	{
		$db->sql_query( "REPLACE INTO `" . NV_CONFIG_GLOBALTABLE . "` (`lang`, `module`, `config_name`, `config_value`) VALUES ('sys', 'define', '" . mysql_real_escape_string( $config_name ) . "', " . $db->dbescape( $config_value ) . ")" );
	}

	$array_config['nv_upass_type'] = $nv_Request->get_int( 'nv_upass_type', 'post', 0 );
	$array_config['nv_unick_type'] = $nv_Request->get_int( 'nv_unick_type', 'post', 0 );
	$array_config['allowmailchange'] = $nv_Request->get_int( 'allowmailchange', 'post', 0 );
	$array_config['allowuserpublic'] = $nv_Request->get_int( 'allowuserpublic', 'post', 0 );
	$array_config['allowquestion'] = $nv_Request->get_int( 'allowquestion', 'post', 0 );
	$array_config['allowloginchange'] = $nv_Request->get_int( 'allowloginchange', 'post', 0 );
	$array_config['allowuserlogin'] = $nv_Request->get_int( 'allowuserlogin', 'post', 0 );
	$array_config['allowuserreg'] = $nv_Request->get_int( 'allowuserreg', 'post', 0 );
	$array_config['openid_mode'] = $nv_Request->get_int( 'openid_mode', 'post', 0 );
	$array_config['is_user_forum'] = $nv_Request->get_int( 'is_user_forum', 'post', 0 );
	$array_config['openid_servers'] = $nv_Request->get_typed_array( 'openid_servers', 'post', 'string' );
	$array_config['openid_servers'] = ! empty( $array_config['openid_servers'] ) ? implode( ",", $array_config['openid_servers'] ) : "";
	$array_config['whoviewuser'] = $nv_Request->get_int( 'whoviewuser', 'post', 0 );

	// Cau hinh cho facebook
	$array_config['facebook_client_id'] = filter_text_input( 'facebook_client_id', 'post', '' );
	$array_config['facebook_client_secret'] = filter_text_input( 'facebook_client_secret', 'post', '' );

	foreach( $array_config as $config_name => $config_value )
	{
		$query = "REPLACE INTO `" . NV_CONFIG_GLOBALTABLE . "` (`lang`, `module`, `config_name`, `config_value`) VALUES('sys', 'global', " . $db->dbescape( $config_name ) . ", " . $db->dbescape( $config_value ) . ")";
		$db->sql_query( $query );
	}

	$array_config['deny_email'] = filter_text_input( 'deny_email', 'post', '', 1 );

	if( ! empty( $array_config['deny_email'] ) )
	{
		$array_config['deny_email'] = valid_name_config( explode( ",", $array_config['deny_email'] ) );
		$array_config['deny_email'] = implode( "|", $array_config['deny_email'] );
	}

	$sql = "UPDATE `" . $db_config['dbsystem'] . "`.`" . NV_USERS_GLOBALTABLE . "_config` SET `content`=" . $db->dbescape( $array_config['deny_email'] ) . ", `edit_time`=" . NV_CURRENTTIME . " WHERE `config`='deny_email'";
	$db->sql_query( $sql );
	$array_config['deny_name'] = filter_text_input( 'deny_name', 'post', '', 1 );
	if( ! empty( $array_config['deny_name'] ) )
	{
		$array_config['deny_name'] = valid_name_config( explode( ",", $array_config['deny_name'] ) );
		$array_config['deny_name'] = implode( "|", $array_config['deny_name'] );
	}
	$sql = "UPDATE `" . $db_config['dbsystem'] . "`.`" . NV_USERS_GLOBALTABLE . "_config` SET `content`=" . $db->dbescape( $array_config['deny_name'] ) . ", `edit_time`=" . NV_CURRENTTIME . " WHERE `config`='deny_name'";
	$db->sql_query( $sql );

	$access_admin = array();
	$access_admin['access_addus'] = $nv_Request->get_typed_array( 'access_addus', 'post', 'bool' );
	$access_admin['access_waiting'] = $nv_Request->get_typed_array( 'access_waiting', 'post', 'bool' );
	$access_admin['access_editus'] = $nv_Request->get_typed_array( 'access_editus', 'post', 'bool' );
	$access_admin['access_delus'] = $nv_Request->get_typed_array( 'access_delus', 'post', 'bool' );
	$access_admin['access_passus'] = $nv_Request->get_typed_array( 'access_passus', 'post', 'bool' );
	$access_admin['access_groups'] = $nv_Request->get_typed_array( 'access_groups', 'post', 'bool' );
	$sql = "UPDATE `" . $db_config['dbsystem'] . "`.`" . NV_USERS_GLOBALTABLE . "_config` SET `content`='" . serialize( $access_admin ) . "', `edit_time`=" . NV_CURRENTTIME . " WHERE `config`='access_admin'";
	$db->sql_query( $sql );

	nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['ChangeConfigModule'], "", $admin_info['userid'] );
	nv_save_file_config_global();
	Header( "Location: " . NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op . "&rand=" . nv_genpass() );
	die();
}
else
{
	$array_config = $global_config;
}

$array_config['allowmailchange'] = ! empty( $array_config['allowmailchange'] ) ? " checked=\"checked\"" : "";
$array_config['allowuserpublic'] = ! empty( $array_config['allowuserpublic'] ) ? " checked=\"checked\"" : "";
$array_config['allowquestion'] = ! empty( $array_config['allowquestion'] ) ? " checked=\"checked\"" : "";
$array_config['allowloginchange'] = ! empty( $array_config['allowloginchange'] ) ? " checked=\"checked\"" : "";
$array_config['allowuserlogin'] = ! empty( $array_config['allowuserlogin'] ) ? " checked=\"checked\"" : "";
$array_config['openid_mode'] = ! empty( $array_config['openid_mode'] ) ? " checked=\"checked\"" : "";
$array_config['is_user_forum'] = ! empty( $array_config['is_user_forum'] ) ? " checked=\"checked\"" : "";
$servers = $array_config['openid_servers'];

$openid_servers = array();
include ( NV_ROOTDIR . '/includes/openid.php' );
$array_config['openid_servers'] = array();
if( ! empty( $openid_servers ) )
{
	$array_keys = array_keys( $openid_servers );
	foreach( $array_keys as $server )
	{
		$checked = ( ! empty( $servers ) and in_array( $server, $servers ) ) ? " checked=\"checked\"" : "";
		$array_config['openid_servers'][] = array( 'name' => $server, 'checked' => $checked );
	}
}
$sql = "SELECT `config`, `content` FROM `" . $db_config['dbsystem'] . "`.`" . NV_USERS_GLOBALTABLE . "_config` WHERE `config`='deny_email' OR `config`='deny_name'";
$result = $db->sql_query( $sql );
while( list( $config, $content ) = $db->sql_fetchrow( $result ) )
{
	$content = array_map( "trim", explode( "|", $content ) );
	$array_config[$config] = implode( ", ", $content );
}
$db->sql_freeresult();
$array_registertype = array(
	0 => $lang_module['active_not_allow'],
	1 => $lang_module['active_all'],
	2 => $lang_module['active_email'],
	3 => $lang_module['active_admin_check']
);
$array_whoview = array(
	0 => $lang_module['whoview_all'],
	1 => $lang_module['whoview_user'],
	2 => $lang_module['whoview_admin']
);

$ignorefolders = array( "", ".", "..", "index.html", ".htaccess" );

$xtpl = new XTemplate( "config.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'FORM_ACTION', NV_BASE_ADMINURL . "index.php?" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $op );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'DATA', $array_config );
if( ! in_array( DIR_FORUM, $ignorefolders ) and file_exists( NV_ROOTDIR . '/' . DIR_FORUM . '/nukeviet' ) )
{
	$forum_files = @scandir( NV_ROOTDIR . '/' . DIR_FORUM . '/nukeviet' );
	if( ! empty( $forum_files ) and in_array( 'is_user.php', $forum_files ) and in_array( 'changepass.php', $forum_files ) and in_array( 'editinfo.php', $forum_files ) and in_array( 'login.php', $forum_files ) and in_array( 'logout.php', $forum_files ) and in_array( 'lostpass.php', $forum_files ) and in_array( 'register.php', $forum_files ) )
	{
		$xtpl->parse( 'main.user_forum' );
	}
}

for( $id = 3; $id < 20; $id++ )
{
	$array = array(
		"id" => $id,
		"select" => ( NV_UNICKMIN == $id ) ? " selected=\"selected\"" : "",
		"value" => $id
	);
	$xtpl->assign( 'OPTION', $array );
	$xtpl->parse( 'main.nv_unickmin' );
}
for( $id = 20; $id < 100; $id++ )
{
	$array = array(
		"id" => $id,
		"select" => ( NV_UNICKMAX == $id ) ? " selected=\"selected\"" : "",
		"value" => $id
	);
	$xtpl->assign( 'OPTION', $array );
	$xtpl->parse( 'main.nv_unickmax' );
}

$lang_global['unick_type_0'] = $lang_module['unick_type_0'];
for( $id = 0; $id < 5; $id++ )
{
	$array = array(
		"id" => $id,
		"select" => ( $global_config['nv_unick_type'] == $id ) ? " selected=\"selected\"" : "",
		"value" => $lang_global['unick_type_' . $id]
	);
	$xtpl->assign( 'OPTION', $array );
	$xtpl->parse( 'main.nv_unick_type' );
}

for( $id = 5; $id < 20; $id++ )
{
	$array = array(
		"id" => $id,
		"select" => ( NV_UPASSMIN == $id ) ? " selected=\"selected\"" : "",
		"value" => $id
	);
	$xtpl->assign( 'OPTION', $array );
	$xtpl->parse( 'main.nv_upassmin' );
}
for( $id = 20; $id < 255; $id++ )
{
	$array = array(
		"id" => $id,
		"select" => ( NV_UPASSMAX == $id ) ? " selected=\"selected\"" : "",
		"value" => $id
	);
	$xtpl->assign( 'OPTION', $array );
	$xtpl->parse( 'main.nv_upassmax' );
}

$lang_global['upass_type_0'] = $lang_module['upass_type_0'];
for( $id = 0; $id < 5; $id++ )
{
	$array = array(
		"id" => $id,
		"select" => ( $global_config['nv_upass_type'] == $id ) ? " selected=\"selected\"" : "",
		"value" => $lang_global['upass_type_' . $id]
	);
	$xtpl->assign( 'OPTION', $array );
	$xtpl->parse( 'main.nv_upass_type' );
}

foreach( $array_registertype as $id => $titleregister )
{
	$array = array(
		"id" => $id,
		"select" => ( $array_config['allowuserreg'] == $id ) ? " selected=\"selected\"" : "",
		"value" => $titleregister
	);
	$xtpl->assign( 'REGISTERTYPE', $array );
	$xtpl->parse( 'main.registertype' );
}

$nv_files = @scandir( NV_ROOTDIR );
foreach( $nv_files as $value )
{
	if( ! in_array( $value, $ignorefolders ) and is_dir( NV_ROOTDIR . '/' . $value . '/nukeviet' ) )
	{
		$array = array(
			"id" => $value,
			"select" => ( $value == DIR_FORUM ) ? " selected=\"selected\"" : "",
			"value" => $value
		);
		$xtpl->assign( 'DIR_FORUM', $array );
		$xtpl->parse( 'main.dir_forum' );
	}
}

foreach( $array_whoview as $id => $titleregister )
{
	$select = ( $array_config['whoviewuser'] == $id ) ? " selected=\"selected\"" : "";
	$array = array(
		"id" => $id,
		"select" => $select,
		"value" => $titleregister
	);
	$xtpl->assign( 'WHOVIEW', $array );
	$xtpl->parse( 'main.whoviewlistuser' );
}
if( ! empty( $array_config['openid_servers'] ) )
{
	foreach( $array_config['openid_servers'] as $server )
	{
		$xtpl->assign( 'OPENID', $server );
		$xtpl->parse( 'main.openid_servers' );
	}
}
$array_access = array(
	array( 'id' => 1, 'title' => $lang_global['level1'] ),
	array( 'id' => 2, 'title' => $lang_global['level2'] ),
	array( 'id' => 3, 'title' => $lang_global['level3'] )
);
$i = 0;

foreach( $array_access as $access )
{
	$level = $access['id'];
	$access['class'] = ( ++$i % 2 == 0 ) ? ' class="second"' : '';
	$access['checked_addus'] = ( isset( $access_admin['access_addus'][$level] ) and $access_admin['access_addus'][$level] == 1 ) ? ' checked="checked" ' : '';
	$access['checked_waiting'] = ( isset( $access_admin['access_waiting'][$level] ) and $access_admin['access_waiting'][$level] == 1 ) ? ' checked="checked" ' : '';
	$access['checked_editus'] = ( isset( $access_admin['access_editus'][$level] ) and $access_admin['access_editus'][$level] == 1 ) ? ' checked="checked" ' : '';
	$access['checked_delus'] = ( isset( $access_admin['access_delus'][$level] ) and $access_admin['access_delus'][$level] == 1 ) ? ' checked="checked" ' : '';
	$access['checked_passus'] = ( isset( $access_admin['access_passus'][$level] ) and $access_admin['access_passus'][$level] == 1 ) ? ' checked="checked" ' : '';
	$access['checked_groups'] = ( isset( $access_admin['access_groups'][$level] ) and $access_admin['access_groups'][$level] == 1 ) ? ' checked="checked" ' : '';
	$xtpl->assign( 'ACCESS', $access );
	$xtpl->parse( 'main.access' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

$page_title = $lang_module['config'];

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_admin_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>
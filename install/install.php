<?php 
/*

UserFrosting Version: 0.2
By Alex Weissman
Copyright (c) 2014

Based on the UserCake user management system, v2.0.2.
Copyright (c) 2009-2012

UserFrosting, like UserCake, is 100% free and open-source.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

// This is the config file in the install directory.
require_once('config.php');

if (userIdExists('1')){
	addAlert("danger", lang("MASTER_ACCOUNT_EXISTS"));
	header('Location: complete.php');
	exit();
}

$db_issue = false;
$errors = array();
$successes = array();

$groups_sql = "
CREATE TABLE IF NOT EXISTS `".$db_table_prefix."groups` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(150) NOT NULL,
`is_default` tinyint(1) NOT NULL,
`can_delete` tinyint(1) NOT NULL,
`home_page_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
";

$groups_entry = "
INSERT INTO `".$db_table_prefix."groups` (`id`, `name`, `is_default`, `can_delete`, `home_page_id`) VALUES
(1, 'User', 1, 0, 4),
(2, 'Administrator', 0, 0, 5);
";

$users_sql = "
CREATE TABLE IF NOT EXISTS `".$db_table_prefix."users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_name` varchar(50) NOT NULL,
`display_name` varchar(50) NOT NULL,
`password` varchar(255) NOT NULL,
`email` varchar(150) NOT NULL,
`activation_token` varchar(225) NOT NULL,
`last_activation_request` int(11) NOT NULL,
`lost_password_request` tinyint(1) NOT NULL,
`active` tinyint(1) NOT NULL,
`title` varchar(150) NOT NULL,
`sign_up_stamp` int(11) NOT NULL,
`last_sign_in_stamp` int(11) NOT NULL,
`enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Specifies if the account is enabled.  Disabled accounts cannot be logged in to, but they retain all of their data and settings.',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";

$user_group_matches_sql = "
CREATE TABLE IF NOT EXISTS `".$db_table_prefix."user_group_matches` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`group_id` int(11) NOT NULL,
`is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Specifies if this is the primary group for the user.',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
";

// Add root acount as a user and administrator
$user_group_matches_entry = "
INSERT INTO `".$db_table_prefix."user_group_matches` (`id`, `user_id`, `group_id`, `is_primary`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 1);
";

$configuration_sql = "
CREATE TABLE IF NOT EXISTS `".$db_table_prefix."configuration` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(150) NOT NULL,
`value` varchar(150) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;
";

$configuration_entry = "
INSERT INTO `".$db_table_prefix."configuration` (`id`, `name`, `value`) VALUES
(1, 'website_name', 'UserFrosting'),
(2, 'website_url', 'localhost/'),
(3, 'email', 'noreply@myfrosting.com'),
(4, 'activation', '0'),
(5, 'resend_activation_threshold', '0'),
(6, 'language', 'models/languages/en.php'),
(7, 'template', 'models/site-templates/default.css'),
(8, 'can_register', '1'),
(9, 'new_user_title', 'New Member'),
(10, 'root_account_config_token', '" . md5(uniqid(mt_rand(), false)) . "');
";

$pages_sql = "CREATE TABLE IF NOT EXISTS `".$db_table_prefix."pages` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`page` varchar(150) NOT NULL,
`private` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;
";

$pages_entry = "INSERT INTO `".$db_table_prefix."pages` (`id`, `page`, `private`) VALUES
(1, 'account/includes.php', 0),
(2, 'account/header.php', 1),
(3, 'account/logout.php', 1),
(4, 'account/dashboard.php', 1),
(5, 'account/dashboard_admin.php', 1),
(6, 'account/account_settings.php', 1),
(7, 'account/site_pages.php', 1),
(8, 'account/site_settings.php', 1),
(9, 'account/users.php', 1),
(10, 'account/user_details.php', 1),
(11, 'account/load_form_user.php', 1),
(12, 'account/delete_user_dialog.php', 1);
";

$group_page_matches_sql = "CREATE TABLE IF NOT EXISTS `".$db_table_prefix."group_page_matches` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`group_id` int(11) NOT NULL,
`page_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;
";

$group_page_matches_entry = "INSERT INTO `".$db_table_prefix."group_page_matches` (`id`, `group_id`, `page_id`) VALUES
(1, 2, 1),
(2, 2, 2),
(3, 2, 3),
(4, 2, 4),
(5, 2, 5),
(6, 2, 6),
(7, 2, 7),
(8, 2, 8),
(9, 2, 9),
(10, 2, 10),
(11, 2, 11),
(12, 2, 12),
(13, 1, 1),
(14, 1, 2),
(15, 1, 3),
(16, 1, 4),
(17, 1, 6);
";

$user_action_permits_sql = "CREATE TABLE IF NOT EXISTS `".$db_table_prefix."user_action_permits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `permits` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;
";

// Sample action permits, should probably change these at some point since root user should automatically have permission for
// all actions
$user_action_permits_entry = "INSERT INTO `".$db_table_prefix."user_action_permits` (`id`, `user_id`, `action`, `permits`) VALUES
(1, 1, 'updateUserEmail', 'isLoggedInUser(user_id)&isActive(user_id)'),
(2, 1, 'loadUser', 'always()'),
(3, 1, 'loadUsers', 'always()'),
(4, 1, 'deleteUser', 'always()'),
(5, 1, 'activateUser', 'always()'),
(6, 1, 'loadGroups', 'always()'),
(7, 1, 'loadUserGroups', 'always()');
";

$db = pdoConnect();

$stmt = $db->prepare($configuration_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."configuration table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing ".$db_table_prefix."configuration table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($configuration_entry);
if($stmt->execute())
{
    $successes[] = "<p>Inserted basic config settings into ".$db_table_prefix."configuration table.....</p>";
}
else
{
    $errors[] = "<p>Error inserting config settings access.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($groups_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."groups table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing ".$db_table_prefix."groups table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($groups_entry);
if($stmt->execute())
{
    $successes[] = "<p>Inserted 'User' and 'Admin' groups into ".$db_table_prefix."groups table.....</p>";
}
else
{
    $errors[] = "<p>Error inserting groups.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($user_group_matches_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."user_group_matches table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing ".$db_table_prefix."user_group_matches table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($user_group_matches_entry);
if($stmt->execute())
{
    $successes[] = "<p>Added 'Admin' entry for first user in ".$db_table_prefix."user_group_matches table.....</p>";
}
else
{
    $errors[] = "<p>Error inserting admin into ".$db_table_prefix."user_group_matches.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($pages_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."pages table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing ".$db_table_prefix."pages table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($pages_entry);
if($stmt->execute())
{
    $successes[] = "<p>Added default pages to ".$db_table_prefix."pages table.....</p>";
}
else
{
    $errors[] = "<p>Error inserting pages into ".$db_table_prefix."pages.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($group_page_matches_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."group_page_matches table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing ".$db_table_prefix."group_page_matches table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($group_page_matches_entry);
if($stmt->execute())
{
    $successes[] = "<p>Added default access to ".$db_table_prefix."group_page_matches table.....</p>";
}
else
{
    $errors[] = "<p>Error adding default access to ".$db_table_prefix."user_group_matches.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($users_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."users table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing users table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($user_action_permits_sql);
if($stmt->execute())
{
    $successes[] = "<p>".$db_table_prefix."user_action_permits table created.....</p>";
}
else
{
    $errors[] = "<p>Error constructing user_action_permits table.</p>";
    $db_issue = true;
}

$stmt = $db->prepare($user_action_permits_entry);
if($stmt->execute())
{
    $successes[] = "<p>Added default access to ".$db_table_prefix."user_action_permits table.....</p>";
}
else
{
    $errors[] = "<p>Error adding default access to ".$db_table_prefix."user_action_permits.</p>";
    $db_issue = true;
}

$result = array();

if(!$db_issue) {
    $successes[] = "<p><strong>Database setup complete, please create the master (root) account.  The configuration token can be found in the 'uc_configuration' table of your database, as the value for 'root_account_config_token'.</strong></p>";
}
else
    $errors[] = "<p><strong>Database setup did not complete successfully.  Please delete all tables and try again.</strong></p>";

$result['errors'] = $errors;
$result['successes'] = $successes;
foreach ($errors as $error){
  addAlert("danger", $error);
}
foreach ($successes as $success){
  addAlert("success", $success);
}

if (count($errors) == 0)
    header('Location: register_root.php');
else
    header('Location: index.php');
exit();	

?>
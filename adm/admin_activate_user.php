<?php
/*

UserFrosting Version: 0.1
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

// Activate the specified user account
// Request method: POST

require_once("models/config.php");

// Recommended access level: admin only
set_error_handler('logAllErrors');

// Recommended admin-only access
if (!securePage($_SERVER['PHP_SELF'])){
  addAlert("danger", "Whoops, looks like you don't have permission to create a user.");
  if (isset($_POST['ajaxMode']) and $_POST['ajaxMode'] == "true" ){
	echo json_encode(array("errors" => 1, "successes" => 0));
  } else {
	header("Location: " . getReferralPage());
  }
  exit();
}

//Check if selected user exists
if(!isset($_POST['user_id']) or !userIdExists($_POST['user_id'])){
	$errors[] = lang("ACCOUNT_INVALID_USER_ID");
} else {
    // Required: id
    $id = $_POST['user_id'];
    
    $userdetails = fetchUserDetails(NULL, NULL, $id); //Fetch user details
    $displayname = $userdetails['display_name'];
    
    //Activate account
    if (setUserActive($userdetails['activation_token'])){
        $successes[] = lang("ACCOUNT_MANUALLY_ACTIVATED", array($displayname));
    }
    else {
        $errors[] = lang("SQL_ERROR");
    }
}

restore_error_handler();

foreach ($errors as $error){
  addAlert("danger", $error);
}
foreach ($successes as $success){
  addAlert("success", $success);
}

// Allows for functioning in either ajax mode or graceful degradation to PHP/HTML only
if (isset($_POST['ajaxMode']) and $_POST['ajaxMode'] == "true" ){
  echo json_encode(array(
	"errors" => count($errors),
	"successes" => count($successes)));
} else {
  header("Location: " . getReferralPage());
  exit();
}
?>

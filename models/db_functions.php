<?

/******************************************************************************************************************

Unsecured functions.  Use these only within secured functions, or when you are not directly rendering their outputs.

*******************************************************************************************************************/

//Functions that interact mainly with .users table
//------------------------------------------------------------------------------

/*****************  Basic user account status/info functions *******************/

// Determines whether or not there is a user logged in.  If there is an active session, but the user no longer exists in the database, return false.
function isUserLoggedIn() {
	global $loggedInUser, $db_table_prefix;
	if($loggedInUser == NULL){
		return false;//if $loggedInUser is null, we don't need to check the database. KISS
	}else{
        try {
            $db = pdoConnect();
            
            $sqlVars = array();        
        
            $query = "SELECT 
                id,
                password
                FROM {$db_table_prefix}users
                WHERE
                id = :user_id
                AND 
                password = :password 
                AND
                active = 1
                LIMIT 1";
            $stmt = $db->prepare($query);
            
            $sqlVars[':user_id'] = $loggedInUser->user_id;
            $sqlVars[':password'] = $loggedInUser->hash_pw;
    
            if (!$stmt->execute($sqlVars)){
                // Error: column does not exist
                return false;
            }
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row)
                return true;
            else {
                destroySession("userCakeUser");//user may have been deleted but a session lingers. delete it.
                return false;//not loggedin
            }
        } catch (PDOException $e) {
          addAlert("danger", "Oops, looks like our database encountered an error.");
          error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
          return false;
        } catch (ErrorException $e) {
          addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
          return false;
        } catch (RuntimeException $e) {
          addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
          error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
          return false;
        }
    }
}

//Check if a user ID exists in the DB
function userIdExists($id) {
    return userValueExists('id', $id);
}

//Checks if a username exists in the DB.  
function usernameExists($user_name) {
    return userValueExists('user_name', $user_name);
}

//Check if a display name exists in the DB.
function displayNameExists($display_name) {
    return userValueExists('display_name', $display_name);
}

//Check if an email exists in the DB
function emailExists($email) {
    return userValueExists('email', $email);
}

// Determine if a user with the specified value for a specified column exists.  Returns true if the username exists, false if not or on error.
function userValueExists($column, $data) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		$column = :data
		LIMIT 1";
        
        // This block allows return false if the table doesn't exist
        try {
            $stmt = $db->prepare($query);
        } catch (PDOException $e) {    
            return false;
        }
        
        $sqlVars[':data'] = $data;

        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

//Check if a user name and email belong to the same user
function emailUsernameLinked($email,$user_name) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT active
		FROM ".$db_table_prefix."users
		WHERE
		user_name = :user_name AND
        email = :email
		LIMIT 1";
        
        $stmt = $db->prepare($query);
        
        $sqlVars[':user_name'] = $user_name;
        $sqlVars[':email'] = $email;

        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

/*****************  User fetch data functions *******************/

// Fetch non-authorization related data for the specified user.
function fetchUser($user_id){
    try {
      global $db_table_prefix;
      
      $results = array();
      
      $db = pdoConnect();
      
      $sqlVars = array();
      
      $query = "select {$db_table_prefix}users.id as user_id, user_name, display_name, email, title, sign_up_stamp, last_sign_in_stamp, active, enabled from {$db_table_prefix}users where {$db_table_prefix}users.id = :user_id";
      
      $sqlVars[':user_id'] = $user_id;
      
      $stmt = $db->prepare($query);
      $stmt->execute($sqlVars);
      
      if (!($results = $stmt->fetch(PDO::FETCH_ASSOC))){
          addAlert("danger", "Invalid user id specified");
          return false;
      }
      
      $stmt = null;
    
      return $results;
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}


// Shortcut functions for fetchUserAuth by different parameters
function fetchUserAuthById($user_id){
    return fetchUserAuth('id', $user_id);
}

function fetchUserAuthByActivationToken($activation_token){
    return fetchUserAuth('activation_token', $activation_token);
}

function fetchUserAuthByUserName($user_name){
    return fetchUserAuth('user_name', $user_name);
}

// Similar to loadUser, except additionally loads authentication data including password hash and activation request data
function fetchUserAuth($column, $data){    
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT 
            id,
            user_name,
            display_name,
            password,
            email,
            activation_token,
            last_activation_request,
            lost_password_request,
            active,
            title,
            sign_up_stamp,
            last_sign_in_stamp,
            enabled
            FROM ".$db_table_prefix."users
            WHERE
            $column = :data
            LIMIT 1";
            
        $stmt = $db->prepare($query);
        
        $sqlVars[':data'] = $data;
        
        $stmt->execute($sqlVars);
          
        if (!($results = $stmt->fetch(PDO::FETCH_ASSOC))){
            // The user does not exist
            return false;
        }
        
        $stmt = null;
        return $results;
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

// Get the value of a specified field for a specified user
function fetchUserField($user_id, $field_name){    
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        // First, check that the specified field exists.  Very important as we are using other unsanitized data in the following query.
        $stmt_field_exists = $db->prepare("SHOW COLUMNS
            FROM ".$db_table_prefix."users
            LIKE :field_name");
        
        $sqlVars[':field_name'] = $field_name;
        
        $stmt_field_exists->execute($sqlVars);
        
        if (!($results = $stmt_field_exists->fetch(PDO::FETCH_ASSOC))){
            // The field does not exist
            return false;
        }

        $query = "SELECT 
            `$field_name`
            FROM ".$db_table_prefix."users
            WHERE
            id = :user_id
            LIMIT 1";
            
        $stmt = $db->prepare($query);
        
        $sqlVars[':user_id'] = $user_id;
        
        $stmt->execute($sqlVars);
          
        if (!($results = $stmt->fetch(PDO::FETCH_ASSOC))){
            // The user does not exist
            return false;
        }
        
        $stmt = null;
        return $results[$field_name];
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

// Fetch the appropriate menu for a user based on their primary group.  TODO: make this cacheable so it doesn't have to be processed each time a page is loaded.
// Hooks is an array of hook names mapped to their values
function fetchUserMenu($user_id, $hooks){
    // Get the user's primary group
    if (!($primary_group = fetchUserPrimaryGroup($user_id))){
        return null;
    }
    
    $group_id = $primary_group['id'];
    
    $path = MENU_TEMPLATES . "menu-" . $group_id . ".html";
    
	$contents = file_get_contents($path);
    
    //Check to see we can access the file / it has some contents
    if(!$contents || empty($contents)) {
          addAlert("danger", "The menu for this group could not be found.");
          return null;
    } else { 
        $find = array_keys($hooks);
        $replace = array_values($hooks);
        
        //Replace hooks
        $contents = str_replace($find, $replace, $contents);
        
        return $contents;
    }
}

// Fetch the primary group for the specified user
function fetchUserPrimaryGroup($user_id){
   try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT ".$db_table_prefix."groups.id as id, name 
            FROM ".$db_table_prefix."user_group_matches,".$db_table_prefix."groups
            WHERE user_id = :user_id and ".$db_table_prefix."user_group_matches.group_id = ".$db_table_prefix."groups.id and
            ".$db_table_prefix."user_group_matches.is_primary = 1 LIMIT 1";
        
        $stmt = $db->prepare($query);  

        $sqlVars[":user_id"] = $user_id;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return $row;
        else {
            addAlert("danger", "The user does not appear to have a primary group assigned.");
            return false;
        }
        
        $stmt = null;
        
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
        
}

// Fetch the home page for the specified user's primary group
function fetchUserHomePage($user_id){
   try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT page 
            FROM ".$db_table_prefix."user_group_matches,".$db_table_prefix."groups,".$db_table_prefix."pages 
            WHERE user_id = :user_id and ".$db_table_prefix."user_group_matches.group_id = ".$db_table_prefix."groups.id
             and ".$db_table_prefix."user_group_matches.is_primary = '1' and ".$db_table_prefix."pages.id = ".$db_table_prefix."groups.home_page_id LIMIT 1";
        
        $stmt = $db->prepare($query);  

        $sqlVars[":user_id"] = $user_id;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return $row['page'];
        else {
            addAlert("danger", "The user does not appear to have a primary group assigned.");
            return false;
        }
        
        $stmt = null;
        
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

/*****************  User account activation functions *******************/

// Change a user from inactive to active by providing the secret token
function setUserActive($token) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query = "UPDATE ".$db_table_prefix."users
            SET active = 1
            WHERE
            activation_token = :token
            LIMIT 1";
        
        $stmt = $db->prepare($query);
        
        $sqlVars[':token'] = $token;

        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            addAlert("danger", "Invalid token specified.");
            return false;
        }
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

//Check if activation token exists in DB, and that account is not already activated
function validateActivationToken($token) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
		$query = "SELECT active
			FROM ".$db_table_prefix."users
			WHERE active = 0
			AND
			activation_token = :token
			LIMIT 1";
  
        $stmt = $db->prepare($query);
        
        $sqlVars[':token'] = $token;

        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }    
}

//Input new activation token, and update the time of the most recent activation request
function updateLastActivationRequest($new_activation_token,$user_name,$email) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        $query = "UPDATE ".$db_table_prefix."users
            SET activation_token = :token,
            last_activation_request = :time
            WHERE email = :email
            AND
            user_name = :user_name";
    
        $stmt = $db->prepare($query);
        
        $sqlVars['token'] = $new_activation_token;
        $sqlVars['time'] = time();
        $sqlVars['email'] = $email;
        $sqlVars['user_name'] = $user_name;
        
        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

/*****************  User lost password functions *******************/

//Check if lost password token exists in DB, that user account is active and that there is an outstanding lost password request.
function validateLostPasswordToken($token) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
		$query = "SELECT active
			FROM ".$db_table_prefix."users
			WHERE active = 1
			AND
			activation_token = :token
			AND
			lost_password_request = 1 
			LIMIT 1";

        $stmt = $db->prepare($query);
        
        $sqlVars[':token'] = $token;

        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

//Toggle if lost password request flag on or off.  Return true on success, false on failure.
function flagLostPasswordRequest($user_name, $value) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
		$query = "UPDATE ".$db_table_prefix."users
		SET lost_password_request = :value
		WHERE
		user_name = :user_name
		LIMIT 1";
        
        $stmt = $db->prepare($query);
        
	    $sqlVars['value'] = $value;
        $sqlVars['user_name'] = $user_name;

        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

//Generate a random password, and new token
function updatePasswordFromToken($password, $current_token) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query = "UPDATE ".$db_table_prefix."users
            SET password = :password,
            activation_token = :new_token
            WHERE
            activation_token = :current_token";
        
		$stmt = $db->prepare($query);
        
	    $sqlVars[':password'] = $password;
        $sqlVars[':new_token'] = generateActivationToken();
        $sqlVars[':current_token'] = $current_token;
	
        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } catch (RuntimeException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    }
}

/*****************  User create and delete functions *******************/

// Add a user to the database
function addUser($user_name, $display_name, $title, $password, $email, $active, $activation_token){
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
            
        $query = "INSERT INTO ".$db_table_prefix."users (
            user_name,
            display_name,
            password,
            email,
            activation_token,
            last_activation_request,
            lost_password_request, 
            active,
            title,
            sign_up_stamp,
            last_sign_in_stamp
            )
            VALUES (
            :user_name,
            :display_name,
            :password,
            :email,
            :activation_token,
            '".time()."',
            '0',
            :active,
            :title,
            '".time()."',
            '0'
            )";
    
        $sqlVars = array(
            ':user_name' => $user_name,
            ':display_name' => $display_name,
            ':title' => $title,
            ':password' => $password,
            ':email' => $email,
            ':active' => $active,
            ':activation_token' => $activation_token
        );
    
        $stmt = $db->prepare($query);
    
        if (!$stmt->execute($sqlVars)){
            // Error: column does not exist
            return false;
        }
        
        $inserted_id = $db->lastInsertId();
        
        $stmt = null;
    
        return $inserted_id;

    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

// Update the last sign in for the specified user
function updateUserLastSignIn($user_id){
    updateUserField($user_id, 'last_sign_in_stamp', time());
}

// Update a field for a user with a given name and value
function updateUserField($user_id, $field_name, $field_value){
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();

        // Note that this function uses the field name directly in the query, so do not use unsanitized user input for this function!
        $query = "UPDATE ".$db_table_prefix."users
			SET
			$field_name = :field_value
			WHERE
			id = :user_id";
        
        $stmt = $db->prepare($query);
        
        $sqlVars[':user_id'] = $user_id;
        $sqlVars[':field_value'] = $field_value;
        
        $stmt->execute($sqlVars);
        
        if ($stmt->rowCount() > 0)
          return true;
        else {
          addAlert("danger", "Invalid user id specified.");
          return false;
        }
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

// Remove a user and associated group membership from DB
function removeUser($user_id){
    try {
      global $db_table_prefix;
      
      $db = pdoConnect();
      
      $sqlVars = array();
      
      $sqlVars[':user_id'] = $user_id;
      
      $query_user = "DELETE FROM ".$db_table_prefix."users WHERE id = :user_id";
      
      $stmt_user = $db->prepare($query_user);
      
      if (!($stmt_user->execute($sqlVars))){
          addAlert("danger", "Invalid user id specified");
          return false;
      }
      
      $query_perms = "DELETE FROM ".$db_table_prefix."user_group_matches WHERE user_id = :user_id";
            
      $stmt_perms = $db->prepare($query_perms);
      $stmt_perms->execute($sqlVars);
      
      $stmt_perms = null;
    
      if ($stmt_user->rowCount() > 0)
          return true;
      else {
          addAlert("danger", "Invalid user id specified.");
          return false;
      }
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}


//Functions that interact mainly with .groups table
//------------------------------------------------------------------------------

//Check if a group exists in the DB
function groupIdExists($group_id) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query = "SELECT id
		FROM ".$db_table_prefix."groups
		WHERE
		id = :group_id
		LIMIT 1";
        
        $stmt = $db->prepare($query);
        
        $sqlVars[':group_id'] = $group_id;

        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Check if a group name exists in the DB
function groupNameExists($name) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query = "SELECT id
		FROM ".$db_table_prefix."groups
		WHERE
		name = :name
		LIMIT 1";
        $stmt = $db->prepare($query);
        
        $sqlVars[':name'] = $name;

        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Retrieve information for all user groups
function fetchAllGroups() {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query = "SELECT 
            id,
            name,
            is_default,
            can_delete
            FROM ".$db_table_prefix."groups"; 
        
        $stmt = $db->prepare($query);

        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
      while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $id = $r['id'];
          $results[$id] = $r;
      }
      $stmt = null;
      
      return $results;
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Retrieve information for a group by id
function fetchGroupDetails($group_id) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query = "SELECT 
            id,
            name,
            is_default,
            can_delete 
            FROM ".$db_table_prefix."groups
            WHERE
            id = :group_id
            LIMIT 1";
	
        $stmt = $db->prepare($query);

        $sqlVars[':group_id'] = $group_id;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
        if (!($results = $stmt->fetch(PDO::FETCH_ASSOC)))
            return false;
            
        $stmt = null;
      
        return $results;
        
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }        
}

//Functions that interact mainly with .user_group_matches table
//------------------------------------------------------------------------------

// Check if the specified user is part of the specified group
function userInGroup($user_id, $group_id){
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();

        $query ="SELECT id 
			FROM ".$db_table_prefix."user_group_matches
			WHERE user_id = :user_id
			AND group_id = :group_id
			LIMIT 1
			";
        
        $stmt = $db->prepare($query);
        
        $sqlVars[':user_id'] = $user_id;
        $sqlVars[':group_id'] = $group_id;

        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row)
            return true;
        else
            return false;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } 


}

// Fetch group information for a specified user
function fetchUserGroups($user_id) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT ".$db_table_prefix."groups.id as id, name 
            FROM ".$db_table_prefix."user_group_matches,".$db_table_prefix."groups
            WHERE user_id = :user_id and ".$db_table_prefix."user_group_matches.group_id = ".$db_table_prefix."groups.id
            ";
        
        $stmt = $db->prepare($query);    

        $sqlVars[':user_id'] = $user_id;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $id = $r['id'];
              $results[$id] = $r;
        }
        $stmt = null;
          
        return $results;
          
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

// Add a user to the default groups.  TODO: check that user exists and isn't already assigned to group.
function addUserToDefaultGroups($user_id){
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();

        $query = "SELECT 
            id 
            FROM ".$db_table_prefix."groups where is_default='1'"; 
        
        $stmt = $db->prepare($query);

        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
        // Query to insert group membership
        $query_user = "INSERT INTO ".$db_table_prefix."user_group_matches (
		group_id,
		user_id
		)
		VALUES (
		:group_id,
		:user_id
		)";			
        
        $stmt_user = $db->prepare($query_user);
        
        // Insert match for each default group
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $group_id = $r['id'];
            $sqlVars = array(':group_id' => $group_id, ':user_id' => $user_id);
            $stmt_user->execute($sqlVars);   
        }
        $stmt = null;
      
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Match group(s) with user
function addUserToGroups($group_ids, $user_id) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "INSERT INTO ".$db_table_prefix."user_group_matches (
		group_id,
		user_id
		)
		VALUES (
		:group_id,
		:user_id
		)";
        
        $stmt->prepare($query);
        
        if (is_array($group_ids)){
            foreach($group_ids as $id){
                $sqlVars = array(':group_id' => $id, ':user_id' => $user_id);
                $stmt->execute($sqlVars);
                $i++;
            }
        } else {
            $sqlVars = array(':group_id' => $group_ids, ':user_id' => $user_id);
            $stmt->execute($sqlVars);
            $i++;
        }
        $stmt = null;
        return $i;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }        
}

//Unmatch group(s) from a user
function removeUserFromGroups($group_ids, $user) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "DELETE FROM ".$db_table_prefix."user_group_matches 
		WHERE group_id = :group_id
		AND user_id = :user_id";
        
        $stmt->prepare($query);
        
        if (is_array($group_ids)){
            foreach($group_ids as $id){
                $sqlVars = array(':group_id' => $id, ':user_id' => $user_id);
                $stmt->execute($sqlVars);
                $i++;
            }
        } else {
            $sqlVars = array(':group_id' => $group_ids, ':user_id' => $user_id);
            $stmt->execute($sqlVars);
            $i++;
        }
        $stmt = null;
        return $i;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } 
}

// TODO: Match user(s) to a group
function addUsersToGroup($group_id, $user_ids){

}

// TODO: Unmatch user(s) from a group
function removeUsersFromGroup($group_id, $user_ids){

}

//Functions that interact mainly with .configuration table
//------------------------------------------------------------------------------

// Fetch the value of a configuration parameter by name
function fetchConfigParameter($name){
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "SELECT id, value
		FROM ".$db_table_prefix."configuration WHERE name = :name";	
        
        if (!$stmt = $db->prepare($query))
            return false;

        $sqlVars[":name"] = $name;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return $row['value'];
        else {
            addAlert("danger", "The specified configuration parameter could not be found.");
            return false;
        }
        
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } 	
}

// Retrieve an array containing all site configuration parameters
function fetchConfigParameters(){
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "SELECT id, name, value
        FROM ".$db_table_prefix."configuration";
        
        $stmt = $db->prepare($query);    
        
        if (!$stmt->execute()){
            // Error
            return false;
        }
            
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = $r['name'];
            $value = $r['value'];
            $results[$name] = $value;
        }
        $stmt = null;
          
        return $results;
          
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Update configuration table with array of values mapped setting name => setting value
function updateConfig($settings) {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "UPDATE ".$db_table_prefix."configuration
            SET 
            value = :value
            WHERE
            name = :name";
        
        $stmt = $db->prepare($query);    
        
        foreach ($settings as $name => $value){
            $sqlVars = array(':name' => $name, ':value' => $value);
            $stmt->execute($sqlVars);
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
        
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }         
}

// Delete a specified configuration parameter (by name)
function deleteConfigParameter($name){
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "DELETE
		FROM ".$db_table_prefix."configuration WHERE name = :name";	
	
        if (!$stmt = $db->prepare($query))
            return false;

        $sqlVars[":name"] = $name;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
        
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }  
}

//Functions that interact mainly with .pages table
//------------------------------------------------------------------------------
//Check if a page ID exists
function pageIdExists($page_id) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query ="SELECT private
		FROM ".$db_table_prefix."pages
		WHERE
		id = :page_id
		LIMIT 1";
    
        $stmt = $db->prepare($query);
        
        $sqlVars[':page_id'] = $page_id;
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return true;
        else {
            return false;
        }
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Fetch information on all pages
function fetchAllPages() {
    try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $query = "SELECT 
            id,
            page,
            private
            FROM ".$db_table_prefix."pages";
        
        $stmt = $db->prepare($query);

        if (!$stmt->execute()){
            // Error
            return false;
        }
        
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $page = $r['page'];
          $results[$page] = $r;
        }
        $stmt = null;
      
        return $results;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Fetch information for a specific page by id
function fetchPageDetails($page_id) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT 
            id,
            page,
            private
            FROM ".$db_table_prefix."pages
            WHERE
            id = :page_id
            LIMIT 1";
        
        $stmt = $db->prepare($query);

        $sqlVars[":page_id"] = $page_id;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return $row;
        else {
            addAlert("danger", "The specified page details could not be found.");
            return false;
        }    
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Fetch information for a specific page by name
function fetchPageDetailsByName($name){
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT 
            id,
            page,
            private
            FROM ".$db_table_prefix."pages
            WHERE
            page = :name
            LIMIT 1";
        
        $stmt = $db->prepare($query);

        $sqlVars[":name"] = $name;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return $row;
        else {
            addAlert("danger", "The specified page details could not be found.");
            return false;
        }    
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}


//Add pages [array] to the DB
function createPages($pages) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $query = "INSERT INTO ".$db_table_prefix."pages (
		page
		)
		VALUES (
		:page
		)";
        
        $stmt = $db->prepare($query);    
        
        foreach ($pages as $page){
            $sqlVars = array(':page' => $page);
            $stmt->execute($sqlVars);
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }        
}

//Toggle private/public setting of a page.  1=private, 0=public
function updatePrivate($page_id, $private) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "UPDATE ".$db_table_prefix."pages
		SET 
		private = :private
		WHERE
		id = :page_id";
        
        $stmt = $db->prepare($query);
        
        $sqlVars[':private'] = $private;
        $sqlVars[':page_id'] = $page_id;
    
        $stmt->execute();
            
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Delete pages [array] from the DB
function deletePages($pages) {
    try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $stmt = $db->prepare("DELETE FROM ".$db_table_prefix."pages 
		WHERE id = :page_id");
        
        
        $stmt2 = $db->prepare("DELETE FROM ".$db_table_prefix."group_page_matches 
		WHERE page_id = :page_id");
        
        foreach($pages as $id){
            $sqlVars = array(':page_id' => $id);
            $stmt->execute($sqlVars);
            $stmt2->execute($sqlVars);
        }
        
        if ($stmt->rowCount() > 0)
            return true;
        else {
            return false;
        } 
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    } 
}

//Functions that interact mainly with .group_page_matches table
//------------------------------------------------------------------------------

// Check whether a particular user has access to a particular page
function userPageMatchExists($user_id, $page_id){
   try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT page_id 
            FROM ".$db_table_prefix."user_group_matches,".$db_table_prefix."group_page_matches 
            WHERE ".$db_table_prefix."user_group_matches.user_id = :user_id and ".
                    $db_table_prefix."user_group_matches.group_id = ".$db_table_prefix."group_page_matches.group_id and ".
                    $db_table_prefix."group_page_matches.page_id = :page_id LIMIT 1";
        
        $stmt = $db->prepare($query);  

        $sqlVars[":user_id"] = $user_id;
        $sqlVars[":page_id"] = $page_id;
        
        if (!$stmt->execute($sqlVars)){
            // Error
            return false;
        }
            
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if ($row)
            return true;
        else {
            addAlert("danger", "The specified user does not have access to this page.");
            return false;
        }
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Retrieve list of groups that can access a page
function fetchPageGroups($page_id) {
   try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT
            id,
            group_id
            FROM ".$db_table_prefix."group_page_matches
            WHERE page_id = :page_id
            ";
        $stmt = $db->prepare($query);
    
        $sqlVars[':page_id'] = $page_id;
    
        if (!$stmt->execute()){
            // Error
            return false;
        }
        
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $group_id = $r['group_id'];
          $results[$group_id] = $r;
        }
        $stmt = null;
      
        return $results;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Retrieve list of pages that a group can access
function fetchGroupPages($group_id) {
   try {
        global $db_table_prefix;
        
        $results = array();
        
        $db = pdoConnect();
        
        $sqlVars = array();
        
        $query = "SELECT
		id,
		page_id
		FROM ".$db_table_prefix."group_page_matches
		WHERE group_id = :group_id
		";
        
        $stmt = $db->prepare($query);
    
        $sqlVars[':page_id'] = $page_id;
    
        if (!$stmt->execute()){
            // Error
            return false;
        }
        
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $page_id = $r['page_id'];
          $results[$page_id] = $r;
        }
        $stmt = null;
      
        return $results;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Match group with page(s)
function addPage($page_ids, $group_id) {
   try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $i = 0;
        $query = "INSERT INTO ".$db_table_prefix."group_page_matches (
            group_id,
            page_id
            )
            VALUES (
            :group_id,
            :page_id
            )";
    
        $stmt->prepare($query);
        
        if (is_array($page_ids)){
            foreach($page_ids as $id){
                $sqlVars = array(':group_id' => $group_id, ':page_id' => $id);
                $stmt->execute($sqlVars);
                $i++;
            }
        } else {
            $sqlVars = array(':group_id' => $group_id, ':page_id' => $page_ids);
            $stmt->execute($sqlVars);
            $i++;
        }
        $stmt = null;
        return $i;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

//Unmatch group from page(s)
function removePage($page_ids, $group_id) {
   try {
        global $db_table_prefix;
        
        $db = pdoConnect();
        
        $i = 0;
        $query = "DELETE FROM ".$db_table_prefix."group_page_matches 
		WHERE page_id = :page_id
		AND group_id = :group_id";
        
        $stmt->prepare($query);
        
        if (is_array($page_ids)){
            foreach($page_ids as $id){
                $sqlVars = array(':group_id' => $group_id, ':page_id' => $id);
                $stmt->execute($sqlVars);
                $i++;
            }
        } else {
            $sqlVars = array(':group_id' => $group_id, ':page_id' => $page_ids);
            $stmt->execute($sqlVars);
            $i++;
        }
        $stmt = null;
        return $i;
    } catch (PDOException $e) {
      addAlert("danger", "Oops, looks like our database encountered an error.");
      error_log("Error in " . $e->getFile() . " on line " . $e->getLine() . ": " . $e->getMessage());
      return false;
    } catch (ErrorException $e) {
      addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
      return false;
    }
}

?>
<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Account\Model;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Model\UFModel;
use UserFrosting\Sprinkle\Account\Authorize\AccessConditionExpression;
use UserFrosting\Sprinkle\Account\Model\Collection\UserCollection;
use UserFrosting\Sprinkle\Account\Util\Password;

/**
 * User Class
 *
 * Represents a User object as stored in the database.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 * @property int id
 * @property string user_name
 * @property string first_name
 * @property string last_name 
 * @property string email
 * @property string locale
 * @property int group_id
 * @property int secret_token
 * @property int flag_verified
 * @property int flag_enabled
 * @property int flag_password_reset
 * @property timestamp created_at
 * @property timestamp updated_at
 * @property string password
 */
class User extends UFModel
{
    
    /**
     * @var string The name of the table for the current model.
     */ 
    protected $table = "users";
    
    protected $fillable = [
        "user_name",
        "first_name",
        "last_name",
        "email",
        "locale",
        "group_id",
        "secret_token",
        "flag_verified",
        "flag_enabled",
        "flag_password_reset",
        "password"
    ];
        
    /**
     * @var Activity[] An array of activities to be inserted for this User when save is called.
     */
    protected $new_events = [];
    
    /**
     * @var bool Enable timestamps for Users.
     */ 
    public $timestamps = true;    
    
    /**
     * Determine if the property for this object exists.
     * We add relations here so that Twig will be able to find them.
     * See http://stackoverflow.com/questions/29514081/cannot-access-eloquent-attributes-on-twig/35908957#35908957
     * Every property in __get must also be implemented here for Twig to recognize it.
     * @param string $name the name of the property to check.
     * @return bool true if the property is defined, false otherwise.
     */ 
    public function __isset($name)
    {
        if (in_array($name, [
                'group',
                'last_sign_in_activity',
                'last_sign_in_time',
                'sign_up_time',
                'last_password_reset_time',
                'last_verification_request_time'
            ])) {
            return true;
        } else {
            return parent::__isset($name);
        }
    }
    
    /**
     * Get a property for this object.
     *
     * @param string $name the name of the property to retrieve.
     * @throws Exception the property does not exist for this object.
     * @return string the associated property.
     */
    public function __get($name)
    {
        if ($name == 'last_sign_in_activity') {
            return $this->lastActivity('sign_in');
        } else if ($name == 'last_sign_in_time') {
            return $this->lastActivityTime('sign_in');
        } else if ($name == 'sign_up_time') {
            return $this->lastActivityTime('sign_up');
        } else if ($name == 'last_password_reset_time') {
            return $this->lastActivityTime('password_reset_request');
        } else if ($name == 'last_verification_request_time') {
            return $this->lastActivityTime('verification_request');
        } else {
            return parent::__get($name);
        }
    }    
    
    /**
     * Get all activities for this user.
     */    
    public function activities()
    {
        return $this->hasMany('UserFrosting\Sprinkle\Account\Model\Activity');
    }
    
    /**
     * Checks whether or not this user has access for a particular authorization hook.
     *
     * Determine if this user has access to the given $hook under the given $params.
     * @param string $hook The authorization hook to check for access.
     * @param array $params[optional] An array of field names => values, specifying any additional data to provide the authorization module
     * when determining whether or not this user has access.
     * @return boolean True if the user has access, false otherwise.
     */ 
    public function checkAccess($hook, $params = [])
    {
        if ($this->isGuest()) {   // TODO: do we sometimes want to allow access to protected resources for guests?  Should we model a "guest" group?
            return false;
        }
    
        // The master (root) account has access to everything.
        // Need to use loose comparison for now, because some DBs return `id` as a string.
        
        if ($this->id == static::$ci->config['reserved_user_ids.master']) {  
            return true;
        }
        
        $pass = false;
        
        // Find all permissions that apply to this user (via roles), and check if any evaluate to true.
        if (!$pass) {
            $ace = new AccessConditionExpression($this, static::$ci->config['debug.auth']);
            $permissions = $this->permissions($hook)->get();
            
            if (!empty($permissions)) {
                foreach ($permissions as $permission) {
                    $pass = $ace->evaluateCondition($permission->conditions, $params);
                    if ($pass) {
                        break;
                    }
                }
            }
        }
        
        return $pass;
    }
    
    /**
     * Delete this user from the database, along with any linked roles and activities.
     *
     * @return bool true if the deletion was successful, false otherwise.
     */
    public function delete()
    {
        // Remove all role associations
        $this->roles()->detach();
        
        // Remove all user activities
        Activity::where("user_id", $this->id)->delete();
        
        // Delete the user
        $result = parent::delete();
        
        return $result;
    }
    
    /**
     * Return this user's group.
     */
    public function group()
    {
        return $this->belongsTo('\UserFrosting\Sprinkle\Account\Model\Group', 'group_id');
    }
    
    /**
     * Determine whether or not this User object is a guest user (id set to `user_id_guest`) or an authenticated user.
     *
     * @return boolean True if the user is a guest, false otherwise.
     */ 
    public function isGuest()
    {
        if (!isset($this->id) || $this->id == static::$ci->config['reserved_user_ids.guest'])   // Need to use loose comparison for now, because some DBs return `id` as a string
            return true;
        else
            return false;
    }
    
    /**
     * @todo
     */ 
    public static function isLoggedIn()
    {
        // TODO.  Not sure how to implement this right now.  Flag in DB?  Or, check sessions?
    }
       
    /**
     * Get the most recent activity of a specified type for this user.
     *
     * @return Activity
     */    
    public function lastActivity($type)
    {
        return $this->activities()
            ->where('type', $type)
            ->orderBy('occurred_at', 'desc')
        ->first();
    }
    
    /**
     * Get the most recent time for a specified activity type for this user.
     *
     * @return string|null The last activity time, as a SQL formatted time (YYYY-MM-DD HH:MM:SS), or null if an activity of this type doesn't exist.
     */     
    public function lastActivityTime($type)
    {
        $result = $this->activities()
            ->where('type', $type)
            ->max('occurred_at');
        return $result ? $result : null;
    }    
    
    /**
     * Extends Eloquent's Collection models.
     *
     * @return UserCollection
     */
    public function newCollection(array $models = Array())
    {
	    return new UserCollection($models);
    }
    
    /**
     * Create a new password reset request activity.  Also, generates a new secret token.
     *
     * @return Activity
     */
    public function newActivityPasswordReset()
    {
        $this->secret_token = User::generateActivationToken();
        $this->flag_password_reset = "1";
        $activity = new Activity([
            "type"  => "password_reset_request",
            "description" => "User {$this->user_name} requested a password reset on " . date("Y-m-d H:i:s") . "."
        ]);
        $this->new_events[] = $activity;
        return $activity;
    }
        
    /**
     * Create a new user sign-in activity.
     *
     * @return Activity
     */
    public function newActivitySignIn()
    {    
        $activity = new Activity([
            "type"  => "sign_in",
            "description" => "User {$this->user_name} signed in at " . date("Y-m-d H:i:s") . "."
        ]);
        $this->new_events[] = $activity;
        return $activity;
    }
    
    /**
     * Create an activity saying that this user registered their account, or an account was created for them.
     * 
     * @param User $creator optional The User who created this account.  If set, this will be recorded in the activity description.
     * @return Activity     
     */
    public function newActivitySignUp($creator = null){
        if ($creator)
            $description = "User {$this->user_name} was created by {$creator->user_name} on " . date("Y-m-d H:i:s") . ".";
        else
            $description = "User {$this->user_name} successfully registered on " . date("Y-m-d H:i:s") . ".";
        $activity = new Activity([
            "type"  => "sign_up",
            "description" => $description
        ]);
        $this->new_events[] = $activity;
        return $activity;
    }
    
    /**
     * Create a new email verification request activity.  Also, generates a new secret token.
     *
     * @return Activity
     */
    public function newActivityVerificationRequest(){
        $this->secret_token = User::generateActivationToken();
        $activity = new Activity([
            "type"  => "verification_request",
            "description" => "User {$this->user_name} requested verification on " . date("Y-m-d H:i:s") . "."
        ]);
        $this->new_events[] = $activity;
        return $activity;
    }
    
    /**
     * Performs tasks to be done after this user has been successfully authenticated.
     *
     * By default, adds a new sign-in activity and updates any legacy hash.
     * @param mixed[] $params Optional array of parameters used for this event handler.
     * @todo Introduce a debug logging service
     */
    public function onLogin($params = array())
    {
        // Add a sign in activity (time is automatically set by database)
        $this->newActivitySignIn();
        
        // Update password if we had encountered an outdated hash
        $passwordType = Password::getHashType($this->password);
        
        if ($passwordType != "modern") {
            if (!isset($params['password'])) {
                error_log("Notice: Unhashed password must be supplied to update to modern password hashing.");
            } else {
                // Hash the user's password and update
                $passwordHash = Password::hash($params['password']);
                if ($passwordHash === null) {
                    error_log("Notice: outdated password hash could not be updated because the new hashing algorithm is not supported.  Are you running PHP >= 5.3.7?");
                } else {
                    $this->password = $passwordHash;
                    error_log("Notice: outdated password hash has been automatically updated to modern hashing.");
                }
            }
        }
        
        // Save changes
        $this->save();
        
        return $this;
    }    
    
    /**
     * Get all of the permissions this user has, via its roles.
     * @todo Turn this into a full-fledged custom relation?
     */
    public function permissions($hook = null)
    {
        $result = Capsule::table('permissions')
            ->select(
                'permissions.id as id',
                'roles.id as role_id',
                'permissions.slug as slug',
                'permissions.name as name',
                'conditions',
                'permissions.description as description')
            ->join('permission_roles', 'permissions.id', '=', 'permission_roles.permission_id')
            ->join('roles', 'permission_roles.role_id', '=', 'roles.id')
            ->join('role_users', 'role_users.role_id', '=', 'roles.id')
            ->where('role_users.user_id', '=', $this->id);
            
        if ($hook) {
            $result = $result->where('permissions.slug', $hook);
        }
        
        return $result;
    }    
    
    /**
     * Get all roles to which this user belongs.
     *
     */       
    public function roles()
    {
        return $this->belongsToMany('UserFrosting\Sprinkle\Account\Model\Role', 'role_users');
    }
    
    /**
     * Store the User to the database, along with any group associations and new activitys, updating as necessary.
     *
     */
    public function save(array $options = []){       
        // Update the user record itself
        $result = parent::save($options);
        
        // Save any new activitys for this user
        foreach ($this->new_events as $activity){
            $this->activities()->save($activity);
        }
        
        return $result;
    }
    
    /**
     * Generate an activation token for a user.
     *
     * This generates a token to use for activating a new account, resetting a lost password, etc.
     * @param string $gen specify an existing token that, if we happen to generate the same value, we should regenerate on.
     * @return string
     */
    public static function generateActivationToken($gen = null)
    {
        do {
            $gen = md5(uniqid(mt_rand(), false));
        } while(User::where('secret_token', $gen)->first());
        return $gen;
    }    
    
}

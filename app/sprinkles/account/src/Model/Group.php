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

/**
 * Group Class
 *
 * Represents a group object as stored in the database.
 *
 * @package UserFrosting
 * @author Alex Weissman
 * @see http://www.userfrosting.com/tutorials/lesson-3-data-model/
 *
 * @property string name
 * @property string description
 * @property string theme
 * @property string landing_page
 * @property string icon
 */
class Group extends UFModel {

    /**
     * @var string The name of the table for the current model.
     */ 
    protected $table = "groups";
    
    protected $fillable = [
        "name",
        "description",
        "theme",
        "landing_page",
        "icon"
    ];
    
    /**
     * Delete this group from the database, along with any user associations
     *
     * @todo What do we do with users when their group is deleted?  Reassign them?  Or, can a user be "groupless"?
     */
    public function delete()
    {
        // Remove all user associations
        $this->users()->detach();
        
        /*
        // Reassign any primary users to the current default primary group
        $default_primary_group = Group::where('is_default', GROUP_DEFAULT_PRIMARY)->first();
        
        Capsule::table('user')->where('primary_group_id', $this->id)->update(["primary_group_id" => $default_primary_group->id]);
        */
        
        // Delete the group        
        $result = parent::delete();
        
        return $result;
    }
    
    /**
     * Lazily load a collection of Users which belong to this group.
     */ 
    public function users()
    {
        return $this->hasMany('\UserFrosting\Sprinkle\Account\Model\User');
    }
}

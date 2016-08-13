<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Account\ServicesProvider;

use Birke\Rememberme\Authenticator as RememberMe;
use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager;
use UserFrosting\Sprinkle\Account\Model\User;
use UserFrosting\Sprinkle\Account\Twig\AccountExtension;

/**
 * Registers services for the account sprinkle, such as currentUser, etc.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AccountServicesProvider
{
    /**
     * Register UserFrosting's account services.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container)
    {
        /**
         * Extends the 'errorHandler' service with custom exception handlers.
         *
         * Custom handlers added: ForbiddenExceptionHandler
         */
        $container->extend('errorHandler', function ($handler, $c) {
            // Register the ForbiddenExceptionHandler.
            $handler->registerHandler('\UserFrosting\Support\Exception\ForbiddenException', '\UserFrosting\Sprinkle\Account\Handler\ForbiddenExceptionHandler');
            
            return $handler;
        });
        
        /**
         * Extends the 'view' service with the AccountExtension for Twig.
         *
         * Adds account-specific functions, globals, filters, etc to Twig.
         */
        $container->extend('view', function ($view, $c) {
            $twig = $view->getEnvironment(); 
            $extension = new AccountExtension($c);
            $twig->addExtension($extension);
            
            return $view;
        });
        
        $container['authenticator'] = function ($c) {
            $config = $c->get('config');
            $session = $c->get('session');
            
            // Force database connection to boot up
            $c->get('db');            
            
            // Fix RememberMe table name
            $config['remember_me.table.tableName'] = Capsule::connection()->getTablePrefix() . $config['remember_me.table.tableName'];          
            
            $authenticator = new Authenticator($session, $config);
            return $authenticator;
        };
        
        $container['authorizer'] = function ($c) {
            // Default access condition callbacks.  Add more in your sprinkle by using $container->extend(...) 
            $callbacks = [
                /**
                 * Unconditionally grant permission - use carefully!
                 * @return bool returns true no matter what.
                 */
                'always' => function () {
                    return true;
                },
                
                /**
                 * Check if the specified values are identical to one another (strict comparison).
                 * @param mixed $val1 the first value to compare.
                 * @param mixed $val2 the second value to compare.     
                 * @return bool true if the values are strictly equal, false otherwise.
                 */    
                'equals' => function ($val1, $val2) {
                    return ($val1 === $val2);
                },
                
                /**
                 * Check if the specified values are numeric, and if so, if they are equal to each other.
                 * @param mixed $val1 the first value to compare.
                 * @param mixed $val2 the second value to compare.     
                 * @return bool true if the values are numeric and equal, false otherwise.
                 */     
                'equals_num' => function ($val1, $val2) {
                    if (!is_numeric($val1)) {
                        return false;
                    }
                    if (!is_numeric($val2)) {
                        return false;
                    }
                    
                    return ($val1 == $val2);
                },
                
                /**
                 * Check if the specified value $needle is in the values of $haystack.
                 *
                 * @param mixed $needle the value to look for in $haystack
                 * @param array[mixed] $haystack the array of values to search.    
                 * @return bool true if $needle is present in the values of $haystack, false otherwise.
                 */      
                'in' => function ($needle, $haystack) {
                    return in_array($needle, $haystack);
                },
                
                /**
                 * Check if the specified user (by user_id) is in a particular group.
                 *
                 * @param int $user_id the id of the user.
                 * @param int $group_id the id of the group. 
                 * @return bool true if the user is in the group, false otherwise.
                 */     
                'in_group' => function ($user_id, $group_id) {
                    $user = \UserFrosting\Sprinkle\Account\Model\User::find($user_id);
                    return ($user->id == $user_id);
                },
                
                /**
                 * Check if all values in the array $needle are present in the values of $haystack.
                 *
                 * @param array[mixed] $needle the array whose values we should look for in $haystack
                 * @param array[mixed] $haystack the array of values to search.    
                 * @return bool true if every value in $needle is present in the values of $haystack, false otherwise.
                 */          
                'subset' => function ($needle, $haystack) {
                    return count($needle) == count(array_intersect($needle, $haystack));
                },
                
                /**
                 * Check if all keys of the array $needle are present in the values of $haystack.
                 *
                 * This function is useful for whitelisting an array of key-value parameters.
                 * @param array[mixed] $needle the array whose keys we should look for in $haystack
                 * @param array[mixed] $haystack the array of values to search.    
                 * @return bool true if every key in $needle is present in the values of $haystack, false otherwise.
                 */      
                'subset_keys' => function ($needle, $haystack) {
                    return count($needle) == count(array_intersect(array_keys($needle), $haystack));
                }
            ];
            
            $authorizer = new AuthorizationManager($c, $callbacks);
            return $authorizer;
        };
        
        /**
         * Loads the User object for the currently logged-in user.
         *
         * Tries to re-establish a session for "remember-me" users who have been logged out, or creates a guest user object if no one is logged in.
         * @todo Move some of this logic to the Authenticate class.
         */ 
        $container['currentUser'] = function ($c) {
            $authenticator = $c->get('authenticator');
            $config = $c->get('config');
            // Force database connection to boot up
            $c->get('db');
            
            // If this throws a PDOException we catch it and generate a guest user rather than allowing the exception to propagate.
            // This is because the error handler relies on Twig, which relies on a Twig Extension, which relies on the global current_user variable.
            // So, we really don't want this particular service to throw any exceptions.
            try {
                // Now, check to see if we have a user in session or rememberMe cookie
                $currentUser = $authenticator->getSessionUser();
            } catch (\PDOException $e) {
                $currentUser = null;
            }
            
            // If no authenticated user, create a 'guest' user object
            if (!$currentUser) {
                $currentUser = new User();
                $currentUser->id = $config['reserved_user_ids.guest'];
            }
            
            // TODO: Add user locale in translator
            // TODO: Set user theme in Twig
            /*
            // Set path to user's theme, prioritizing over any other themes.
            $loader = $twig->getLoader();
            $loader->prependPath($this->config('themes.path') . "/" . $this->user->getTheme());
            */            
            
            return $currentUser;
        };
    }
}

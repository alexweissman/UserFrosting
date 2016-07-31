<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Account\ServicesProvider;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Account\Twig\AccountExtension;
use UserFrosting\Sprinkle\Account\Model\User;

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
         * Loads the User object for the currently logged-in user.
         *
         * Tries to re-establish a session for "remember-me" users who have been logged out, or creates a guest user object if no one is logged in.
         * @todo Move some of this logic to the Authenticate class.
         */ 
        $container['currentUser'] = function ($c) {
            $config = $c->get('config');
            $session = $c->get('session');
            // Force database connection to boot up
            $c->get('db');
            
            // By default, create a guest user
            $currentUser = new User();
            $currentUser->id = $config['reserved_user_ids.guest'];
            
            // Now, check to see if we have a user in session or rememberMe cookie
            try {
                $rememberMe = $c->get('rememberMe');
                
                // Determine if we are already logged in (user exists in the session variable)
                if($session->has('user_id') && ($session['user_id'] != null)) {       
                    $currentUser = User::find($session['user_id']);
                    
                    // Load the user.  If they don't exist any more, throw an exception.
                    if (!$currentUser)
                        throw new AccountInvalidException();
                        
                    if (!$currentUser->flag_enabled)
                        throw new AccountDisabledException();
                    
                    // Check, if the Rememberme cookie exists and is still valid.
                    // If not, we log out the current session
                    if(!empty($_COOKIE[$rememberMe->getCookieName()]) && !$rememberMe->cookieIsValid()) {
                        $rememberMe->clearCookie();
                        throw new AuthExpiredException();
                    }
                // If not, try to login via RememberMe cookie
                } else {
                    // Get the user id. If we can present the correct tokens from the cookie, log the user in
                    $user_id = $rememberMe->login();
                    
                    if($user_id) {
                        // Load the user
                        return User::find($user_id);
                        // Update in session
                        $session['user_id'] = $user_id;
                        // There is a chance that an attacker has stolen the login token, so we store
                        // the fact that the user was logged in via RememberMe (instead of login form)
                        $session['remembered_by_cookie'] = true;
                    } else {
                        // If $rememberMe->login() returned false, check if the token was invalid.  This means the cookie was stolen.
                        if($rememberMe->loginTokenWasInvalid()) {
                            throw new AuthCompromisedException();
                        }
                    }
                }
            } catch (\PDOException $e){
                // If we can't connect to the DB, then we can't create an authenticated user.
                // That's ok if we're in installation mode. We'll use the guest user instead.
            }
            
            // If we have an authenticated user, setup their environment
            
            // TODO: Add user locale in translator
            
            /*
            // TODO: Set user theme in Twig
            // Set path to user's theme, prioritizing over any other themes.
            $loader = $twig->getLoader();
            $loader->prependPath($this->config('themes.path') . "/" . $this->user->getTheme());
            */            
            
            return $currentUser;
        };
        
        /**
         * "Remember me" service.
         *
         * Allows UF to recreate a user's session from a "remember me" cookie.
         * @throws PDOException
         */
        $container['rememberMe'] = function ($c) {
            $config = $c->get('config');
            $session = $c->get('session');        
            // Force database connection to boot up
            $c->get('db');            
            
            // Initialize RememberMe
            $storage = new \Birke\Rememberme\Storage\PDO($config['remember_me_table']);
            $storage->setConnection(Capsule::connection()->getPdo());
            $rememberMe = new \Birke\Rememberme\Authenticator($storage);
            // Set cookie name
            $rememberMe->setCookieName($config['session.name'] . '-rememberme');
            
            // Change cookie path
            $cookie = $rememberMe->getCookie();
            $cookie->setPath("/");
            $rememberMe->setCookie($cookie);
            
            return $rememberMe;
        };
    }
}

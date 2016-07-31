<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2013-2016 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Account\Authenticate;

use UserFrosting\Support\Exception\ForbiddenException;

/**
 * Invalid account exception.  Used when an account has been removed during an active session.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class AccountInvalidException extends ForbiddenException
{
    protected $default_message = 'ACCOUNT_INVALID';
}

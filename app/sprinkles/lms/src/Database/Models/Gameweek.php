<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Lms\Database\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use UserFrosting\Sprinkle\Account\Database\Models\Interfaces\UserInterface;
use UserFrosting\Sprinkle\Account\Facades\Password;
use UserFrosting\Sprinkle\Core\Database\Models\Model;
use UserFrosting\Sprinkle\Core\Facades\Debug;

class Gameweek extends Model
{
    /**
     * The name of the table for the current model.
     *
     * @var string
     */
    protected $table = 'gameweek';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'gameweek_number',
        'year',
        'status',
        'deadline'
    ];
}

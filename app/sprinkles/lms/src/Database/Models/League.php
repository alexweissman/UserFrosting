<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Lms\Database\Models;

use UserFrosting\Sprinkle\Core\Database\Models\Model;

class League extends Model
{
    /**
     * The name of the table for the current model.
     *
     * @var string
     */
    protected $table = 'league';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'league_name',
        'status',
        'admin_user_id',
        'original_league_id',
        'join_code'
    ];

    public function rounds(){
        $rounds = Round::where('league_id', $this->id)->get();
        return $rounds;
    }
}

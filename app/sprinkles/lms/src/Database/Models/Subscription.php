<?php

namespace UserFrosting\Sprinkle\Lms\Database\Models;
use UserFrosting\Sprinkle\Core\Database\Models\Model;

class Subscription extends Model
{
    /**
     * The name of the table for the current model.
     *
     * @var string
     */
    protected $table = 'subscription';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'stripe_cus_id',
        'term', 
        'status'
    ];
}

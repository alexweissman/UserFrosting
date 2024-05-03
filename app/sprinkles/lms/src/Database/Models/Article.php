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

// database needs id, title, subtitle, slug, article, meta description, meta keywords, created_at, updated_at


class Article extends Model
{
    /**
     * The name of the table for the current model.
     *
     * @var string
     */
    protected $table = 'article';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'subtitle',
        'slug',
        'article',
        'meta_description',
        'meta_keywords'
    ];
}

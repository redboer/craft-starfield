<?php
/**
 * Starfield plugin for Craft CMS
 *
 * Adds a simple star rating field.
 *
 * @link https://github.com/redboer
 * @copyright Copyright (c) Richard de Boer
 * @license MIT
 */

namespace redboer\starfield\models;

use craft\base\Model;

/**
 * Starfield Settings Model
 */
class Settings extends Model
{
    /**
     * @var bool Whether users can deselect all stars (set rating to 0)
     */
    public bool $allowZeroStars = false;

    /**
     * @var bool Whether to show empty stars in element index
     */
    public bool $showEmptyStars = true;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['allowZeroStars', 'showEmptyStars'], 'boolean'],
        ];
    }
}

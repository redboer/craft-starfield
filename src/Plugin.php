<?php
/**
 * Starfield plugin for Craft CMS
 *
 * Adds a simple star rating field.
 *
 * @link https://www.oberon.nl/
 * @copyright Copyright (c) Oberon
 * @license MIT
 */

namespace oberon\starfield;

use craft\base\Element;
use craft\elements\Entry;
use craft\events\RegisterElementSortOptionsEvent;
use oberon\starfield\fields\StarField;

use Craft;
use \craft\services\Fields;
use \craft\events\RegisterComponentTypesEvent;
use yii\base\Event;

/**
 * Simple Text plugin
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public string $schemaVersion = '1.0.1';

    /**
     * @var Plugin
     */
    public static Plugin $plugin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our fields
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = StarField::class;
            }
        );

        Craft::info(
            'starfield plugin loaded',
            __METHOD__
        );
    }

}

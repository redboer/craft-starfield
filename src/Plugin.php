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

namespace redboer\starfield;

use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use redboer\starfield\fields\StarField;
use yii\base\Event;

/**
 * Starfield plugin
 *
 * @method static Plugin getInstance()
 * @method \redboer\starfield\models\Settings getSettings()
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public string $schemaVersion = '1.0.1';

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        \Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new \redboer\starfield\models\Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        return \Craft::$app->view->renderTemplate('starfield/settings', [
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * Attach event handlers
     */
    private function attachEventHandlers(): void
    {
        // Register field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = StarField::class;
            }
        );
    }
}

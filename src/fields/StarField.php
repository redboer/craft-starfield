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

namespace oberon\starfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\helpers\Cp;

/**
 * Simple Text field type
 */
class StarField extends Field implements PreviewableFieldInterface, SortableFieldInterface
{
    /**
     * @var int
     */
    public int $maxStars = 5;

    /**
     * @inheritDoc
     */
    public static function displayName(): string
    {
        return Craft::t('starfield', 'Starfield');
    }

    /**
     * Returns the field's settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml(): ?string
    {
        return Cp::selectFieldHtml(
            [
                'label' => Craft::t('starfield', 'Max stars'),
                'instructions' => Craft::t('starfield', 'Choose the maximum number of stars that can be given.'),
                'id' => 'maxStars',
                'name' => 'maxStars',
                'value' => $this->maxStars,
                'options' => [
                    1 => Craft::t('starfield', '1 star'),
                    3 => Craft::t('starfield', '3 stars'),
                    5 => Craft::t('starfield', '5 stars'),
                    10 => Craft::t('starfield', '10 stars')
                ],
                'errors' => $this->getErrors('maxStars'),
            ]);
    }

    /**
     * Returns the field's input HTML.
     *
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return string
     */
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('starfield/input', [
            'name' => $this->handle,
            'value' => $value,
            'maxStars' => $this->maxStars,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTableAttributeHtml(mixed $value, ElementInterface $element): string
    {
        $result = '';
        if ($value <= 0) return '-';

        if ($this->maxStars <= 5) {
            for ($i = 0; $i < $value; $i++) {
                $result .= '⭐';
            }
        } else {
            $result = "⭐ ($value/$this->maxStars)";
        }
        return $result;
    }

}

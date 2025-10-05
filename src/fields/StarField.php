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

namespace redboer\starfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\helpers\Cp;
use GraphQL\Type\Definition\Type;
use redboer\starfield\Plugin;
use yii\db\Schema;

/**
 * Starfield Field
 */
class StarField extends Field implements PreviewableFieldInterface, SortableFieldInterface
{
    /**
     * @var int Maximum number of stars
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
     * @inheritDoc
     */
    public static function icon(): string
    {
        return '@redboer/starfield/icon.svg';
    }

    /**
     * @inheritDoc
     */
    public static function dbType(): string
    {
        return Schema::TYPE_TINYINT;
    }

    /**
     * @inheritDoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['maxStars'], 'required'];
        $rules[] = [['maxStars'], 'in', 'range' => [1, 3, 5, 10]];
        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function getElementValidationRules(): array
    {
        return [
            ['number', 'integerOnly' => true],
            ['number', 'min' => 0],
            ['number', 'max' => $this->maxStars],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSettingsHtml(): ?string
    {
        return Cp::selectFieldHtml([
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
     * @inheritDoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Ensure it's an integer and clamp between 0 and maxStars
        $value = (int)$value;
        return max(0, min($value, $this->maxStars));
    }

    /**
     * @inheritDoc
     */
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        $plugin = Plugin::getInstance();
        $settings = $plugin?->getSettings();

        return Craft::$app->getView()->renderTemplate('starfield/input', [
            'name' => $this->handle,
            'value' => $value,
            'maxStars' => $this->maxStars,
            'allowZeroStars' => $settings->allowZeroStars ?? false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getPreviewHtml(mixed $value, ElementInterface $element): string
    {
        $plugin = Plugin::getInstance();
        $settings = $plugin?->getSettings();
        $showEmptyStars = $settings?->showEmptyStars ?? true;

        // For one star show star or empty star
        if ($this->maxStars === 1) {
            if ($value === 1) {
                return '⭐';
            } else {
                return $showEmptyStars ? '☆' : '-';
            }
        }

        // For more than 1 star, show - if empty or 0
        if ($value === null || $value <= 0) {
            return '-';
        }

        // For 3 or 5 stars, show filled stars with optional empty stars
        if ($this->maxStars <= 5) {
            $stars = str_repeat('⭐', $value);
            if ($showEmptyStars) {
                $emptyStars = str_repeat('☆', $this->maxStars - $value);
                return $stars . $emptyStars;
            }
            return $stars;
        }

        // For more than 5 stars, show compact text format
        return "⭐ ($value/$this->maxStars)";
    }

    /**
     * @inheritDoc
     */
    public function getTableAttributeHtml(mixed $value, ElementInterface $element): string
    {
        return $this->getPreviewHtml($value, $element);
    }

    /**
     * @inheritDoc
     */
    public function getStaticHtml(mixed $value, ElementInterface $element): string
    {
        return $this->getPreviewHtml($value, $element);
    }

    /**
     * @inheritDoc
     */
    public function getSearchKeywords(mixed $value, ElementInterface $element): string
    {
        return (string)($value ?? '');
    }

    /**
     * @inheritDoc
     */
    public function getContentGqlType(): Type|array
    {
        return Type::int();
    }

    /**
     * @inheritDoc
     */
    public function getContentGqlMutationArgumentType(): Type|array
    {
        return [
            'name' => $this->handle,
            'type' => Type::int(),
            'description' => "The star rating value (0-{$this->maxStars})",
        ];
    }

    /**
     * @inheritDoc
     */
    public function getElementConditionRuleType(): array|string|null
    {
        return \craft\fields\conditions\NumberFieldConditionRule::class;
    }
}

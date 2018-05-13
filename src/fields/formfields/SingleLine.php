<?php

namespace barrelstrength\sproutforms\fields\formfields;

use Craft;
use craft\helpers\Template as TemplateHelper;
use yii\db\Schema;
use craft\base\ElementInterface;
use craft\base\PreviewableFieldInterface;

use barrelstrength\sproutforms\base\FormField;

/**
 * Class SingleLine
 *
 * @package Craft
 */
class SingleLine extends FormField implements PreviewableFieldInterface
{
    /**
     * @var string
     */
    public $cssClasses;

    /**
     * @var string|null The input’s placeholder text
     */
    public $placeholder = '';

    /**
     * @var int|null The maximum number of characters allowed in the field
     */
    public $charLimit;

    /**
     * @var string The type of database column the field should have in the content table
     */
    public $columnType = Schema::TYPE_TEXT;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('sprout-forms', 'Single Line');
    }

    /**
     * @return string
     */
    public function getSvgIconPath()
    {
        return '@sproutbaseicons/font.svg';
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        $rendered = Craft::$app->getView()->renderTemplate(
            'sprout-forms/_components/fields/formfields/singleline/settings',
            [
                'field' => $this,
            ]
        );

        return $rendered;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        return Craft::$app->getView()->renderTemplate('sprout-base-fields/_components/fields/formfields/singleline/input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getExampleInputHtml()
    {
        return Craft::$app->getView()->renderTemplate('sprout-forms/_components/fields/formfields/singleline/example',
            [
                'field' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getFrontEndInputHtml($value, array $renderingOptions = null): string
    {
        $rendered = Craft::$app->getView()->renderTemplate(
            'singleline/input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'renderingOptions' => $renderingOptions
            ]
        );

        return TemplateHelper::raw($rendered);
    }
}
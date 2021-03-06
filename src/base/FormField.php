<?php

namespace barrelstrength\sproutforms\base;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;

/**
 * Class FormField
 *
 * @package Craft
 */
abstract class FormField extends Field
{
    /**
     * @var array
     */
    protected static $fieldVariables = [];

    /**
     * @var bool
     */
    public $allowRequired = true;

    /**
     * @var string
     */
    protected $originalTemplatesPath;

    /**
     * Allows a user to add variables to an object that can be parsed by fields
     *
     * @example
     * {% do craft.sproutForms.addFieldVariables({ entry: entry }) %}
     * {{ craft.sproutForms.displayForm('contact') }}
     *
     * @param array $variables
     */
    public static function addFieldVariables(array $variables)
    {
        static::$fieldVariables = array_merge(static::$fieldVariables, $variables);
    }

    /**
     * @return array
     */
    public static function getFieldVariables()
    {
        return static::$fieldVariables;
    }

    /**
     * The name of your form field
     *
     * @return string
     */
    public static function displayName(): string
    {
        return '';
    }

    /**
     * The icon to display for your form field
     *
     * @return string
     */
    public function getSvgIconPath()
    {
        return '';
    }

    /**
     * Tells Sprout Forms NOT to wrap your getInputHtml() content inside any extra HTML
     *
     * @return bool
     */
    public function isPlainInput()
    {
        return false;
    }

    /**
     * Tells Sprout Forms to use a <fieldset> instead of a <div> as your field wrapper and
     * NOT to add a for="" attribute to your field's top level label.
     *
     * @note
     * Sprout Forms renders a label with a (for) attribute for all fields.
     * If your field has multiple labels, like radio buttons do for example,
     * it would make sense for your field no to have a (for) attribute at the top level
     * but have them at the radio field level. Individual inputs can then wrap each
     * <input> field in a <label> attribute.
     */
    public function hasMultipleLabels()
    {
        return false;
    }

    /**
     * Display or suppress instructions field.
     *
     * @note
     * This is useful for some field types like the Section Heading field
     * where another textarea field may be the primary to use for output.
     *
     * @return bool
     */
    public function displayInstructionsField()
    {
        return true;
    }

    /**
     * The namespace to use when preparing your field's <input> name. This value
     * is also prepended to the field ID.
     *
     * @example
     * All fields default to having name attributes using the fields namespace:
     *
     * <input name="fields[fieldHandle]">
     *
     * @return string
     */
    public function getNamespace()
    {
        return 'fields';
    }

    /**
     * The folder path where this field template is located. This value may be overridden by users
     * when using Form Templates.
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        return Craft::getAlias('@barrelstrength/sproutforms/templates/_components/formtemplates/accessible/fields/');
    }

    /**
     * The folder name within the field path to find the input HTML file for this field. By default,
     * the folder is expected to use the Field Class short name.
     *
     * @example
     * The PlainText Field Class would look for it's respective input HTML in the `plaintext/input.html`
     * file within the folder returned by getTemplatesPath()
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getFieldInputFolder()
    {
        $fieldClassReflection = new \ReflectionClass($this);

        return strtolower($fieldClassReflection->getShortName());
    }

    /**
     * The example HTML input field that displays in the UI when a field is dragged to the form layout editor
     *
     * @return string
     */
    abstract public function getExampleInputHtml();

    /**
     * The HTML to render when a Form is output using the displayForm, displayTab, or displayField tags
     *
     * @param mixed $value
     * @param array $renderingOptions
     *
     * @return \Twig_Markup
     */
    abstract public function getFrontEndInputHtml($value, array $renderingOptions = null);
}

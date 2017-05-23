<?php
namespace barrelstrength\sproutforms\contracts;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use craft\helpers\FileHelper;
/**
 * Class SproutFormsBaseField
 *
 * @package Craft
 */
abstract class SproutFormsBaseField extends Field
{
	/**
	 * @var array
	 */
	protected static $fieldVariables = array();

	/**
	 * @var string
	 */
	protected $originalTemplatesPath;

	/**
	 * @param FieldModel $field
	 * @param mixed      $value
	 * @param mixed      $settings
	 * @param array      $renderingOptions
	 *
	 * @return \Twig_Markup
	 */
	abstract public function getFormInputHtml($field, $value, $settings, array $renderingOptions = null);

	final public function beginRendering()
	{
		$this->originalTemplatesPath = Craft::$app->getView()->getTemplatesPath();

		Craft::$app->getView()->setTemplatesPath($this->getTemplatesPath());
	}

	final public function endRendering()
	{
		Craft::$app->getView()->setTemplatesPath($this->originalTemplatesPath);
	}

	final public function setValue($handle, $value)
	{
		Craft::$app->httpSession->add($handle, $value);
	}

	final public function getValue($handle, $default = null)
	{
		return craft()->httpSession->get($handle, $default);
	}

	public static function addFieldVariables(array $variables)
	{
		static::$fieldVariables = array_merge(static::$fieldVariables, $variables);
	}

	public static function getFieldVariables()
	{
		return static::$fieldVariables;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return 'fields';
	}

	/**
	 * @return string
	 */
	public function getIconPath()
	{
		return '';
	}

	/*
	 * Svg icon
	*/
	public function getIcon()
	{
		$iconPath = $this->getIconPath();

		if (!is_file($iconPath) || FileHelper::getMimeType($iconPath) !== 'image/svg+xml')
		{
			$iconPath = Craft::getAlias('@barrelstrength/sproutforms/templates/_components/fields/default.svg');
		}

		return file_get_contents($iconPath);
	}

	/**
	 * @return string
	 */
	public static function displayName(): string
	{
		return '';
	}

	/**
	 * @return string
	 */
	public function getTemplatesPath()
	{
		return Craft::$app->path->getPluginsPath() . '/sproutforms/src/templates/_components/fields/';
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
	 * Tells Sprout Forms NOT to add a (for) attribute to your field's top leve label
	 *
	 * @note
	 * Sprout Forms renders a label with a (for) attribute for all fields.
	 * If your field has multiple labels, like radio buttons do for example,
	 * it would make sense for your field no to have a (for) attribute at the top level
	 * but have them at the radio field level
	 */
	public function hasMultipleLabels()
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function getTableAttributeHtml($value, ElementInterface $element): string
	{
		return $value;
	}
}
<?php
namespace Craft;

class SproutForms_FieldsController extends BaseController
{
	/**
	 * Save a field.
	 */
	public function actionSaveField()
	{
		$this->requirePostRequest();

		// Make sure our field has a section
		// @TODO - handle this much more gracefully
		$tabId = craft()->request->getRequiredPost('tabId');

		// Get the Form these fields are related to
		$formId = craft()->request->getRequiredPost('formId');
		$form = craft()->sproutForms_forms->getFormById($formId);

		$field = new FieldModel();
		
		$field->id           = craft()->request->getPost('fieldId');
		$field->name         = craft()->request->getRequiredPost('name');
		$field->handle       = craft()->request->getRequiredPost('handle');
		$field->instructions = craft()->request->getPost('instructions');
		$field->required     = craft()->request->getPost('required');
		$field->translatable = (bool) craft()->request->getPost('translatable');

		$field->type = craft()->request->getRequiredPost('type');

		$typeSettings = craft()->request->getPost('types');

		if (isset($typeSettings[$field->type]))
		{
			$field->settings = $typeSettings[$field->type];
		}

		// Does our field validate?
		if (!craft()->fields->validateField($field)) 
		{
			return false;
		}

		// Save a new field
		if (!$field->id) 
		{
			SproutFormsPlugin::log('New Field');

			$isNewField = true;
		}
		else
		{
			SproutFormsPlugin::log('Existing Field');

			$isNewField = false;
		}

		// Set our field context
		craft()->content->fieldContext = $form->getFieldContext();
		craft()->content->contentTable = $form->getContentTable();

		// Save our field
		craft()->fields->saveField($field);


		// Now let's add this field to our field layout
		// ------------------------------------------------------------

		// Set the field layout
		$oldFieldLayout =  $form->getFieldLayout();

		$postedFieldLayout = array();
		$requiredFields = array();
		
		foreach ($oldFieldLayout->getTabs() as $oldTab) 
		{	
			$oldTabFields = $oldTab->getFields();

			foreach ($oldTabFields as $oldFieldLayoutField) 
			{
				// Build the postedFieldLayout array
				// [fieldLayout] => Array
				// (
				//     [Tab%201] => Array
				//         (
				//             [0] => 549
				//             [1] => 311
				//         )
				//     [Tab%202] => Array
				//         (
				//             [0] => 457
				//             [1] => 456
				//             [2] => 295
				//         )
				// )
				
				$postedFieldLayout[$oldTab->name][] = $oldFieldLayoutField->fieldId;

				// Build the Required field array
				// Array
				// (
				//     [0] => 549
				//     [1] => 311
				// )
				
				if ($oldFieldLayoutField->required) 
				{
					$requiredFields[] = $oldFieldLayoutField->fieldId;
				}
			}

			if ($tabId == $oldTab->id) 
			{
				$postedFieldLayout[$oldTab->name][] = $field->id;
			}
		}

		// Set the field layout
		$fieldLayout = craft()->fields->assembleLayout($postedFieldLayout, $requiredFields);
		
		$fieldLayout->type = 'SproutForms_Form';
		$form->setFieldLayout($fieldLayout);
		
		// Hand the field off to be saved in the 
		// field layout of our Form Element
		if (craft()->sproutForms_forms->saveForm($form, $field))
		{
			SproutFormsPlugin::log('Field Saved');

			craft()->userSession->setNotice(Craft::t('Field saved.'));

			$this->redirectToPostedUrl($field);
		}
		else
		{
			SproutFormsPlugin::log("Couldn't save field.");

			craft()->userSession->setError(Craft::t('Couldn’t save field.'));

			// Send the field back to the template
			craft()->urlManager->setRouteVariables(array(
				'field' => $field
			)); 
		}
	}

	/**
	 * Edit a field.
	 *
	 * @param array $variables
	 * @throws HttpException
	 * @throws Exception
	 */
	public function actionEditFieldTemplate(array $variables = array())
	{
		$formId = craft()->request->getSegment(3);
		$form = craft()->sproutForms_forms->getFormById($formId);

		if (!empty($variables['fieldId']))
		{
			if (empty($variables['field']))
			{
				$field = craft()->fields->getFieldById($variables['fieldId']);
				$variables['field'] = $field;
	
				$fieldLayoutField = FieldLayoutFieldRecord::model()->find(array(
					'condition' => 'fieldId = :fieldId AND layoutId = :layoutId',
					'params' => array(':fieldId' => $field->id, ':layoutId' => $form->fieldLayoutId)
				));
				
				$variables['required'] = $fieldLayoutField->required;

				$variables['tabId'] = $fieldLayoutField->tabId;
				
				if (!$variables['field'])
				{
					throw new HttpException(404);
				}
			}

			$variables['title'] = (isset($field->name) ? $field->name : "");
		}
		else
		{
			
			if (empty($variables['field']))
			{
				$variables['field'] = new FieldModel();
			}

			$variables['tabId'] = null;

			$variables['title'] = Craft::t('Create a new field');
		}

		$variables['sections'] = $form->getFieldLayout()->getTabs();
		

		$this->renderTemplate('sproutforms/forms/fields/_edit', $variables);
	}

	/**
	 * Deletes a field.
	 * 
	 * @return void
	 */
	public function actionDeleteField()
	{
		$this->requirePostRequest();
		$this->requireAjaxRequest();
		
		$fieldId = craft()->request->getRequiredPost('id');
		$success = craft()->fields->deleteFieldById($fieldId);
		$this->returnJson(array(
			'success' => $success
		));
	}
	
	/**
	 * Reorder a field
	 * 
	 * @return json
	 */
	public function actionReorderFields()
	{
		craft()->userSession->requireAdmin();
		$this->requirePostRequest();
		$this->requireAjaxRequest();
		
		$fieldIds = JsonHelper::decode(craft()->request->getRequiredPost('ids'));
		craft()->sproutForms_fields->reorderFields($fieldIds);
	
		$this->returnJson(array(
			'success' => true
		));
	}
}
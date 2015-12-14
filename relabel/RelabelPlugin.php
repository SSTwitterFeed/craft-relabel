<?php
namespace Craft;

/**
 * Class RelabelPlugin
 *
 * Thank you for using Craft Relabel!
 * @see https://github.com/benjamminf/craft-relabel
 * @package Craft
 */
class RelabelPlugin extends BasePlugin
{
	function getName()
	{
		return Craft::t('Relabel');
	}

	function getVersion()
	{
		return '0.0.1';
	}

	public function getSchemaVersion()
	{
		return '1.0.0';
	}

	function getDeveloper()
	{
		return 'Benjamin Fleming';
	}

	function getDeveloperUrl()
	{
		return 'http://benf.co';
	}

	public function getDocumentationUrl()
	{
		return 'https://github.com/benjamminf/craft-relabel/blob/master/README.md';
	}

	public function init()
	{
		parent::init();

		if($this->isCraftRequiredVersion())
		{
			$this->includeResources();
		}
	}

	public function isCraftRequiredVersion()
	{
		return version_compare(craft()->getVersion(), '2.5', '>=');
	}

	protected function includeResources()
	{
		if(craft()->request->isCpRequest() && !craft()->request->isAjaxRequest() && craft()->userSession->isAdmin())
		{
			craft()->templates->includeCssResource('relabel/css/main.css');

			craft()->templates->includeJsResource('relabel/js/Relabel.js');
			craft()->templates->includeJsResource('relabel/js/Editor.js');
			craft()->templates->includeJsResource('relabel/js/EditorModal.js');

			craft()->templates->includeJs('window.Relabel.fields=' . json_encode($this->_getFields()));
			craft()->templates->includeJs('window.Relabel.labels=' . json_encode($this->_getLabels()));
			craft()->templates->includeJs('window.Relabel.layouts=' . json_encode($this->_getLayouts()));

			craft()->templates->includeJsResource('relabel/js/main.js');
		}
	}

	private function _getFields()
	{
		$fields = craft()->fields->getAllFields();
		$output = array();

		foreach($fields as $field)
		{
			$output[(int) $field->id] = array(
				'id' => (int) $field->id,
				'name' => $field->name,
				'instructions' => $field->instructions
			);
		}

		return $output;
	}

	private function _getLabels()
	{
		$labels = craft()->relabel->getAllLabels();
		$output = array();

		foreach($labels as $label)
		{
			$output[$label->id] = array(
				'id' => (int) $label->id,
				'fieldId' => (int) $label->fieldId,
				'fieldLayoutId' => (int) $label->fieldLayoutId,
				'name' => $label->name,
				'instructions' => $label->instructions
			);
		}

		return $output;
	}

	private function _getLayouts()
	{
		$assetSources = craft()->assetSources->getAllSources();
		$categoryGroups = craft()->categories->getAllGroups();
		$globalSets = craft()->globals->getAllSets();
		$entryTypes = EntryTypeModel::populateModels(EntryTypeRecord::model()->ordered()->findAll());
		$tagGroups = craft()->tags->getAllTagGroups();
		//$userFields = FieldLayoutModel::populateModel(FieldLayoutRecord::model()->findByAttributes('type', ElementType::User));

		return array(
			'assetSource' => $this->_mapLayouts($assetSources),
			'categoryGroup' => $this->_mapLayouts($categoryGroups),
			'globalSet' => $this->_mapLayouts($globalSets),
			'entryType' => $this->_mapLayouts($entryTypes),
			'tagGroup' => $this->_mapLayouts($tagGroups),
			//'userFields' => $userFields->id
		);
	}

	private function _mapLayouts($list)
	{
		$output = array();

		foreach($list as $item)
		{
			$output[(int) $item->id] = (int) $item->fieldLayoutId;
		}

		return $output;
	}
}

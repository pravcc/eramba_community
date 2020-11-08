<?php
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('Translation', 'Translations.Model');

class TranslationItemData extends ItemDataEntity
{
	public function getPoFilePath()
	{
		return APP . 'Locale' . DS . $this->getTranslationName() . DS . 'LC_MESSAGES' . DS . 'default.po';
	}

	public function getTranslationName()
	{
		if ($this->type == Translation::TYPE_SYSTEM) {
			return $this->folder;
		}

		return Translation::getCustomTranslationName($this->getPrimary());
	}
}
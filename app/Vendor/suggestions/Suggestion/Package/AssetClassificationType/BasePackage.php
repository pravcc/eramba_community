<?php
namespace Suggestion\Package\AssetClassificationType;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'AssetClassificationType';
	public $requestAction = '/assetClassifications/addClassificationType';
	public $forceExistingItem = true;
}

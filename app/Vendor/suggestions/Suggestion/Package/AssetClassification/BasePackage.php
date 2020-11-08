<?php
namespace Suggestion\Package\AssetClassification;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'AssetClassification';
	public $requestAction = '/assetClassifications/add';
}

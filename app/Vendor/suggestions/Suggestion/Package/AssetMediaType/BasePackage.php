<?php
namespace Suggestion\Package\AssetMediaType;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'AssetMediaType';
	public $requestAction = '/assetMediaTypes/add';
}

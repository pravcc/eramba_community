<?php
namespace Suggestion\Package\AssetLabel;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'AssetLabel';
	public $requestAction = '/assetLabels/add';
}

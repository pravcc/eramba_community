<?php
namespace Suggestion\Package\Process;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'Process';
	public $requestAction = '/processes/add';
}

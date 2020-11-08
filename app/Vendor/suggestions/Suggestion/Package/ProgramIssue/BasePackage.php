<?php
namespace Suggestion\Package\ProgramIssue;
use Suggestion\Package;

class BasePackage extends Package {
	public $model = 'ProgramIssue';
	public $requestAction = '/programIssues/add';
}

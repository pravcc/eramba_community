<?php
/**
 * Model Inheritance Interface.
 * Implementing classes are used in functionalities that requires inheritance between models.
 * For example Visualisation permission inheritance to perform ACL synchronization.
 *
 * @package       App.Model.Interface
 */
interface InheritanceInterface {

/**
 * Parent node necessary for Acl.
 *
 * @return void
 */
	public function parentNode($type);

/**
 * Get the parent model name for the current model.
 *
 * @return void
 */
	public function parentModel();

/**
 * Child sections are required to bind to the parent node for reading permission access.
 * 
 * @return array Parent node.
 */
	// public function bindNode($item);

}

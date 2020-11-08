<?php
App::uses('MacroCollection', 'Macros.Lib');

/**
 * MacroSeed interface.
 */
interface MacroSeed
{
/**
 * Bulk create and add of macros of some type to collection.
 * 
 * @param MacroCollection $collection 
 * @return void
 */
	public function seed(MacroCollection $collection);
}
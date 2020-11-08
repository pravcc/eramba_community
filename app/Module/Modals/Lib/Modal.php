<?php

App::uses('Hash', 'Utility');

class Modal
{
	/**
	 * ID of modal
	 */
	protected $modalId = null;

	/**
	 * Friendly name of modal: e.g. for breadcrumbs
	 */
	protected $friendlyName = "";

	/**
	 * Settings for modal's header
	 */
	protected $header = null;

	/**
	 * Settings for modal's body
	 */
	protected $body = null;

	/**
	 * Settings for modal's footer
	 */
	protected $footer = null;

	public function __construct()
	{
	}

	public function setModalId($modalId)
	{
		$this->modalId = $modalId;
	}

	public function setFriendlyName($name)
	{
		$this->friendlyName = $name;
	}

	public function setHeader($header)
	{
		$this->header = $header;
	}

	public function setBody($body)
	{
		$this->body = $body;
	}

	public function setFooter($footer)
	{
		$this->footer = $footer;
	}

	public function getModalId()
	{
		return $this->modalId;
	}

	public function getFriendlyName()
	{
		return $this->friendlyName;
	}

	public function getHeader($path = '', $default = null)
	{
		if ($path === '') {
			return $this->header;
		} else {
			return Hash::get($this->header, $path, $default);
		}
	}

	public function getBody($path = '', $default = null)
	{
		if ($path === '') {
			return $this->body;
		} else {
			return Hash::get($this->body, $path, $default);
		}
	}

	public function getFooter($path = '', $default = null)
	{
		if ($path === '') {
			return $this->footer;
		} else {
			return Hash::get($this->footer, $path, $default);
		}
	}
}

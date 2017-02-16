<?php

abstract class DRM_Counter {

	private $role;

	public function __construct( $role ) {
		$this->role = $role;
	}

	abstract public function get_role();

}
<?php

abstract class generations_counter {

	private $target;

	public function __construct( $target ) {
		$this->target = $target;
	}

	abstract public function get_role();

}
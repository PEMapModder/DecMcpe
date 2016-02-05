<?php

/*
 * DecMcpe
 *
 * Copyright (C) 2016 PEMapModder
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

class Packet{
	/** @type string */
	public $name;
	/** @type int */
	public $id;
	/** @type string */
	public $idHex;
	/** @type PacketField[] */
	public $fields;

	public function __construct($name){
		$this->name = $name;
	}

	public function dumpInfo(){
		return [
			"id" => $this->idHex,
			"fields" => array_map(function(PacketField $field){return $field->dumpInfo();},$this->fields),
		];
	}
}

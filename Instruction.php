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

class Instruction{
	/** @var string */
	public $offsetHex;
	/** @var int */
	public $offset;
	/** @var string */
	public $byteCode;
	/** @var string */
	public $instr;
	/** @var string */
	public $cond;
	/** @var string */
	public $args;

	public function __construct($line){
		if(preg_match(/** @lang RegExp */
			'%([a-f0-9]+):[\t ]+([0-9a-f]{4}( [0-9a-f]{4})?).*; <UNDEFINED>.*$%', $line, $match)
		){
			$this->offsetHex = $match[1];
			$this->offset = hexdec($this->offsetHex);
			$this->byteCode = $match[2];
			$this->instr = "undefined";
			$this->cond = "";
			$this->args = "";
			return;
		}
		if(!preg_match(/** @lang RegExp */
			'%([a-f0-9]+):[\t ]+([0-9a-f]{4}( [0-9a-f]{4})?)[\t ]+([a-z]+)(\.([a-z]))?([\t ]+([^;]+)(;.*)?)?$%', $line, $match)
		){
			echo $line, PHP_EOL;
			throw new InvalidArgumentException("Not an instruction");
		}
		$this->offsetHex = $match[1];
		$this->offset = hexdec($this->offsetHex);
		$this->byteCode = $match[2];
		$this->instr = strtolower($match[4]);
		$this->cond = $match[6] ?? "";
		$this->args = $match[8] ?? "";
	}
}

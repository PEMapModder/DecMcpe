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
	/** @var Instruction[] */
	public $instr;
	public $instrOffsetIndex;
	/** @type PacketField[][] */
	public $fields;
	/** @var Flow[] */
	public $flows = [];

	public function __construct($name){
		$this->name = $name;
	}

	public function dumpInfo(){
		return [
			"id" => $this->idHex,
			"fields" => array_map(function ($fieldSet){
				return array_map(function (PacketField $field){
					return $field->dumpInfo();
				}, $fieldSet);
			}, $this->fields),
		];
	}

	public function startAnalyze(){
		$this->fields = [];
		$this->instr = [];
		$this->instrOffsetIndex = [];
	}

	public function analyze($line){
		$instr = new Instruction($line);
		$this->instrOffsetIndex[$instr->offsetHex] = $i = count($this->instr);
		$this->instr[$i] = $instr;
	}

	public function stopAnalyze(){
		$flow = new Flow($this);
		$this->flows[] = $flow;
		$flow->flow();

		$fieldSets = [];
		foreach($this->flows as $flow){
			$fields = $flow->getFields();
			foreach($fieldSets as $fieldSet){
				if($fieldSet == $fields){
					continue 2;
				}
			}
			$fieldSets[] = $fields;
		}

		$this->fields = $fieldSets;
		echo "\rFinished analyzing $this->name (memory: " . round(memory_get_usage() / 1024, 2) . " KB)\n";
		unset($this->instr);
		gc_collect_cycles();
		echo "Freed resources (memory: " . round(memory_get_usage() / 1024, 2) . " KB)\n";
	}

	public function isReady(){
		return isset($this->id) and isset($this->fields);
	}
}

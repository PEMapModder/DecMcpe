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

class Flow{
	private static $childId;

	/** @var Packet */
	private $packet;
	private $nextSize;
	private $current = 0;

	private $fields = [];
	private $history = [];

	public function __construct(Packet $packet){
		$this->packet = $packet;
		self::$childId = 0;
		$this->id = 0;
	}

	public function flow(){
//		echo json_encode($this->packet->instr,JSON_PRETTY_PRINT);
//		exit;
		while($this->current < count($this->packet->instr)){
			$this->flowUnit();
			$this->current++;
		}
	}

	public function flowUnit(){
		$instr = $this->packet->instr[$this->current];
		if(!($instr instanceof Instruction)){
			var_dump($instr);
			exit("Not an Instruction");
		}
		if($instr->instr === "movs"){
			list($name, $value) = explode(", ", $instr->args);
			if($name === "r2"){
				if($value{0} === "#"){
					if(substr($value, 1, 2) === "0x"){
						$this->nextSize = hexdec(substr($value, 3));
					}else{
						$this->nextSize = (int) substr($value, 1);
					}
				}
			}
		}elseif($instr->instr === "bne"){
			$offset = strstr($instr->args, " ", true);
			$this->branchIf($offset);
		}elseif($instr->instr === "cbnz"){
			$offset = strstr(explode(", ", $instr->args)[1], " ", true);
			$this->branchIf($offset);
		}elseif($instr->instr === "b"){
			$offset = strstr($instr->args, " ", true);
			$this->branch($offset);
		}elseif($instr->instr === "pop"){
			$this->current = PHP_INT_MAX;
		}elseif($instr->instr === "bl"){
			if(strstr($instr->args, " ") === " <RakNet::BitStream::ReadBits(unsigned char*, unsigned int, bool)>"){
				$field = new PacketField("scalar", $this->nextSize);
				$this->fields[] = $field;
			}elseif(strpos($instr->args, " <PacketUtil::readString(") !== false){
				$this->fields[] = new PacketField("string", -2);
			}elseif(strpos($instr->args, " <PacketUtil::readUUID(") !== false){
				$this->fields[] = new PacketField("uuid", 16);
			}elseif(strpos($instr->args, " <PacketUtil::readItemInstance(") !== false){
				$this->fields[] = new PacketField("item", -2);
			}
		}
	}

	public function branchIf($next){
		$flow = clone $this;
		if(!$flow->branch($next)){
			return;
		}
		$flow->id = ++self::$childId;
		echo "\nChild branch #$flow->id from parent #$this->id";
		$this->packet->flows[] = $flow;
		$flow->flow();
	}

	public function branch($offset){
		if(isset($this->packet->instrOffsetIndex[$offset]) and !isset($this->history[$offset])){
			$this->history[$offset] = true;
			$this->current = $this->packet->instrOffsetIndex[$offset];
			return true;
		}
		return false;
	}

	public function getFields(){
		return $this->fields;
	}
}

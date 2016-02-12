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

class PacketField{
//	private $message;
//	private $instr, $modifier, $args;
//	private $valid = false;
	private $type;
	private $size;

	public function __construct($type, $size){
		$this->type = $type;
		$this->size = $size;
	}

//	private $destination, $value;

//	public function __construct($message, &$pkSize){
//		$this->message = $message = trim($message);
//		if(preg_match('@^[0-9a-f]+:[ \t]+(([0-9a-f]{4}[ \t]+){1,2})(([a-z]+)(\.([a-z]))?)[ \t]+(.+)$@', $message, $match)){
//			$instr = $match[4];
//			$modifier = $match[6];
//			$args = $match[7];
//			$this->instr = $instr;
//			$this->modifier = $modifier;
//			$this->args = $args;
//			if($instr === "bl"){
//				$types = [
//					["PacketUtil::readString", "string"],
//					["PacketUtil::readUUID", "uuid"],
//					["PacketUtil::readItemInstance", "item"],
//				];
//				foreach($types as list($needle, $type)){
//					if(strpos($message, $needle) !== false){
//						$this->type = $type;
//						$this->valid = true;
//						return;
//					}
//				}
//				if(strpos($message, "RakNet::BitStream::ReadBits") !== false){
//					$this->type = "numeric";
//					$this->size = $pkSize;
//					$this->valid = true;
//				}
//			}
//			if($instr === "movs"){
//				list($dest, $val) = explode(", ", $args);
//				$this->destination=$dest;$this->value=$val;$this->valid=true;
//				if($dest === "r2"){
//					$pkSize = intval(substr($val, 1));
//				}
//			}
//		}
//	}

//	/**
//	 * @return boolean
//	 */
//	public function isValid(){
//		return $this->valid;
//	}

	public function dumpInfo(){
		return [
//			"instruction" => $this->instr,
//			"modifier" => $this->modifier,
//			"args" => $this->args,
			"type" => $this->type,
			"size" => $this->size,
//			"destination" => $this->destination, "value" => $this->value,
		];
	}
}

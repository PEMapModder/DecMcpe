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

class PacketCollection{
	/** @type Packet[] */
	private $packets = [];

	public function getPackets(){
		return $this->packets;
	}

	public function get($name){
		if(!isset($this->packets[$name])){
			$this->packets[$name] = new Packet($name);
		}
		return $this->packets[$name];
	}

	public function write($file){
		$data = $this->packets;
		file_put_contents($file, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_BIGINT_AS_STRING | JSON_PRETTY_PRINT));
	}
}

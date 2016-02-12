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
	/** @type int */
	private $protocolVersion;
	/** @type string */
	private $protocolVersionHex;
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
		ksort($this->packets, SORT_NATURAL | SORT_FLAG_CASE);
		$this->free();
		$data = [
			"protocolVersion" => $this->protocolVersionHex,
			"packets" => $this->packets,
		];
		file_put_contents($file, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_BIGINT_AS_STRING | JSON_PRETTY_PRINT));
	}

	/**
	 * @return int
	 */
	public function getProtocolVersion(){
		return $this->protocolVersion;
	}

	/**
	 * @return string
	 */
	public function getProtocolVersionHex(){
		return $this->protocolVersionHex;
	}

	/**
	 * @param int $protocolVersion
	 */
	public function setProtocolVersion($protocolVersion){
		$this->protocolVersion = $protocolVersion;
		$this->protocolVersionHex = sprintf("0x%x", $protocolVersion);
	}

	public function free(){
		foreach($this->packets as &$packet){
			if($packet instanceof Packet and $packet->isReady()){
				$packet = $packet->dumpInfo();
			}
		}
	}
}

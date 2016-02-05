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

$SUBJECT = "in/mpe.asm";
$OUTPUT = "out/pkdump.json";

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@mkdir(dirname($OUTPUT), 0777, true);

spl_autoload_register(function ($class){
	require_once __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
});

$pkColl = new PacketCollection;

$is = fopen($SUBJECT, "r");
while(!feof($is)){
	$line = rtrim(fgets($is));
	if(preg_match('/([A-Za-z0-9_]+Packet)::getId\(\)/', $line, $match)){
		$name = $match[1];
		$next = trim(fgets($is));
		if(preg_match('/[0-9]{4}[ \t]+movs[ \t]+r0, #([0-9]+)/', $next, $match2)){
			$id = hexdec($match2[1]) + 0x8e;
			$pkColl->get($name)->id = $id;
			$pkColl->get($name)->idHex = sprintf("0x%x", $id);
		}
	}elseif(preg_match('/^[0-9a-f]+ <([A-Za-z0-9_]+Packet)::read\(RakNet::BitStream\*\)>:/', $line, $match)){
		$pkName = $match[1];
		$fields = [];
		while(!feof($is)){
			$line = rtrim(fgets($is));
			if(strlen($line) - 2 !== strlen(ltrim($line, " "))){
				break;
			}
			$fields[] = $line;
		}
		$pkColl->get($pkName)->fields = $fields;
	}
}

$pkColl->write($OUTPUT);

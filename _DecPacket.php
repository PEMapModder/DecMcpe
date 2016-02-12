#!/usr/bin/env php
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

$opts = getopt("", ["in:", "out:"]);
$SUBJECT = $opts["in"] ?? "in/mpe.asm";
$OUTPUT = $opts["out"] ?? "out/pkdump.json";

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@mkdir(dirname($OUTPUT), 0777, true);

spl_autoload_register(function ($class){
	require_once __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
});

$pkColl = new PacketCollection;

echo "\rOpening stream to read $SUBJECT";
$is = fopen($SUBJECT, "r");
$linesCnt = 0;
while(!feof($is)){
	$linesCnt++;
//	echo "\rLine $linesCnt: Reading...";
	$line = rtrim(fgets($is));
	if(strpos($line, "SharedConstants::NetworkProtocolVersion") !== false){
		echo "\rLine $linesCnt: Reading...";
		echo " Detecting protocol version...";
		$linesCnt++;
		$next = trim(fgets($is));
		if(preg_match('/[0-9a-f]+:[ \t]+([0-9a-f]+)/', $next, $match)){
			$pkColl->setProtocolVersion(hexdec($match[1]));
			echo " Detected 0x$match[1]";
		}
	}elseif(preg_match('/([A-Za-z0-9_]+Packet)::getId\(\)/', $line, $match)){
		echo "\rLine $linesCnt: Reading...";
		echo " Detecting packet-to-ID declaration...";
		$name = $match[1];
		$linesCnt++;
		$next = trim(fgets($is));
		if(preg_match('/[0-9a-f]{4}[ \t]+movs[ \t]+r0, #([0-9]+)/', $next, $match2)){
			$id = hexdec($match2[1]) + 0x8e;
			printf(" Detected 0x%02x", $id);
			$pkColl->get($name)->id = $id;
			$pkColl->get($name)->idHex = sprintf("0x%02x", $id);
		}
	}elseif(preg_match('/^[0-9a-f]+ <([A-Za-z0-9_]+Packet)::read\(RakNet::BitStream\*\)>:/', $line, $match)){
		echo "\rLine $linesCnt: Reading...";
		$pkName = $match[1];
//		$fields = [];
//		$pkSize = 0;
		$pk = $pkColl->get($pkName);
		$pk->startAnalyze();
		while(!feof($is)){
			$linesCnt++;
			echo "\rLine $linesCnt: Reading $pkName packet structure...";
			$line = rtrim(fgets($is));
			if(strlen($line) - 2 !== strlen(ltrim($line, " "))){
				break;
			}
//			$field = new PacketField($line, $pkSize);
//			if($field->isValid()){
//				$fields[] = $field;
//			}
			$pk->analyze($line);
		}
		$pk->stopAnalyze();
		echo "\rAnalyzed $pkName; Memory state: " . round(memory_get_usage() / 1024, 2) . " KB", PHP_EOL;
	}
}

echo "\rWriting to $OUTPUT";
$pkColl->write($OUTPUT);
echo PHP_EOL;
exit(0);

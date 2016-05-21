#!/usr/bin/env php
<?php

/*
 * DecMcpe
 *
 * Copyright (C) 2016 tomocrafter
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author tomocrafter
 */

$opts = getopt("", ["in:", "out:"]);
$SUBJECT = $opts["in"] ?? "in/mpe.asm";
$OUTPUT = $opts["out"] ?? "out/var.json";

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@mkdir(dirname($OUTPUT), 0777, true);

spl_autoload_register(function ($class){
	require_once __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
});

$pkColl = new PacketCollection;

$gets = [
	"NetworkDefaultMaxIncomingConnections",
	"NetworkDefaultGamePort",
	"CurrentLevelChunkFormat",
	"CurrentStorageVersion",
	"LevelDBCompressorID",
	"MaxChatLength",
	"NetworkProtocolVersion",
	"BetaVersion",
	"PatchVersion",
	"MinorVersion",
	"MajorVersion",
];

echo "\rOpening stream to read $SUBJECT";
$is = fopen($SUBJECT, "r");
$linesCnt = 0;
$data = [];
while(!feof($is)){
	$linesCnt++;
	echo "\rLine $linesCnt: Reading...";
	$line = rtrim(fgets($is));
	if(preg_match("<SharedConstants::([\w]+)>", $line, $match)){
		if(in_array($match[1], $gets)){
			echo " Detecting protocol version...";
			$linesCnt++;
			$next = trim(fgets($is));
			if(preg_match('/[0-9a-f]+:[ \t]+([0-9a-f]+)/', $next, $matcha)){
				$data[$match[1]] = "0x$matcha[1]";
				echo " Detected 0x$matcha[1]";
			}
		}
	}
}

echo "\rWriting to $OUTPUT";
file_put_contents($OUTPUT, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_BIGINT_AS_STRING | JSON_PRETTY_PRINT));
echo PHP_EOL;
exit(0);

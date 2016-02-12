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
$OUTPUT = $opts["out"] ?? "out/attrdump.json";

/** @noinspection PhpUsageOfSilenceOperatorInspection */
@mkdir(dirname($OUTPUT), 0777, true);

spl_autoload_register(function ($class){
	require_once __DIR__ . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
});

$output = [];

echo "\rOpening stream to read $SUBJECT";
$is = fopen($SUBJECT, "r");
$linesCnt = 0;
while(!feof($is)){
	$linesCnt++;
	$line = rtrim(fgets($is));
	if(strpos($line, "<Player::registerAttributes()>:")){
		if(preg_match('%<AttributeInstance::([a-zA-Z0-9_]+).*>:%', $line, $match)){
			$output["AttributeInstance::" . $match[1]] = true;
		}
	}
}
file_put_contents($OUTPUT, json_encode(array_keys($output), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_BIGINT_AS_STRING));

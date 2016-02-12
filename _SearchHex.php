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

($hex = getopt("x::")["x"] ?? false) or die("Missing parameter -x");
$opts = getopt("r", ["in:"]);
$SUBJECT = $opts["in"] ?? "in/libminecraftpe.so";
$in = file_get_contents($SUBJECT);
if(isset($opts["r"])){
	$bin = strrev(hex2bin($hex));
}else{
	$bin = hex2bin($hex);
}
printf("%s\n", bin2hex($bin));

$offset = 0;
while(($offset = strpos($in, $bin, $offset)) !== false){
	echo "Found: $offset\n";
}
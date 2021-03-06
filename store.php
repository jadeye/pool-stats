#!/usr/bin/php
<?php
/*

	Copyright 2013 Jindrich Dolezy (dzindra)

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

		http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.

*/


require dirname(__FILE__) . "/inc/common.inc.php";

if (!$env->headless)
	$env->error("This script can be launched from command line only!", 2);

$dataDir = dirname(__FILE__) . '/data';
if (!is_writable($dataDir))
	$env->error("Directory '$dataDir' not writable!", 3);


try {
	$dao = $env->createBlockDao();
	$pools = $env->createPools();

	$runFile = "$dataDir/run";
	$firstRun = !(int)@file_get_contents($runFile);

	$blocks = array();
	/** @var $pool PoolSource */
	foreach ($pools as $pool)
		$blocks = array_merge($blocks, $pool->fetch($firstRun));

	$dao->addBlocks($blocks);

	if (@file_put_contents($runFile, '1') === false)
		throw new PoolStatException("Unable to write to '$runFile'.");

	exit(0);
} catch (Exception $e) {
	$env->error($e, 1);
}


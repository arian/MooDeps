<?php

error_reporting(0);

include_once 'lib/parse.php';

// Find the dependencies from the js file

$depsRes = array();
if(!empty($_POST['file']) && is_array($_POST['file'])){
	
	$files = array();
	foreach($_POST['file'] as $file){
		if(!empty($file)){
			$files[] = make_request($file);
		}
	}
	
	if(isset($_FILES['file']['tmp_name']) && is_array($_FILES['file']['tmp_name'])){
		foreach($_FILES['file']['tmp_name'] as $key => $name){
			$files[] = file_get_contents($name);
		}
	}
	
	$jsDeps = new JSDeps();
	$jsDeps->setTests(require 'lib/mootoolsDependencies.php');
	
//	print_r($files);
	
	foreach($files as $file){
		
		if(!empty($file)){
			$deps1 = $jsDeps->getDependencies($file);
			foreach($deps1 as $dep){
				$depsRes[$dep] = $dep;
			}
		}		
	}
	
	$mooScripts = json_decode(file_get_contents('scripts.json'),true);

	// Complete the dependencies
	$mooDeps = new MooDependencies();
	$mooDeps->setDependencies($mooScripts);
	foreach($depsRes as $dep){
		$mooDeps->addComponent($dep);
	}
	
	$depsRes = $mooDeps->getComponents();
}
echo json_encode(array_values((array)$depsRes));

function make_request($url) {
	if(!function_exists('curl_init')){
		return file_get_contents($url);
	}	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

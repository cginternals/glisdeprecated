<?php
  	function xContains(&$glxpath, $path, $lhs, $rhs)
  	{
		$upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$lower = "abcdefghijklmnopqrstuvwxyz";

  		$arg_lhs = "translate(".$lhs.", '$upper', '$lower')";
		$arg_rhs = "translate(".$rhs.", '$upper', '$lower')";

		return $glxpath->query($path."[contains($arg_lhs, $arg_rhs)]");
  	}

	function queryCommand(&$glxpath, &$result, $query)
	{
		// search and output single COMMAND
		$q = xContains($glxpath, "/registry/commands/command/proto/name", "text()", "$query");

		if(0 == $q->length)
			return;

		foreach ($q as $command)
			$result[] = $command->nodeValue;
	}

	function queryEnum(&$glxpath, &$result, $query)
	{
		// search ENUM by name
		$q = xContains($glxpath, "/registry/enums/enum", "@name", $query);

		if(0 == $q->length)
			// search ENUM by value
			$q = xContains($glxpath, "/registry/enums/enum", "@value", $query);

		if(0 == $q->length)
			return;

		foreach ($q as $command)
			$result[] = $command->getAttribute('name');
	}

	function queryType(&$glxpath, &$result, $query)
	{
		// search ENUM by name
		$q = xContains($glxpath, "/registry/types/type/name", "text()", $query);

		if(0 == $q->length)
			return;

		foreach ($q as $command)
			$result[] = $command->nodeValue;
	}

	function queryExtension(&$glxpath, &$result, $query)
	{
		// search ENUM by name
		$q = xContains($glxpath, "/registry/extensions/extension", "@name", $query);

		if(0 == $q->length)
			return;

		foreach ($q as $command)
			$result[] = $command->getAttribute('name');
	}

	function queryFeature(&$glxpath, &$result, $query)
	{
		// search ENUM by name
		$q = xContains($glxpath, "/registry/feature", "@number", $query);

		if(0 == $q->length)
			return;

		foreach ($q as $command)
			$result[] = $command->getAttribute('number');
	}

	function sortAndClamp(&$results, $query, $limit)
	{
		// aggregate all results and weight them, i.e., by levenshtein

		$weighted = array();

		foreach ($results as $result)
		{
			$l = levenshtein($query, $result);
			$weighted[$l][] = $result;
		}

		// sort from low to high
		ksort($weighted);

		$result = array();

		foreach ($weighted as $key => $values)
			foreach ($values as $value)
			{
				$result[] = $value;

				if(sizeof($result) == $limit)
					return $result;
			}
		return $result;
	}

	function value($request, $default)
	{
		if(!isset($_GET[$request]))
		{
			if(!isset($_POST[$request]))
				return $default;
			else
				return $_POST[$request];	
		}
		else
			return $_GET[$request];
	}

	function exists($request)
	{
		return isset($_GET[$request]) || isset($_POST[$request]);
	}

	//ini_set('error_reporting', E_ALL-E_NOTICE);
	ini_set('display_errors', 0);

	$results = array();

	$query = value("query", "");
	if(empty($query))
	{
		echo json_encode($results);
		return;
	}
	$query = "\"".strtolower($query)."\"";

	$limit = value("limit", 8);
	$mode = value("mode", 1);

	// load gl spec

	$gldoc = new DOMDocument();
	$gldoc->load('gl.xml');

	$glxpath = new Domxpath($gldoc);

	switch($mode)
	{
	case 1:
		queryCommand($glxpath, $results, $query);
		queryEnum($glxpath, $results, $query);
		queryType($glxpath, $results, $query);
		queryExtension($glxpath, $results, $query);
		break;
	case 2:
		queryFeature($glxpath, $results, $query);
		break;
	}

	$results = sortAndClamp($results, $query, $limit);
	echo json_encode($results);
?>
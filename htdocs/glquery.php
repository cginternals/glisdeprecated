<?php

	/** Prettifies an XML string into a human-readable and indented work of art
	 *  @param string $xml The XML as a string
	 *  @param boolean $html_output True if the output should be escaped (for use in HTML)
	 */
	// http://gdatatips.blogspot.de/2008/11/xml-php-pretty-printer.html
	function xmlpp($xml, $html_output=false) {
	    $xml_obj = new SimpleXMLElement($xml);
	    $level = 4;
	    $indent = 0; // current indentation level
	    $pretty = array();
	    
	    // get an array containing each XML element
	    $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

	    // shift off opening XML tag if present
	    if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
	      $pretty[] = array_shift($xml);
	    }

	    foreach ($xml as $el) {
	      if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
	          // opening tag, increase indent
	          $pretty[] = str_repeat(' ', $indent) . $el;
	          $indent += $level;
	      } else {
	        if (preg_match('/^<\/.+>$/', $el)) {            
	          $indent -= $level;  // closing tag, decrease indent
	        }
	        if ($indent < 0) {
	          $indent += $level;
	        }
	        $pretty[] = str_repeat(' ', $indent) . $el;
	      }
	    }   
	    $xml = implode("\n", $pretty);   
	    return ($html_output) ? htmlentities($xml) : $xml;
	}


	function createAndAttachNode(&$document, $tag, &$parent = nil)
	{
		if(nil == $document)
			return nil;

		$node = $document->createElement($tag);
		if(nil == $node)
			return nil;

		if(nil != $parent)
			return $parent->appendChild($node);
		else
			return $document->appendChild($node);
	}

	function cloneAndAttachNode(&$document, $node, &$parent = nil)
	{
		if(nil == $document)
			return nil;

		$clone = $node->cloneNode(true);
		$clone = $document->importNode($clone, true);
	
		if(nil == $clone)
			return nil;

		if(nil != $parent)
			return $parent->appendChild($clone);
		else
			return $document->appendChild($clone);
	}

	function renameAndAttachNode(&$document, $node, $name, &$parent = nil)
	{
		$clone = $document->createElement($name);

		if(nil == $node)
			return nil;

		if(nil != $parent)
			$clone = $parent->appendChild($clone);
		else
			$clone = $document->appendChild($clone);

		// clone attributes
		if ($node->hasAttributes())
			foreach ($node->attributes as $attr) 
			{
				$a = $document->createAttribute($attr->nodeName);
				$a->value = $attr->nodeValue;

				$clone->appendChild($a);
			}

		// TODO: clone children

		return $clone; 
	}

	function removeChildNodes(&$node)
	{
		if(nil == $node)
			return nil;

		while($node->hasChildNodes())
    		$node->removeChild($node->firstChild);
    }

	function queryRelatingFeatures(&$glxpath, &$document, $type, $query, &$features)
	{
    	// find the feature it was required in
		$q = $glxpath->query("//registry/feature[require/{$type}[@name='$query']]");

		foreach ($q as $feature)
		{
			removeChildNodes($feature);
			renameAndAttachNode($document, $feature, 'require', $features);
		}

		// find the feature it was required in
		$q = $glxpath->query("//registry/feature[remove/{$type}[@name='$query']]");
		foreach ($q as $feature)
		{
			removeChildNodes($feature);
			renameAndAttachNode($document, $feature, 'remove', $features);
		}
	}

	function queryRelatingExtensions(&$glxpath, &$document, $type, $query, &$extensions)
	{
    	// find the extension it was required in
		$q = $glxpath->query("//registry/extensions/extension[require/{$type}[@name='$query']]");

		foreach ($q as $extension)
		{
			removeChildNodes($extension);
			renameAndAttachNode($document, $extension, 'require', $extensions);
		}

		// find the extension it was required in
		$q = $glxpath->query("//registry/extensions/extension[remove/{$type}[@name='$query']]");
		foreach ($q as $extension)
		{
			removeChildNodes($extension);
			renameAndAttachNode($document, $extension, 'remove', $extensions);
		}
	}

	function queryCommand(&$glxpath, &$document, &$glquery, $query)
	{
		// search and output single COMMAND
		$q = $glxpath->query("/registry/commands/command[proto[name='$query']]");

		if(0 == $q->length)
			return;

		$node = cloneAndAttachNode($document, $q->item(0), $glquery);

		$features = createAndAttachNode($document, 'features', $node);
		queryRelatingFeatures($glxpath, $document, 'command', $query, $features);
		$extensions = createAndAttachNode($document, 'extensions', $node);
		queryRelatingExtensions($glxpath, $document, 'command', $query, $extensions);
	}

	function queryEnum(&$glxpath, &$document, &$glquery, $query)
	{
		// search ENUM by name
		$q = $glxpath->query("/registry/enums/enum[@name='$query']");
		if(1 != $q->length)
		{
			// search ENUM by value
			$q = $glxpath->query("/registry/enums/enum[@value='$query']");
			if(1 != $q->length)	
				return;
		}

		$enum = $q->item(0);
		$query = $enum->getAttribute('name');

		$node = cloneAndAttachNode($document, $enum, $glquery);

		// TODO: retrieve enums class..

		// an enum can relate to either extensions and features
		$features = createAndAttachNode($document, 'features', $node);
		queryRelatingFeatures($glxpath, $document, 'enum', $query, $features);
		$extensions = createAndAttachNode($document, 'extensions', $node);
		queryRelatingExtensions($glxpath, $document, 'enum', $query, $extensions);
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


//	$xsl = 'href="glresult.xsl" type="text/xsl"';  
//	$stylesheet = new DOMProcessingInstruction('xml-stylesheet',$xsl);

	$result = new DOMDocument('1.0');
	$result->preserveWhiteSpace = true;
	$result->formatOutput = true;
//	$result->appendChild($stylesheet); 

	$query = value("query", "");
	if(empty($query))
	{
		echo $result->saveXML();
		return;
	}

	// load gl spec

	$gldoc = new DOMDocument();
	$gldoc->load('gl.xml');

	$glxpath = new Domxpath($gldoc);

	$glquery = createAndAttachNode($result, 'glquery');

	queryCommand($glxpath, $result, $glquery, $query);
	queryEnum($glxpath, $result, $glquery, $query);


	echo xmlpp($result->saveXML(), false);
	//echo $result->saveXML();



//	echo $glquery->length;

	// search COMMAND
	//$query = $glxpath->query("//registry/commands/command/proto[contains(name,'glVertex')]");
	//$query = $glxpath->query("//registry/commands/command/proto[name='glVertex3f']");

	// search ENUM by name
	//$query = $glxpath->query("//registry/enums/enum[contains(@name,'GL_POLYGON_')]");
	//$query = $glxpath->query("//registry/enums/enum[@name='GL_POLYGON_BIT']");

	// search ENUM by value
	//$query = $glxpath->query("//registry/enums/enum[contains(@value,'0x00000004')]");
	//$query = $glxpath->query("//registry/enums/enum[@value='0x00000004']");


	// retrieve information for specific function
	//$query = $glxpath->query("//registry/feature/require/command[@name='glVertex3f']");

	//echo $query->length;
?>
<?php
    $gldates = array(
      '1.0' => '1 July 1994'
    , '1.1' => '4 March 1997'
    , '1.2' => '1 April 1999'
    , '1.3' => '14 August 2001'
    , '1.4' => '24 July 2002'
    , '1.5' => '30 October 2003'
    , '2.0' => '22 October 2004'
    , '2.1' => '1 December 2006'
    , '3.0' => '23 September 2008'
    , '3.1' => '28 May 2009'
    , '3.2' => '7 December 2009'
    , '3.3' => '11 March 2010'
    , '4.0' => '11 March 2010'
    , '4.1' => '25 July 2010'
    , '4.2' => '27 April 2012'
    , '4.3' => '14 February 2013'
    );

    $glspecuris = array(
      '1.0' => 'http://www.opengl.org/registry/doc/glspec10.pdf'
    , '1.1' => 'http://www.opengl.org/registry/doc/glspec11.ps'
    , '1.2' => 'http://www.opengl.org/registry/doc/glspec121_bookmarked.pdf'
    , '1.3' => 'http://www.opengl.org/registry/doc/glspec13.pdf'
    , '1.4' => 'http://www.opengl.org/registry/doc/glspec14.pdf'
    , '1.5' => 'http://www.opengl.org/registry/doc/glspec15.pdf'
    , '2.0' => 'http://www.opengl.org/registry/doc/glspec20.20041022.pdf'
    , '2.1' => 'http://www.opengl.org/registry/doc/glspec21.20061201.pdf'
    , '3.0' => 'http://www.opengl.org/registry/doc/glspec30.20080923.pdf'
    , '3.1' => 'http://www.opengl.org/registry/doc/glspec31.20090528.pdf'
    , '3.2' => 'http://www.opengl.org/registry/doc/glspec32.compatibility.20091207.pdf'
    , '3.3' => 'http://www.opengl.org/registry/doc/glspec33.compatibility.20100311.pdf'
    , '4.0' => 'http://www.opengl.org/registry/doc/glspec40.compatibility.20100311.pdf'
    , '4.1' => 'http://www.opengl.org/registry/doc/glspec41.compatibility.20100725.pdf'
    , '4.2' => 'http://www.opengl.org/registry/doc/glspec42.compatibility.20120427.pdf'
    , '4.3' => 'http://www.opengl.org/registry/doc/glspec43.compatibility.20130214.pdf'
    );

    $glids = array(
      '1.0' => '10'
    , '1.1' => '11'
    , '1.2' => '12'
    , '1.3' => '13'
    , '1.4' => '14'
    , '1.5' => '15'
    , '2.0' => '20'
    , '2.1' => '21'
    , '3.0' => '30'
    , '3.1' => '31'
    , '3.2' => '32'
    , '3.3' => '33'
    , '4.0' => '40'
    , '4.1' => '41'
    , '4.2' => '42'
    , '4.3' => '43'
    );

    function echoGLFeatureBar()
    {
        global $glspecuris;
        global $glids;

        $width = 100.0 / count($glspecuris);
        echo "\n<div class=\"row features\"><div class=\"span8 offset2\"><div class=\"progress\">\n";
        foreach($glspecuris as $key => $value)
            echo "<a href=\"$value\"><div id=\"gl$glids[$key]\" class=\"bar bar-success\" style=\"width: $width%;\">$key</div></a>\n";
        echo "</div></div></div>\n";
    }

    function echoGLFeatureBarTooltips()
    {
        global $gldates;
        global $glids;

        foreach($gldates as $key => $value)
            echo "$(\"#gl$glids[$key]\").tooltip({title: '$value', placement: 'bottom'});";
    }
?>
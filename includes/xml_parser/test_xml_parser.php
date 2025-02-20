<?php
/*
 * test_xml_parser.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/PHPlibrary/test_xml_parser.php,v 1.5 2003/04/09 08:28:05 mlemos Exp $
 *
 */

?><HTML>
<HEAD>
<TITLE>Test for Manuel Lemos's XML parser PHP class</TITLE>
</HEAD>
<BODY>
<H1><CENTER>Test for Manuel Lemos's XML parser PHP class</CENTER></H1>
<HR>
<?
	require("xml_parser.php");

Function DumpArray(&$array,$indent)
{
	for(Reset($array),$node=0;$node<count($array);Next($array),$node++)
	{
		echo $indent."\"".Key($array)."\"=";
		$value=$array[Key($array)];
		if(GetType($value)=="array")
		{
			echo "\n".$indent."[\n";
			DumpArray($value,$indent."\t");
			echo $indent."]\n";
		}
		else
			echo "\"$value\"\n";
	}
}

Function DumpStructure(&$structure,&$positions,$path)
{
	echo "[".$positions[$path]["Line"].",".$positions[$path]["Column"].",".$positions[$path]["Byte"]."]";
	if(GetType($structure[$path])=="array")
	{
		echo "&lt;".$structure[$path]["Tag"]."&gt;";
		for($element=0;$element<$structure[$path]["Elements"];$element++)
			DumpStructure($structure,$positions,$path.",$element");
		echo "&lt;/".$structure[$path]["Tag"]."&gt;";
	}
	else
		echo $structure[$path];
}

	$file_name="example.xml";
	$error=XMLParseFile($parser,$file_name,1,$file_name.".cache");
	if(strcmp($error,""))
		echo "<H2><CENTER>Parser error: $error</CENTER></H2>\n";
	else
	{
		echo "<H2><CENTER>Parsed file structure</CENTER></H2>\n";
		echo "<P>This example dumps the structure of the elements of an XML file by displaying the tags and data preceded by their positions in the file: line number, column number and file byte index.</P>\n";
		echo "<PRE>";
		DumpStructure($parser->structure,$parser->positions,"0");
		echo "</PRE>\n";
	}
?></BODY>
</HTML>

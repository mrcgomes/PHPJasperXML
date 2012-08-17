<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 error_reporting(E_ALL  & ~E_NOTICE & ~E_WARNING);

require_once('../class/tcpdf/tcpdf.php');
require_once("../class/PHPJasperXML.inc.php");
require_once("../class/jasperJS.php");
require_once('setting.php');


class xmlJasper extends PHPJasperXML
{


public function transferXMLtoArray($fileName,$recordpath = 'record',$varpath = 'vars') 
{ 
if(!file_exists($fileName)) 
echo "File - $fileName does not exist"; 
else 
{ 
$this->m=0; 
$xmlAry = $this->xmlobj2arr(simplexml_load_file($fileName)); 

foreach($xmlAry['rpt'][$recordpath] as $item) 
{ 

$this->arraysqltable[] = $item;
$this->m++;
} 
foreach($xmlAry['rpt'][$varpath] as $item) 
{ 

$this->arraysqltable[] = $item;
$this->m++;
} 

if(isset($this->arrayVariable))	//if self define variable existing, go to do the calculation 
$this->variable_calculation($m); 
} 
} 

//wrote by huzursuz at mailinator dot com on 02-Feb-2009 04:44 
//http://hk.php.net/manual/en/function.get-object-vars.php 
public function xmlobj2arr($Data) 
{ 

if (is_object($Data)) 
{ 
foreach (get_object_vars($Data) as $key => $val) 
$ret[$key] = $this->xmlobj2arr($val); 
return $ret; 
} 
elseif (is_array($Data)) 
{ 
foreach ($Data as $key => $val) 
$ret[$key] = $this->xmlobj2arr($val); 
return $ret; 
} 
else 
return $Data; 
} 
function transferXLStoArray($filename)
{
    include("../class/excel_reader/reader.php");
    $datasheet = new Spreadsheet_Excel_Reader();
	// Set output Encoding.
 $datasheet->setUTFEncoder('iconv');
$datasheet->setOutputEncoding('UTF-8');
         $datasheet->setRowColOffset(0);
   $this->m=0;   
     	$datasheet->read($filename);
      foreach($datasheet->sheets as $sheet )
       {
	   $data = $sheet['cells'];
        $h = $data[0];
 foreach( $h as $key => $i)
    {
	$this->m++;
  
 $i  = trim($i);
 
      $i  = trim($i,'"');

      if ($mappedField[$i])
         $i =   $mappedField[$i];
    // $i = preg_replace('/[ ]/','_',$i);
     $i = preg_replace('/[\n\r]/','',$i);
       $i  = trim($i);
 
     $headers[$key] = $i;
    }
	

	unset($data[0]);
     foreach($data as $item)
    {
   
    $d = array();
    foreach($item as $key => $col)
      $d[$headers[$key]] = $col;
    $this->arraysqltable[] = $d;
}	
   
	  break;
       }
//var_dump(	 $this->arraysqltable);  
if(isset($this->arrayVariable))	//if self define variable existing, go to do the calculation 
$this->variable_calculation(	$this->m);
}
public $fontmap  =array();
function getFontMap($family)
 {
  
  $sub = $fontmap[$family];
  if (!empty($sub))
    return $sub;

  return $family;
 
 }


}

class myTCPDF extends TCPDF
  {
  	public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {

	parent::__construct($orientation , $unit , $format , $unicode, $encoding, $diskcache);
	$this->jpeg_quality = 90;
	$fontname = $this->addTTFfont('D:\projects\OE\OE Reports\fonts\pala.ttf', 'TrueTypeUnicode', '', 32);
	
	}
  }



                
$xml =  simplexml_load_file("D:\projects\OE\OE Reports\import\Quote.jrxml");

$PHPJasperXML = new xmlJasper("en","TCPDF","myTCPDF");
$PHPJasperXML->fontmap['Palatino Linotype'] = 'times';
$PHPJasperXML->report_path = "D:/OE Reports/";
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter=array("printType"=>"quote");
$PHPJasperXML->xml_dismantle($xml);
$PHPJasperXML->transferXLStoArray('D:\projects\OE\OE Reports\import\PriceListImport.xls'); 
				

$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file


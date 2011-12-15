<?php 
// ---------------------------------------------------------------------------- 
// Library - Class template 
// ---------------------------------------------------------------------------- 
// Copyright (C) 2001-2003 - Régis GAIDOT - http://regis.gaidot.net 
// ---------------------------------------------------------------------------- 
// This program is free software; you can redistribute it and/or 
// modify it under the terms of the GNU General Public License 
// as published by the Free Software Foundation; either version 2 
// of the License, or (at your option) any later version. 
// 
// This program is distributed in the hope that it will be useful, 
// but WITHOUT ANY WARRANTY; without even the implied warranty of 
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
// GNU General Public License for more details. 
// 
// You should have received a copy of the GNU General Public License 
// along with this program; if not, write to the Free Software 
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. 
// ---------------------------------------------------------------------------- 
/**
* Use : 
* include("class_template.php");
* 
* $t1 = new template();
* 
* $t1->addTpl("Main","main.html");
* $t1->addTpl("Sample1","tpl1.html");
* $t1->addTpl("Sample2","tpl2.html");
* $t1->addTpl("Sample3","tpl3.html");
* $t1->addTpl("Sample4","tpl4.html");
* $t1->addTpl("Sample5_1","tpl5_1.html");
* $t1->addTpl("Sample5_2","tpl5_2.html");
* 
* $vmain = array("titrepage" => "Class::template",
*                "sSample1" => "".$t1->getTpl("Sample1")."",
*                "sSample2" => "".$t1->getTpl("Sample2")."",
*                "sSample3" => "".$t1->getTpl("Sample3")."",
*                "sSample4" => "".$t1->getTpl("Sample4")."",
*                "Sample1" => "{tpl}",
*                "Sample2" => "{tpl}",
*                "Sample3" => "{tpl}",
*                "Sample4" => "{tpl}",
*                "Sample5_1" => "{tpl}");
* 
* $vsample = array("titre" => "MyPHPSoft",
*                  "description" => "MyPHPSoft - Description",
*                  "auteur" => "Régis Gaidot");
* 				
* $vsamplespe1 = array("titre" => "List", "Sample5_2" => "{tpl}");
* 
* $t1->setTpl("Sample1",$vsample);
* $t1->setTpl("Sample2",$vsample);
* $t1->setTpl("Sample3",$vsample);
* $t1->setTpl("Sample4",$vsample);
* $t1->setTpl("Sample5_2","PHP",TRUE);
* $t1->setTpl("Sample5_2","Zend Technologies",TRUE);
* $t1->setTpl("Sample5_2","MySQL",TRUE);
* $t1->setTpl("Sample5_1",$vsamplespe1);
* 
* $t1->setTpl("Main",$vmain);
* 
* echo $t1->getTplContent("Main") 
* 
**/

class template{ 
 var $tpls; 
 var $tplscontent; 
 var $UsePregMatchAll=TRUE; 

/** 
* Constructor 
*/ 
 function template(){ 
  $this->tpls = array(); 
  $this->tplscontent = array(); 
 } 

/** 
* Add template 
* @param        type 
* @aName        string 
* @aTpl         string 
*/ 
 function addTpl($aName,$aTpl){ 
  if(isset($this->tpls[$aName])){ 
   trigger_error('template::addTpls - Error: Template already existe !'); 
  } 
  if(is_file($aTpl)){ 
   $this->tpls[$aName] = implode("",file($aTpl)); 
  }elseif(is_string($aTpl)){ 
   $this->template[$aName] = $aTpl; 
  }else{ 
   trigger_error('template::addTpls - Error: File or String params !'); 
  } 
 } 

/** 
* Equality of preg_match_all 
* @param        type 
* @tpl          string 
*/ 
 function BuildArrayTags($tpl){ 
  while(ereg("\{([^}]+)\}", $tpl, $tmp)){ 
   $tpl=str_replace($tmp[0], '', $tpl); 
   $results[]=$tmp[1]; 
  } 
  return $results; 
 } 

/** 
* Joint data with the template 
* @param        type 
* @aName        string 
* @aValues      array/string 
* @aAppend      bool 
*/ 
 function setTpl($aTpl,$aValues,$aAppend=FALSE){ 
  $toks = array(); 
  if($this->UsePregMatchAll){ 
   preg_match_all("/\\{([^}]+)\\}/", $this->tpls[$aTpl], $toks, PREG_PATTERN_ORDER); 
   $toks = $toks[1]; 
  }else{ 
   $toks = $this->BuildArrayTags($this->tpls[$aTpl]); 
  } 
  if(is_array($aValues)){ 
   $tmp = $this->tpls[$aTpl]; 
   for($i=0;$i<=count($toks);$i++){ 
    if($aValues[$toks[$i]]=="{tpl}"){ 
     $tmp = str_replace('{'.$toks[$i].'}', $this->tplscontent[$toks[$i]],$tmp); 
    }else{ 
     $tmp = str_replace('{'.$toks[$i].'}',$aValues[$toks[$i]],$tmp); 
    } 
   } 
   if($aAppend){ 
    $this->tplscontent[$aTpl] .= $tmp; 
   }else{ 
    $this->tplscontent[$aTpl] = $tmp; 
   } 
  }elseif(is_string($aValues)){ 
   if($aAppend){ 
    $this->tplscontent[$aTpl] .= str_replace('{'.$toks[0].'}',$aValues,$this->tpls[$aTpl]); 
   }else{ 
    $this->tplscontent[$aTpl] = str_replace('{'.$toks[0].'}',$aValues,$this->tpls[$aTpl]); 
   } 
  }else{ 
   trigger_error('template::setTpl - Error: Array or String params !'); 
  } 
 } 

/** 
* Show a template with the data 
* @param        type 
* @aTpl         string 
*/ 
 function getTplContent($aTpl){ 
  return $this->tplscontent[$aTpl]; 
 } 

/** 
* Show the structure of the template 
* @param        type 
* @aTpl         string 
*/ 
 function getTpl($aTpl){ 
  return $this->tpls[$aTpl]; 
 } 

} 
?>

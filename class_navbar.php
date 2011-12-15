<?php 
// ---------------------------------------------------------------------------- 
// Library - Class NavBar 
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
* include("class_navbar.php");
* 
* $result = mysql_query("SELECT id, name FROM table WHERE name='php' 
* 		LIMIT ".($deb*$limitvalue).", ".$limitvalue."");
* 
* $navbar = new navbar($totalvalue,$deb,$limitvalue);
* 
* $navbar->SetStrBegin("<a href=\"?_URL_\"&amp;deb=[page]\">Begin</a>");
* $navbar->SetStrEnd("<a href=\"?_URL_\"&amp;deb=[page]\">End</a>");
* 
* $navbar->SetStrLink("<a href=\"?_URL_\"&amp;deb=[page]\">[n]</a>");
* 
* $navbar->SetStrNextOn("<a href=\"?_URL_\"&amp;deb=[page]\">Next</a>");
* $navbar->SetStrPreviewOn("<a href=\"?_URL_\"&amp;deb=[page]\">Preview</a>");
* 
* $navbar->SetSeparator(" &middot; ");
* $navbar->SetStrNextOff("Next");
* $navbar->SetStrPreviewOff("Preview");
* echo $navbar->showbar();
* 
**/
class navbar { 
 var $nb_total;  
 var $nb_result_per_page; 
 var $nb_page; 
 var $separator; 
 var $current_page;  
 var $str_begin;  
 var $str_end;  
 var $str_next_on;  
 var $str_next_off; 
 var $str_preview_on; 
 var $str_preview_off; 
 var $str_link;  

 function SetNbTotal($n){ 
  $this->nb_total = $n; 
 } 

 function GetNbTotal(){ 
  return $this->nb_total; 
 } 

 function SetNbPage($n){ 
  $this->nb_page = $n; 
 } 

 function GetNbPage(){ 
  return $this->nb_page-1; 
 } 

 function SetCurrentPage($n){ 
  $this->current_page = $n; 
 } 

 function GetCurrentPage(){ 
  return $this->current_page; 
 } 

 function SetNbResultPerPage($n){ 
  $this->nb_result_per_page = $n; 
 } 

 function GetNbResultPerPage(){ 
  return $this->nb_result_per_page; 
 } 

 function SetStrBegin($str){ 
  $this->str_begin = $str; 
 } 

 function GetStrBegin(){ 
  return $this->str_begin; 
 } 

 function SetStrEnd($str){ 
  $this->str_end = $str; 
 } 

 function GetStrEnd(){ 
  return $this->str_end; 
 } 

 function SetStrNextOn($str){ 
  $this->str_next_on = $str; 
 } 

 function GetStrNextOn(){ 
  return $this->str_next_on; 
 } 
  
 function SetStrNextOff($str){ 
  $this->str_next_off = $str; 
 } 

 function GetStrNextOff(){ 
  return $this->str_next_off; 
 } 

 function SetStrPreviewOn($str){ 
  $this->str_preview_on = $str; 
 } 

 function GetStrPreviewOn(){ 
  return $this->str_preview_on; 
 } 

 function SetStrPreviewOff($str){ 
  $this->str_preview_off = $str; 
 } 

 function GetStrPreviewOff(){ 
  return $this->str_preview_off; 
 } 

 function SetStrLink($str){ 
  $this->str_link = $str; 
 } 

 function GetStrLink(){ 
  return $this->str_link; 
 } 

 function SetSeparator($str){ 
  $this->separator = $str; 
 } 

 function GetSeparator(){ 
  return $this->separator; 
 } 

 function navbar($total,$cpage=0,$rpage=10,$nbpage=12){ 
  $this->SetNbTotal($total); 
  $this->SetNbPage($nbpage); 
  $this->SetCurrentPage($cpage); 
  $this->SetNbResultPerPage($rpage); 
 } 

 function showbar() { 
  $result = ""; 
  $pages = 0; 
  $cpt_begin = 0; 
  $cpt_end = 0; 
  $pages = $this->GetNbTotal() / $this->GetNbResultPerPage();  

  // Begin 
  $result .= str_replace("[page]",0,$this->GetStrBegin()).$this->GetSeparator(); 

  // Preview 
  if($this->GetCurrentPage()>=1){ 
   $result .= str_replace("[page]",($this->GetCurrentPage()-1),$this->GetStrPreviewOn()).$this->GetSeparator(); 
  }else{ 
   $result .= $this->GetStrPreviewOff().$this->GetSeparator(); 
  } 
         
  // n' pages 
  $lastpagecalc = floor($this->GetNbTotal()/$this->GetNbResultPerPage()); 
  $pagesnb = floor($this->GetNbPage()/2); 
  while ($pagesnb > 0) { 
   $previous = $this->GetCurrentPage() - $pagesnb; 
   $next = $this->GetCurrentPage() + $pagesnb; 
   if ($previous >= 0) { 
    $previouspages .= str_replace("[n]",($previous+1),str_replace("[page]",$previous,$this->GetStrLink())).$this->GetSeparator(); 
   } 
   if ($next <= $lastpagecalc) { 
    $nextpages = str_replace("[n]",($next+1),str_replace("[page]",$next,$this->GetStrLink())).$this->GetSeparator().$nextpages; 
   } 
   $pagesnb--; 
  } 
  $result .= $previouspages.($this->GetCurrentPage()+1).$this->GetSeparator().$nextpages; 

  // Next             
  if($this->GetCurrentPage()<$lastpagecalc){ 
   $result .= str_replace("[page]",($this->GetCurrentPage()+1),$this->GetStrNextOn()).$this->GetSeparator();         
  }else{ 
   $result .= $this->GetStrNextOff().$this->GetSeparator(); 
  } 

  // End 
  $result .= str_replace("[page]",$lastpagecalc,$this->GetStrEnd()); 

  return $result; 
 } 
} 
?>

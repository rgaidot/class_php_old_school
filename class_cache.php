<?php 
// ---------------------------------------------------------------------------- 
// Library - Cache partial source code HTML 
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
* include ("class_cache.php"); 
* $cache = new cache("YourNameCache",Period,"YourDirCache"); 
* if($cache->isExpered()){ 
*  $html="<Your Source HTML>" 
*  $cache->SetCache($html); 
* } 
* echo $cache->GetCache("YourNameCache"); 
* 
**/ 

class cache { 
  var $name; 
  var $period=900; 
  var $periodOfCleanDirCache=172800; 
  var $dircache="cache/"; 
  var $filename; 
  var $content; 

/** 
* Constructor 
* 
* @param        type 
* @name         string 
* $period       int 
* $dircache     string 
*/ 
 function cache($aName,$aPeriod,$aDircache){ 
  $this->name = $aName; 
  $this->period = $aPeriod; 
  $this->dircache = $aDircache; 
  $this->SetCacheFileName($this->name); 
 } 

/** 
* Attribute name file cache 
* 
*/ 
 function SetCacheFileName(){ 
  $this->filename = $this->dircache . md5($this->name) . ".cachemd5.html"; 
 } 

/** 
* Attribute a time for delete files cache 
* 
* @param        type 
* $aValues      int 
*/ 
 function SetPeriodOfCleanDirCache($aValues){ 
  $this->periodOfCleanDirCache = $aValues; 
 } 

/** 
* Delete files cache 
* 
*/ 
 function DeleteFileCache($aFilename){ 
  return @unlink($aFilename); 
 } 

/** 
* Expered files cache ? 
* 
*/ 
 function isExpered(){ 
  $result=false; 
  $this->CleanDirCache(); 
  if(!file_exists($this->filename)||((time()-filemtime($this->filename))>$this->period)||!$file=fopen($this->filename,"r")){ 
   $result=true; 
  } 
  return $result; 
 } 

/** 
* Generate file cache 
* 
* @param        type 
* $html         string 
*/ 
 function SetCache($aHtml){ 
  $this->content=$aHtml; 
  $this->DeleteFileCache($this->filename); 
  if($file = fopen($this->filename,"w")){ 
   fwrite($file,$this->content); 
   fclose($file); 
  } 
 } 

/** 
* Show file cache 
* 
*/ 
 function GetCache(){ 
  $file = fopen($this->filename,"r"); 
  $this->content = fread($file, filesize($this->filename)); 
  fclose($file); 
  return $this->content; 
 } 

/** 
* Clean dir cache 
* 
*/ 
 function CleanDirCache(){ 
  if(file_exists($this->filename)){ 
   if(filemtime($this->filename)<(time()-$this->periodOfCleanDirCache)){ 
    $dircache = opendir($this->dircache); 
    while($filename = readdir($dircache)){ 
     if(($filename=='.')||($filename=='..')) 
      continue; 
     $this->DeleteFileCache($this->dircache.$filename); 
    } 
   } 
  } 
 } 

/** 
* Get name file cache 
* 
* @param        type 
*/ 
 function GetCacheFileName(){ 
  return $this->filename; 
 } 

/** 
* Period of valid file cache 
* 
* @param        type 
*/ 
 function GetPeriodCache(){ 
  return $this->period; 
 } 

/** 
* Get time remaining valid of file cache 
* 
* @param        type 
*/ 
 function GetTimeOutFileCache(){ 
  return $this->period-(time()-filemtime($this->filename)); 
 } 

/** 
* Get name cache 
* 
* @param        type 
*/ 
 function GetNameCache(){ 
  return $this->name; 
 } 
  
} 
?> 

<?php 
// ---------------------------------------------------------------------------- 
// Library - Class Abstract MySQL 
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
* include ("class_mysql.php"); 
* $dbs = new data(0,$Hote, $Base, $User, $Pass); 
* if(!$dbs->connect()) 
*  die($dbs->error); 
* if(!$dbs->query("Your_Query_SQL")) 
*  die($dbs->error); 
* $RecordSet = $dbs->numrows(); 
* while($dbs->nextrecord()){ 
*  echo $dbs->value("NameChp"); 
* }
* 
**/ 

class data{ 
 var $hostname = ""; 
 var $database = ""; 
 var $login = ""; 
 var $password = ""; 
 var $idresult = 0; 
 var $idconnect = 0; 
 var $error = ""; 
 var $record = array(); 
 var $row = 0; 
 var $memfree = 0; 

/** 
 * Constructor 
 * 
 * @param    $memfree        int 
 * @param    $hostname       string 
 * @param    $database       string 
 * @param    $login            string    
 * @param    $password       string     
 * 
 * @access     public 
 */ 
 function data($memfree,$hostname,$database,$login,$password){ 
  $this->memfree = $memfree; 
  $this->hostname = $hostname; 
  $this->database = $database; 
  $this->login = $login; 
  $this->password = $password; 
 } 

/** 
 * Connect 
 * 
 * @return    bool 
 * 
 * @access     public 
 */ 
 function connect(){ 
  if(($this->idconnect = @mysql_connect($this->hostname, $this->login, $this->password)) == false){ 
   $this->error = "Impossible de creer une connexion persistante !"; 
   return(0); 
  } 
  if(@mysql_select_db($this->database, $this->idconnect) == false){ 
   $this->error = "Impossible de selectionner la base !"; 
   return(0); 
  } 
  return($this->idconnect); 
 } 

/** 
 * Disconnect 
 * 
 * @access     public 
 */ 
 function disconnect(){ 
  $this->mysql_close; 
 } 

/** 
 * Free men 
 * 
 * @access     private 
 */ 
 function free(){ 
  if(@mysql_free_result($this->idresult) == false) 
   $this->error = "Erreur lors de la tentative de liberation de memoire"; 
  $this->idresult = 0; 
 } 

/** 
 * Execute query 
 * 
 * @access     public 
 */ 
 function query($query = ""){ 
  $rtval = 0; 
  if($this->idconnect != 0){ 
   if($this->idresult != 0){ 
    if($this->memfree == 1) 
     $this->free(); 
   } 
   if(($this->idresult = mysql_query($query, $this->idconnect)) == false) 
    $this->error = "<b>Impossible de lancer la requete :</b><br>$query"; 
   else{ 
    $rtval = $this->idresult; 
    $this->row = 0; 
   } 
  }else 
   $this->error = "Impossible de lancer une requete, il n'existe pas de connexion !"; 
  return($rtval); 
 } 

/** 
 * Result to record 
 * 
 * @access     private 
 */ 
 function affecresult(){ 
  $this->record = @mysql_fetch_array($this->idresult); 
 } 

/** 
 * Move cursor (+1) 
 * 
 * @access    public 
 */ 
 function nextrecord(){ 
  $rtval = 0; 
  if($this->idresult != -1){ 
   $this->affecresult(); 
   $this->row = $this->row + 1; 
   $stat = is_array($this->record); 
   if(!$stat && $this->memfree) 
    $this->free(); 
    if($stat) 
     $rtval = 1; 
  }else 
   $this->error = "Impossible d'avancer le resultat, pas d'id !"; 
   return($rtval); 
 } 

/** 
 * Move cursor to 
 * 
 * @param    $row    int  
 * 
 * @access     public 
 */ 
 function seekrecord($row){ 
  $this->row = $row; 
  return(@mysql_data_seek($this->idresult,$row)); 
 } 

/** 
 * Number of lines (record) 
 * 
 * @return      int 
 * 
 * @access     public 
 */ 
 function numrows(){ 
  return(@mysql_num_rows($this->idresult)); 
 } 

/** 
 * Value of field 
 * 
 * @param    $col    string   
 * 
 * @return    string 
 * 
 * @access     public 
 */ 
 function value($col){ 
  return($this->record[$col]); 
 } 

/** 
 * Result 
 * 
 * @return      array 
 * 
 * @access     public 
 */ 
 function result(){ 
  return($this->record); 
 } 

/** 
 * Get name tables from database 
 * 
 * @return      array 
 * 
 * @access     public 
 */ 
 function gettables(){ 
  if(!$this->query("SHOW TABLES FROM ".$this->database)) 
   die($this->error);         
  $i=0;  
  if($this->numrows()<>0){ 
    while($this->nextrecord()){ 
     $tables[$i++] = $this->value("Tables_in_".$this->database); 
    } 
  } 
  return $tables;  
 }  
  
/** 
 * Get fields of table 
 * 
 * @param    $tablename    string   
 * 
 * @return      array 
 * 
 * @access     public 
 */ 
 function getfieldstable($tablename){ 
  if(!$this->query("SHOW FIELDS FROM ".$tablename)) 
   die($this->error);        
  $i=0;  
  if($this->numrows()<>0){ 
    while($this->nextrecord()){ 
     $fields[$i++] = array('Field'=>$this->value("Field"), 
         'Type'=>$this->value("Type"), 
        'Null'=>$this->value("Null"), 
        'Key'=>$this->value("Key"), 
        'Default'=>$this->value("Default"), 
        'Extra'=>$this->value("Extra")); 
    } 
  } 
  return $fields;  
 }   
  
/** 
 * Get keys of table 
 * 
 * @param    $tablename    string   
 * 
 * @return      array 
 * 
 * @access     public 
 */ 
 function getkeystable($tablename){ 
  if(!$this->query("SHOW KEYS FROM ".$tablename)) 
   die($this->error);       
  $i=0;  
  if($this->numrows()<>0){ 
    while($this->nextrecord()){ 
     $keys[$i++] = array('Table'=>$this->value("Table"), 
         'Non_unique'=>$this->value("Non_unique"), 
        'Key_name'=>$this->value("Key_name"), 
        'Seq_in_index'=>$this->value("Seq_in_index"), 
        'Column_name'=>$this->value("Column_name"), 
        'Collation'=>$this->value("Collation"), 
        'Cardinality'=>$this->value("Cardinality"), 
        'Sub_part'=>$this->value("Sub_part"), 
        'Packed'=>$this->value("Packed"),                                 
        'Comment'=>$this->value("Comment")); 
    } 
  } 
  return $keys;  
 }   
  
/** 
 * Get struct table 
 * 
 * @param    $tablename    string   
 * 
 * @return      string 
 * 
 * @access     public 
 */ 
 function getstructable($tablename){  
  $create .= "CREATE TABLE " .$tablename ."(\n"; 
  //Fields 
  $flieds = $this->getfieldstable($tablename); 
  for($j=0;$j<count($flieds);$j++){ 
   $create .= $flieds[$j]['Field']." ".$flieds[$j]['Type']." ";  
   if($flieds[$j]['Null'] != "YES")  $create .= " NOT NULL "; 
   if($flieds[$j]['Default'] != "")  $create .= " DEFAULT '".$flieds[$j]['Default']."' "; 
   if ($flieds[$j]['Extra'] != "")  $create .= $flieds[$j]['Extra'];           
   $create .= ",\n"; 
  } 
  $create = ereg_replace(",\n" . "$", "", $create); 
  //Keys     
  $keys = $this->getkeystable($tablename); 
  for($k=0;$k<count($keys);$k++){ 
  $key_name    = $keys[$k]['Key_name']; 
  $non_unique = $keys[$k]['Non_unique']; 
  $column_name = $keys[$k]['Column_name']; 
  $comment  = (isset($keys[$k]['Comment'])) ? $keys[$k]['Comment'] : ''; 
  $sub_part = (isset($keys[$k]['Sub_part'])) ? $keys[$k]['Sub_part'] : ''; 
  if($key_name!="PRIMARY" && $non_unique== 0) { 
   $key_name = "UNIQUE|$key_name"; 
  } 
  if($comment=="FULLTEXT") { 
   $key_name = "FULLTEXT|$key_name"; 
  }          
  if(!isset($index[$key_name])) { 
   $index[$key_name] = array(); 
  } 
  if($sub_part>1){ 
   $index[$key_name][] = $column_name . "(" . $sub_part . ")"; 
  }else{ 
   $index[$key_name][] = $column_name; 
  } 
 } 
 while(list($x, $columns)=@each($index)){ 
  $create .= ",\n";     
  if($x=="PRIMARY"){ 
   $create .= "PRIMARY KEY ("; 
  }elseif(substr($x, 0, 6)=="UNIQUE"){ 
   $create .= "UNIQUE " . substr($x, 7) . " ("; 
  }elseif(substr($x, 0, 8) == "FULLTEXT") { 
   $create .= "FULLTEXT " . substr($x, 9) . " ("; 
  }else{ 
   $create .= "KEY " . $x . " ("; 
  } 
  $create .= implode($columns, ', ') . ")"; 
 }      
 $create .= ");\n\n";  
 return $create;  
}    
  
/** 
 * Get content table 
 * 
 * @param    $tablename    string   
 * 
 * @return      string 
 * 
 * @access     public 
 */ 
 function getcontenttable($tablename,$clausequery=""){ 
  if(!$this->query("SELECT * FROM ".$tablename." ".$clausequery)) 
   die($this->error); 
  $fields_cnt = mysql_num_fields($this->idresult);        
  if($this->numrows()<>0){ 
   while($this->nextrecord()){ 
    $row = $this->result(); 
    $fields_list = "("; 
    for($i=0;$i<$fields_cnt;$i++){ 
     $fields_list .= mysql_field_name($this->idresult, $i) . ", "; 
    } 
    $fields_list = substr($fields_list, 0, -2); 
    $fields_list .= ")";         
    $insert .= "INSERT INTO ".$tablename." ".$fields_list." VALUES ("; 
    for($i=0;$i<$fields_cnt;$i++){ 
     if(!isset($row[$i])){ 
      $insert .= " NULL, "; 
     }elseif($row[$i]=='0' || $row[$i]!=''){ 
      $type = mysql_field_type($this->idresult, $i); 
      if($type=='tinyint' || $type=='smallint' || $type=='mediumint' || $type=='int' || 
        $type=='bigint' || $type=='timestamp') { 
       $insert .= $row[$i] . ', '; 
      }else{ 
       $dummy  = ''; 
       $srcstr = $row[$i]; 
       for($j=0;$j<strlen($srcstr);$j++){ 
        $yy = strlen($dummy); 
        if($srcstr[$j]=='\\') $dummy .= '\\\\'; 
        if($srcstr[$j]=='\'') $dummy .= '\\\''; 
        if($srcstr[$j]=="\x00") $dummy .= '\0'; 
        if($srcstr[$j]=="\x0a") $dummy .= '\n'; 
        if($srcstr[$j]=="\x0d") $dummy .= '\r'; 
        if($srcstr[$j]=="\x1a") $dummy .= '\Z'; 
        if(strlen($dummy)==$j) $dummy .= $srcstr[$j]; 
       } 
       $insert .= "'" . $dummy . "', "; 
      } 
     }else{ 
      $insert .= "'', "; 
     } 
    } 
    $insert = ereg_replace(', $', '', $insert); 
    $insert .= ");\n"; 
   } 
  } 
  return $insert; 
 } 
  
/** 
 * Dump database 
 * 
 * @param    $create        bool   
 * 
 * @return      string 
 * 
 * @access     public 
 */ 
 function dumpdatabase($create){  
  $tables = $this->gettables(); 
  for($i=0;$i<count($tables);$i++){ 
   if($create==true){   
    $dump .= $this->getstructable($tables[$i]); 
   } 
   $dump .= $this->getcontenttable($tables[$i]); 
   $dump .= "\n\n"; 
  }  
  return $dump;   
 } 
  
} 
?>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Custom Model 
* 
* @includes 
* 
* @link Model.php
*/

//included here so that we can directly implement it into models without inclusion
include_once(APPPATH.'models/InfModel.php');
class Base_model extends CI_Model
{
    
    private $log_path;
   
    
    /*****
    *  Failsafe loading parent constructor
    */
    public function __construct($arr = array())
    {
        parent::__construct($arr);
        $config =& get_config();
        $this->log_path=($config['log_path'] != '') ? $config['log_path'] : APPPATH.'logs/';
        unset($config);      
    }
    
       
    
    /**
    * Write a log file for Edit,Delete,Insert Operation.
    * Log file is saved into binary mode that is ".bin"
    * But opened as XML file.
    * 
    * @param string $s_log_msg
    * @param string $s_log_sql, serilized value of sql query and its values 
    * @param int $i_user_id
    * 
    * @returns true if success and false if failed
    */
    public function write_log($s_log_msg,$i_user_id,$s_log_sql="")
    {
        try{
			if(defined(SQL_LOGGING) && SQL_LOGGING) {
				////////Opening XML File For logging//////
				$s_log_file=date("Y-M").".bin";///saved as binary file. ex- 2010-Sep 
	
				if(file_exists($this->log_path.$s_log_file))
				{
				  //$logData=file_get_contents($this->log_path.$logFile);
				  //$xml=simplexml_load_string($logData); 			  
				  
				  $xml_obj=new SimpleXMLElement($this->log_path.$s_log_file,LIBXML_NOCDATA ,true);        
				  $log_obj=$xml_obj->addChild('Log');
				  $log_obj->addAttribute("date",date('Y-m-d H:i:s'));
				  $log_obj->addAttribute("user_id",$i_user_id);
				  $log_obj->addChild('Msg',"<![CDATA[".$s_log_msg."]]>");///msg tag
				  if(trim($s_log_sql)!="")
					$log_obj->addChild('Sql',"<![CDATA[".$s_log_sql."]]>");///sql tag
				  
				  unset($log_obj,$s_log_msg,$i_user_id,$s_log_sql);
				  //return $xml_obj->asXML($this->log_path.$s_log_file); 
				  ///////for using CDATA ///////
				  $file_data=$xml_obj->asXML();
				  $arr_search=array("<Msg>&lt;![CDATA","]]&gt;</Msg>","<Sql>&lt;![CDATA","]]&gt;</Sql>");
				  $arr_replace=array("<Msg><![CDATA","]]></Msg>","<Sql><![CDATA","]]></Sql>");
				  $file_data=str_replace($arr_search,$arr_replace,$file_data,$cnt);
				  file_put_contents($this->log_path.$s_log_file,$file_data);
				  unset($arr_search,$arr_replace);
				  return $file_data;
				 ///////end for using CDATA ///////           
				  
				}
				else///creating new xml file
				{
				  $xml_obj=new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><LogRegister></LogRegister>',LIBXML_NOCDATA);
				  $log_obj=$xml_obj->addChild('Log');
				  $log_obj->addAttribute("date",date('Y-m-d H:i:s'));
				  $log_obj->addAttribute("user_id",$i_user_id);
				  $log_obj->addChild('Msg',"<![CDATA[".$s_log_msg."]]>");///msg tag
				  if(trim($s_log_sql)!="")
					$log_obj->addChild('Sql',"<![CDATA[".$s_log_sql."]]>");///sql tag    
							  
				  unset($log_obj,$s_log_msg,$i_user_id,$s_log_sql);
				  //return $xml_obj->asXML($this->log_path.$s_log_file);           
				  ///////for using CDATA ///////
				  $file_data=$xml_obj->asXML();
				  $arr_search=array("<Msg>&lt;![CDATA","]]&gt;</Msg>","<Sql>&lt;![CDATA","]]&gt;</Sql>");
				  $arr_replace=array("<Msg><![CDATA","]]></Msg>","<Sql><![CDATA","]]></Sql>");
				  $file_data=str_replace($arr_search,$arr_replace,$file_data,$cnt);
				  file_put_contents($this->log_path.$s_log_file,$file_data);
				  unset($arr_search,$arr_replace);
				  return $file_data;
				 ///////end for using CDATA /////// 
							  
				}
				////////end Opening XML File For logging//////
				return  FALSE;  
			}
			else {
				return true;
			}
          
        }
        catch(Exception $err_obj)
        {
          show_error($err_obj->getMessage());
        }        
    }
    
    /******
    * put your comment there...
    * 
    * @param date $dt_date, dd-mmm-yyyy
    * @param int $i_user_id
    * @return array of logInformation
	* ex- read_log("29-Dec-2010") // will fetch all logs on the date 29-Dec-2010
	* read_log() // will fetch all logs in the directory
    */
    public function read_log($dt_date=null,$i_user_id=null)
    {
        try{
            $ret_=array();
            /**
            * Optional date value found then 
            * Search all XML data for that date and list it
            */
            if($dt_date && !$i_user_id)
            {
                $s_log_file=date("Y-M",strtotime($dt_date)).".bin";///saved as binary file. ex- 2010-Sep 
                if(file_exists($this->log_path.$s_log_file))
                {            
                    $ret_=$this->read_logxml($this->log_path.$s_log_file,array("dt_log"=>$dt_date));
                }
            }
            else{
                ////Get all files in the log directory///
                if ($handle = opendir($this->log_path)) 
                { 
                    $tmp=array();
                    /* This is the correct way to loop over the directory. */ 
                    while (false !== ($s_log_file = readdir($handle))) 
                    {  
                        if ($s_log_file != "." && $s_log_file != ".." && $s_log_file != "index.html") 
                        { 
                            //echo "$s_log_file\n"; 
                            if($i_user_id)
                            {
                               $tmp=$this->read_logxml($this->log_path.$s_log_file,array("user_id"=>$i_user_id)); 
                               $ret_=array_merge($ret_,$tmp);
                            }
                            else
                            {
                               $tmp=$this->read_logxml($this->log_path.$s_log_file);
                               $ret_=array_merge($ret_,$tmp);
                            }
                            
                        }                        
                        
                    }///end while 

                    closedir($handle); 
                }                
                ////end Get all files in the log directory///
            }///end else
            unset($dt_date,$i_user_id,$tmp,$handle,$s_log_file);
            //pr($ret_);
            return $ret_;
        }
        catch(Exception $err_obj)
        {
          show_error($err_obj->getMessage());
        }        
    }
    
    /****
    * put your comment there...
    * 
    * @param string $s_file 
    * @param array $filter [dt_log] or [user_id]
    * @return array
    */
    private function read_logxml($s_file,$filter=null)
    {
        try{
            $ret_=array();
			
            $xml_obj=simplexml_load_file($s_file,'SimpleXMLElement', LIBXML_NOCDATA);
            if(!empty($xml_obj)){
                $i_tot=count($xml_obj)-1;
                $i_cnt=0;
                while($i_tot>=0)
                {
                    $Log=$xml_obj->Log[$i_tot];
                    $b_matched=false;
                    if(is_array($filter)){   
                                   
                        $b_matched=($filter["dt_log"]!=""?(date("Y-M-d",strtotime(htmlentities($Log["date"])))==date("Y-M-d",strtotime($filter["dt_log"]))):false); 
                        $b_matched=($filter["user_id"]!=""?(intval(htmlentities($Log["user_id"]))==$filter["user_id"]) || $b_matched:$b_matched);                        
                    }
                    else{
                        ////Fetch all logs
                        $b_matched=true;
                    }    
                    
                    if($b_matched){
                        $ret_[$i_cnt]["dt_log"]=date("Y-M-d H:i:s",strtotime(htmlentities($Log["date"])));
                        $ret_[$i_cnt]["i_user_id"]=intval(htmlentities($Log["user_id"]));
                        $ret_[$i_cnt]["s_logmsg"]=trim(htmlentities($Log->Msg));  
                        $ret_[$i_cnt]["s_logsql"]=trim(htmlentities($Log->Sql));  
                        $i_cnt++;                      
                    }					 
                    $i_tot--;
                }///end while
            } 
            unset($xml_obj,$i_tot,$i_cnt,$b_matched,$Log,$s_file);
            return $ret_;       
        }
        catch(Exception $err_obj)
        {
          show_error($err_obj->getMessage());
        }        
    }
    
    public function __destruct()
    {
        
    }
    
}
/////end of class

<?php
  /*********
  * Author: 
  * Date  : 
  * Modified By: 
  * Modified Date:
  * 
  * Purpose:
  *  Interfaces For Models
  * 
  */
  
interface InfModel
{
  
  /******
  * This method will fetch all records from the db. 
  * 
  * @param string $s_where, ex- " status=1 AND deleted=0 " 
  * @param int $i_start, starting value for pagination
  * @param int $i_limit, number of records to fetch used for pagination
  */
  public function fetch_multi($s_where=null,$i_start=null,$i_limit=null);
  
  /****
  * Fetch Total records
  * @param string $s_where, ex- " status=1 AND deleted=0 " 
  * @returns int on success and FALSE if failed 
  */ 
  public function gettotal_info($s_where=null);           
  
  /*******
  * Fetches One record from db for the id value.
  * 
  * @param int $i_id
  */
  public function fetch_this($i_id);
  
  /***
  * Inserts new records into db. As we know the table name 
  * we will not pass it into params.
  * 
  * @param array $info, array of fields(as key) with values
  * @returns $i_new_id  on success and FALSE if failed 
  */
  public function add_info($info);
  /***
  * Update records in db. As we know the table name 
  * we will not pass it into params.
  * 
  * @param array $info, array of fields(as key) with values
  * @param int $i_id, id value to be updated used in where clause
  * @returns $i_rows_affected  on success and FALSE if failed 
  */
  public function edit_info($info,$i_id);
  /******
  * Deletes  all or single record from db
  *
  * @param int $i_id, id value to be deleted used in where clause 
  * $i_id=-1 to delete all records from db
  * @returns $i_rows_affected  on success and FALSE if failed 
  * 
  */
  public function delete_info($i_id);
  /****
  * Register a log for add,edit and delete operation
  * 
  * @param mixed $attr
  * @returns TRUE on success and FALSE if failed 
  */
  public function log_info($attr);
}
/////////End Interface////////

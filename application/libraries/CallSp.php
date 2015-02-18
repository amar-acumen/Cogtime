<?php

class CallSp
{
    public function CallSp(  )
    {
        
    }

    public function executeStoreProcedure( $query ,$return_rs=FALSE)
    {
        global $CI;
        $row    = array();
        //echo $query;
        //$mysqli              =   new mysqli($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
        //$conId               =   mysqli_connect('192.168.1.202', 'root', '', 'cogtime');
		$conId               =   mysqli_connect($CI->db->hostname, $CI->db->username, $CI->db->password, $CI->db->database);
        mysqli_set_charset($conId,"utf8");

        $rsDelete            =   mysqli_multi_query( $conId, $query );
        //$rsDelete            =   $mysqli->multi_query(  $query );

        $result              =   mysqli_store_result( $conId );
        //var_dump($result );
        if($return_rs)
          return $result;
        if( (count( $result) > 0) && $result )
        {
            //echo '<br>if : '.$query;
            while( $abi = mysqli_fetch_assoc( $result ) )
            {
                //$result              =   $mysqli->store_result( );
                //echo '<br>'.'1'.$query;
                $row[] = $abi;
                
                //exit;
            }
        }
        //var_dump($row);
        mysqli_close( $conId );
        //$mysqli->close( );
        return $row;
    }

}
?>

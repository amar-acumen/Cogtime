<?php
/*********
* Author: 
* Date  : 
* Modified By: 
* Modified Date:
* 
* Purpose:
*  Model For  * Utility model is to define all db/functionality
 * related functions used throughout the site
* 
* @package 
* @subpackage 
* 
* @link InfModel.php 
* @link Base_model.php
* @link controllers/
* @link views/
*/

class Admin_utility_model extends Base_model 
{

        # constructor definition...
     public function __construct() 
    {
        try
        {
          parent::__construct();
          $this->conf = get_config();
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }    
    }


        # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        #               DISPLAYORDER RELATED FUNCTIONs [BEGIN]
        # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


            /* Ranking Row Create Starts */
            function RankingRowCreate($OrderValue, $record_id, $tbl="", $where="")
            {
				
                $tbl = ( !empty($tbl) )? $tbl: $this->db->CMS_PAGE;
                
                $where = ( empty($where) )? "": $where;
                
                // get selected table's max-rank...
                $maxSQL = "SELECT IFNULL(MAX(i_order),1) AS `max_displayorder`
                          FROM ".$tbl." {$where}";
                $query = $this->db->query($maxSQL);
                $row = $query->row();
         		$MAX_DISPLAYORDER = $row->max_displayorder;
				
				
				  // get selected table's min-rank...
                $minSQL = sprintf("SELECT IFNULL(MIN(i_order),1) AS `min_displayorder`
                          FROM ".$tbl." {$where}";
                $query_min = $this->db->query($minSQL);
                $row_min = $query_min->row();
         		$MIN_DISPLAYORDER = $row_min->min_displayorder;

                $ImgRank="";
				
                if($OrderValue==1)
                {
                    if($OrderValue==$MAX_DISPLAYORDER)
                        $ImgRank = '<td align="center">-</td>';
                    else
                      /*  $ImgRank = "<td align='center'><a href='javascript:void(0)' onClick=\"displayOrderAJAX($record_id, 'up')\"><img src='../images/admin/arrow_up.png' alt='' /></a></td>";*/
						$ImgRank ="<td align='center'><a href='javascript:void(0);' onClick=\"displayOrderAJAX($record_id, 'up')\"><img alt='' src='".base_url()."images/up.png'></a></td>";
                }
                elseif($OrderValue < $MAX_DISPLAYORDER &&  $OrderValue != $MIN_DISPLAYORDER )
				{
                    /*$ImgRank="<td align='center'><a href='javascript:void(0)' onClick=\"displayOrderAJAX($record_id, 'dn')\"><img src='../images/admin/arrow_down.png' alt='' /></a> <a href='javascript:void(0)' onClick=\"displayOrderAJAX($record_id, 'up')\"><img src='../images/admin/arrow_up.png' alt='' /></a></td>";
					*/
					
					$ImgRank = "<td align='center'><a href='javascript:void(0);' onClick=\"displayOrderAJAX('$record_id', 'dn')\"><img alt='' src='".base_url()."images/dwn.png'></a>&nbsp;<a href='javascript:void(0);' onClick=\"displayOrderAJAX($record_id, 'up')\"><img alt='' src='".base_url()."images/up.png'></a></td>";
				}
				elseif($OrderValue < $MAX_DISPLAYORDER && $OrderValue == $MIN_DISPLAYORDER)
				 /* $ImgRank = "<td align='center'><a href='javascript:void(0)' onClick=\"displayOrderAJAX($record_id, 'up')\"><img src='../images/admin/arrow_up.png' alt='' /></a></td>";*/
				  $ImgRank ="<td align='center'><a href='javascript:void(0);'  onClick=\"displayOrderAJAX($record_id, 'up')\"><img alt='' src='".base_url()."images/up.png'></a></td>";
                elseif($OrderValue == $MAX_DISPLAYORDER)
                 /*   $ImgRank = "<td align='center'><a href='javascript:void(0)' onClick=\"displayOrderAJAX($record_id, 'dn')\"><img src='../images/admin/arrow_down.png' alt='' /></a></td>";*/
					$ImgRank ="<td align='center'><a href='javascript:void(0)'; onClick=\"displayOrderAJAX($record_id, 'dn')\"><img alt='' src='".base_url()."images/dwn.png'></a></td>";

                return $ImgRank;

            }
            /* Ranking Row Create Ends */

        # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        #               DISPLAYORDER RELATED FUNCTIONs [END]
        # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	/* Manage Ranking-Order Starts */
				function Ranking($to, $id, $where='', $tbl='')
				{
                $tbl = ( !empty($tbl) )? $tbl: $this->db->CMS_PAGE;
                
                /* Ranking Up Starts */
                if($to=='dn')
                {
                    $query_pageorder = "SELECT `i_order` FROM ".$tbl." WHERE `id` = '".$id."' ";
                    $query1 = $this->db->query($query_pageorder);
                    $row1 = $query1->row();
                    $pageOrder=$row1->i_order;

                    $query_pageid="SELECT `id` FROM  ".$tbl." WHERE `i_order`=('".$pageOrder."'-1) ";
                    $query2 = $this->db->query($query_pageid);
                    $row2 = $query2->row();
                    $pageid=$row2->id;

                    $upt_pageorder = sprintf("UPDATE %s SET `i_order`=(%s-1)
                                   WHERE `id` = %s ", $tbl, $pageOrder, $id);
                    $this->db->query($upt_pageorder);

                    $upt_pageorder1 = sprintf("UPDATE %s SET `i_order`=%s
                                    WHERE `id` = %s ", $tbl, $pageOrder, $pageid);
                    $this->db->query($upt_pageorder1);

                }
                /* Ranking Up Ends */

                /* Ranking Down Starts */
                if($to == 'up')
                {
                    $query_pageorder = "SELECT `i_order` FROM ".$tbl." WHERE `id` = '".$id."' ",
                                                                          , ;
                    $query1 = $this->db->query($query_pageorder);
                    $row1 = $query1->row();
                    $pageOrder=$row1->i_order;

                    $query_pageid=sprintf("SELECT `id` FROM  ".$tbl." WHERE `i_order`=('".$pageOrder."'+1) ";
                    $query2 = $this->db->query($query_pageid);
                    $row2 = $query2->row();
                    $pageid=$row2->id;

                    $upt_pageorder = sprintf("UPDATE %s SET `i_order`=(%s+1) WHERE `id` = %s ",
                                                                      $tbl, $pageOrder, $id);
                    $this->db->query($upt_pageorder);

                    $upt_pageorder1 = sprintf("UPDATE %s SET `i_order`=%s WHERE `id` = %s ",
                                                                        $tbl, $pageOrder, $pageid);
                    $this->db->query($upt_pageorder1);
                }
                /* Ranking Down Ends */

            }
			/* Manage Ranking-Order Ends */


            /* function to get maximum display-order [in time of INSERT action] */
            function getMaxDisplayOrder($tbl="", $where="")
            {
                $tbl = ( !empty($tbl) )? $tbl: $this->db->CMS_PAGE;
                
                $where = ( empty($where) )? " WHERE `i_is_active` = 1 ": $where;
                
                $SQL = "SELECT IFNULL(MAX(`i_displayorder`)+1,1) AS `max_displayorder` FROM ".$tbl." {$where}";
                $query = $this->db->query($SQL);
                $rows = $query->row();

                return $rows->max_displayorder;
            }
			
			
			

            /* Rearrange DisplayOrder [in case of delete/remove] Starts */
            function RearrangeOrder($pID, $tbl='', $where='')
            {
                $tbl = ( !empty($tbl) )? $tbl: $this->db->CMS_PAGE;
                
				$WHERE_COND = ( empty($where) )? " `i_is_active` = 1 ": $where;
				
                $SQL1 = "SELECT `i_displayorder` FROM ".$tbl." WHERE `id` = '".$pID."' AND {$WHERE_COND} ";
                $query1 = $this->db->query($SQL1);

                $row1 = $query1->row();
                $DisplayOrder = $row1->i_displayorder;

                $SQL2 = "SELECT `id`, `i_displayorder`
								 FROM ".$tbl."
								 WHERE `i_displayorder` > '".$DisplayOrder."' AND {$WHERE_COND}
								 ORDER BY `i_displayorder` ASC ";
                $query2 = $this->db->query($SQL2);
                $rows = $query2->result_array();

                $recCount = count($rows);

                for($i=0; $i<$recCount; $i++)
                {
                      $prevContentId = $rows[$i]['id'];
                      $prevDisplayOrder = $rows[$i]['i_displayorder'];

                      $newDisplayOrder = $prevDisplayOrder - 1;

                      $updtSQL = sprintf("UPDATE %s
										  SET `i_displayorder` = %s
										  WHERE `id` = %s AND %s ", $tbl, $newDisplayOrder, $prevContentId, $WHERE_COND);
                      $this->db->query($updtSQL);
                }

            }

}   // end of class definition...

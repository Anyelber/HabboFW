<?php
include "../configs/configs.php";
include "../configs/functions.php";
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array( 'id', 'habbo', 'created_by', 'user', 'rol', 'firma', 'especial', 'placa_paga', 'placa_boni');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "users";
	
	/* Database connection information */
	$gaSql['user']       = $pdo_user;
	$gaSql['password']   = $pdo_password;
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] = new PDO('mysql:host=localhost;dbname='.$pdo_db, $gaSql['user'], $gaSql['password']);
	
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['start'] ) && $_GET['length'] != '-1' )
	{
		$sLimit = "LIMIT ".htmlspecialchars( $_GET['start'] ).", ".
			htmlspecialchars( $_GET['length'] );
	}else{
        $sLimit = "LIMIT 10";
    }
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".htmlspecialchars( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['search']['value'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".htmlspecialchars( $_GET['search']['value'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	// for ( $i=0 ; $i<count($aColumns) ; $i++ )
	// {
	// 	if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
	// 	{
	// 		if ( $sWhere == "" )
	// 		{
	// 			$sWhere = "WHERE ";
	// 		}
	// 		else
	// 		{
	// 			$sWhere .= " AND ";
	// 		}
	// 		$sWhere .= $aColumns[$i]." LIKE '%".htmlspecialchars($_GET['sSearch_'.$i])."%' ";
	// 	}
	// }
	
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	$rResult = $gaSql['link']->prepare($sQuery);
    $rResult->execute();
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $gaSql['link']->prepare($sQuery);
    $rResultFilterTotal->execute();
	$aResultFilterTotal = $rResultFilterTotal->fetch();
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = $gaSql['link']->prepare($sQuery);
    $rResultTotal->execute();
	$aResultTotal =$rResultTotal->fetch();
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */

     $data = array();
	
	
	while ( $aRow = $rResult->fetch() )
	{
		$row = array();
        if($aRow['especial'] == 1){
            $especial = "<b class='badge badge-success'>Si</b>";
        }else{
            $especial = "<b class='badge badge-danger'>No</b> ".$r['especial'];
        }
    
    
            $temp = array();
    
			array_push($temp, $aRow['id']);
            array_push($temp, '<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.$aRow['habbo'].'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$aRow['id'].'"/> '.$aRow['habbo']);


			if(strlen(nombre_habbo($aRow['created_by']))>0){
				array_push($temp, '<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.nombre_habbo($aRow['created_by']).'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$aRow['created_by'].'"/> '.nombre_habbo($aRow['created_by']));
			}else{
				array_push($temp, '<img src="imgs/deleted.png" style="width:50px; height:auto;"/> Deleted User');
			}



			array_push($temp, $aRow['user']);
            array_push($temp, fecha_placa_seg($aRow['id']));
            array_push($temp, nombre_rol_usuario($aRow['id']));
            array_push($temp, $aRow['firma']);
            array_push($temp, $especial);
    
            $checked1 = "";
            $checked2 = "";
    
            if($aRow['placa_paga'] == 1){ $checked1 = "checked"; }
    
            if($aRow['placa_boni'] == 1){ $checked2 = "checked"; }
            
            if(is_admin_or_more(rol($_SESSION['id']))){
    
                array_push($temp, '<input type="checkbox" id="paga" '.$checked1.' onclick="marcarPaga('.$aRow['id'].')"/> Paga
                <input type="checkbox" id="boni" '.$checked2.' onclick="marcarBoni('.$aRow['id'].')"/> Boni');
                        
            }else{
                array_push($temp, '');
            }
    
            $botones = '<a href="?p=ver_detalles_habbo&id='.$aRow['id'].'" data-toggle="tooltip" title="Ver Perfil"><i class="fas fa-eye"></i></a>';
    
    
                            if(is_admin_or_more(rol($_SESSION['id']))){
    
                                $botones .= '&nbsp;
                                <a href="?p=editarusuario&id='.$aRow['id'].'" data-toggle="tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                                &nbsp;
                                <a href="?p=traslado&id='.$aRow['id'].'" data-toggle="tooltip" title="Traslado"><i class="fas fa-arrow-right"></i></a>';
                            
                            }
    
                            if(is_manager_or_more(rol($_SESSION['id']))){
    
                                $botones .= '&nbsp;
                                <a href="#" onclick="eliminarusuario('.$aRow['id'].',\''.$aRow['habbo'].'\')" data-toggle="tooltip" title="Eliminar"><i class="fas fa-trash"></i></a>
                                ';
                            
                          
                            }
    
                            array_push($temp, $botones);
                            
    
           
    
            array_push($data,$temp);
	}

    $output = array(
		"draw" => $_GET['draw'],
		"recordsTotal" => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data" => $data
	);
	
	echo json_encode( $output );
?>
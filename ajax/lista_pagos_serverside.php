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
	$aColumns = array( 'id', 'habbo', 'created_by', 'user', 'rol', 'firma', 'especial', 'placa_paga', 'placa_boni' );
	
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
		$sWhere .= ') AND rol > 4';
	}else{
        $sWhere = "WHERE rol > 4";
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
        ORDER BY placa_paga DESC, placa_boni DESC
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
	
	
	while ( $r = $rResult->fetch() )
	{
		$temp = array();

        $qa = $pdo->prepare("SELECT id FROM ascensos WHERE id_dio = :id AND pagado = 0");
        $qa->execute([$r['id']]);

        $ascensos = $qa->rowCount();

       


        if(tiene_save($r['id'])){
            $save = '<span class="badge badge-success">Si</span>';
        }else{
            $save = '<span class="badge badge-danger">No</span>';
        }

        $es_especial = es_especial($r['id']);




        if($r['rol'] >=5 && $r['rol'] <=11){
            $requisito = $req_pago_seg;
        }elseif($r['rol'] >= 12 && $r['rol'] <= 18){
            $requisito = $req_pago_tec;
        }elseif($r['rol'] >= 19 && $r['rol'] <= 25){
            if($es_especial){
                $requisito = $req_pago_especial_log;
            }else{
                $requisito = $req_pago_log;
            }
        }elseif($r['rol'] >= 26 && $r['rol'] <= 32){
            if($es_especial){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_sup;
            }
        }elseif($r['rol'] >= 33 && $r['rol'] <= 39){
            if($es_especial){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_dir;
            }
        }elseif($r['rol'] >= 40 && $r['rol'] <= 46){
            if($es_especial){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_pre;
            }
        }elseif($r['rol'] >= 47 && $r['rol'] <= 53){
            if($es_especial){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_eli;
            }
        }elseif($r['rol'] >= 54 && $r['rol'] <= 60){
            if($es_especial){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_jtd;
            }
        }elseif($r['rol'] < 5 || $r['rol'] > 60){
            $requisito = "na";
        }


        if($requisito == "na"){
            $status = '<span class="badge badge-warning">N/A</span>';
        }else{
            if($requisito[0] == "time"){
                if(horas_trabajadas($r['id']) >= $requisito[1]){
                    if(horas_trabajadas($r['id']) >= ($requisito[2] + $requisito[1])){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }

            }elseif($requisito[0] == "asc"){
                if(cant_ascensos($r['id']) >= $requisito[1]){
                    if(cant_ascensos($r['id']) >= ($requisito[2] + $requisito[1])){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }
            }elseif($requisito[0] == "asct"){
                if(cant_ascensos($r['id']) >= $requisito[1]){
                    if(cant_times_validos_user($r['id']) >= $requisito[2]){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }
            }elseif($requisito[0] == "timea"){
                if(cant_times_validos_user($r['id'])>=$requisito[1]){
                    if(cant_ascensos($r['id'])>=$requisito[2]){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }
            }
        }

        
        $times_validos = times_tomados($r['id']);

        $time_asc = proximo_ascenso($r['id']);

        //$actual = new DateTime(date("Y-m-d H:i:s"));
        //$dif = $actual->diff(new DateTime($time_asc));

        $time_final = "Dias: ".$dif->d." Horas: ".$dif->h." Minutos".$dif->i;


        if(requiere_ascenso($r['id'])){

            $asc = "<span class='badge badge-success'>Si</span>";

        }else{

            $asc = "<span data-toggle='tooltip' title='".$time_asc."' class='badge badge-danger'>No</span>";

        }

        if($r['placa_paga'] == 1){
            $placa_paga = "<br><span id='paga".$r['id']."' onclick='quitar_placa_paga(".$r['id'].")' class='badge badge-danger' style='background: black; color: white; cursor:pointer;' data-toggle='tooltip' title='Quitar Placa Paga'>Placa Paga</span>";
        }else{
            $placa_paga = "<br><span  id='paga".$r['id']."' onclick='dar_placa_paga(".$r['id'].")' class='badge badge-secondary' style='cursor:pointer;' data-toggle='tooltip' title='Dar Placa Paga'>No Placa Paga</span>";
        }

        if($r['placa_boni'] == 1){
            $placa_boni = "<br><span id='boni".$r['id']."' onclick='quitar_placa_boni(".$r['id'].")' class='badge badge-warning' style='cursor:pointer;' data-toggle='tooltip' title='Quitar Placa Boni'>Placa Boni</span>";
        }else{
            $placa_boni = "<br><span id='boni".$r['id']."' onclick='dar_placa_boni(".$r['id'].")' class='badge badge-secondary' style='cursor:pointer;' data-toggle='tooltip' title='Dar Placa Boni'>No Placa Boni</span>";
        }

        $horas_trabajadas = horas_trabajadas_text_return($r['id']);


        array_push($temp, $r['id']);
        array_push($temp, '<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.$r['habbo'].'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$r['id'].'"/>');
        array_push($temp, $r['habbo']);
        array_push($temp, nombre_rol($r['rol']));
        array_push($temp, $ascensos);
        array_push($temp, $times_validos);
        array_push($temp, $horas_trabajadas);
        array_push($temp, $asc);
        array_push($temp, $save);
        array_push($temp, $status." ".$placa_paga." ".$placa_boni);

        if(is_seguridad_or_more(rol($r['id']))){
            $apago = $pago_seg;
        }
        
        if(is_tecnico_or_more(rol($r['id']))){
            $apago = $pago_tec;
        }
        
        if(is_logistica_or_more(rol($r['id']))){
            $apago = $pago_log;
        }
        
        if(is_supervisor_or_more(rol($r['id']))){
            $apago = $pago_sup;
        }
        
        if(is_director_or_more(rol($r['id']))){
            $apago = $pago_dir;
        }
        
        if(is_presidente_or_more(rol($r['id']))){
            $apago = $pago_pre;
        }
        
        if(is_elite_or_more(rol($r['id']))){
            $apago = $pago_eli;
        }
        
        if(is_junta_directiva_or_more(rol($r['id']))){
            $apago = $pago_jtd;
        }

        $status_pago = status_pago($r['id']);

        if($status_pago == 0){
            $total_pagar = 0;
        }elseif($status_pago == 1){
            $total_pagar = $apago[0];
        }elseif($status_pago == 2){
            $total_pagar = $apago[0] + $apago[1];
        }

        array_push($temp, $total_pagar);
        array_push($temp, ' <a target="_blank" href="?p=ver_detalles_habbo&id='.$r['id'].'" data-toggle="tooltip" title="Ver Detalles"><i class="fas fa-eye"></i></a>
        &nbsp;
    <a href="#" id="pagar'.$r['id'].'" onclick="pagar_creditos('.$r['id'].')" data-toggle="tooltip" title="Pagar"><i class="fa fa-check"></i></a>
        &nbsp;
    <a href="#" id="pagar'.$r['id'].'" onclick="cargar_ascenso('.$r['id'].')" data-toggle="modal" data-target="#pagar_ascenso" title="Pagar con Ascenso"><i class="fa fa-crown"></i></a>
        &nbsp;
    <a href="#" id="pagar'.$r['id'].'" onclick="no_pagar_user('.$r['id'].')" data-toggle="tooltip" title="Rechazar"><i class="fas fa-times-circle"></i></a>');

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
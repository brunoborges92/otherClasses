<?php
class SQLcontrole {

	public function __construct(){}
/*
Fun��o para listar uma SQL que � recebida por parametro e retorna uma Array
*/	
	public static function listar($SQL){
        try{
        	if( empty($SQL) ){ throw new Exception( "SQL n�o foi informado!" , 1 ); }               
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
        }  catch ( PDOException $e ){              
            echo 'N�o foi poss�vel listar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}
/*
*	Lista todas as informa��es da tabela pre definido o SQL
*/
	public static function listAll( $table ){
        try{
            if( empty($table) 		){ throw new Exception( "Nome da tabela n�o foi informado!" , 1	); }
			$SQL   = "SELECT * FROM ".$table;
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
        }  catch ( PDOException $e ){    
            echo 'N�o foi poss�vel listar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}
/* 
*	Gera as condi��es para SELECT, recebe uma array e monta a condi��o WHERE com AND
*/
	public function condicional( $where = array() ){
		$terms = null;
		if(!empty($where)){
	    	$terms = " WHERE ";
	    	foreach ($where as $value) { $terms .= $value . " AND ";}
	    	$terms = rtrim( $terms , " AND " );
	    }
	    return $terms;
	}
/*
*	Lista informa��es onde pode ser adicionado condi��es e campos pre definido o SQL
*/
	public static function find( $table , $where = array() , $fields = "*" ){
        try{
        	$terms = null;
            if( 	empty($table) 		){ throw new Exception( "Nome da tabela n�o foi informado!"					); }
            elseif( !is_array($where) 	){ throw new Exception( "Condi��es n�o foram informadas como ARRAY!"		); }
            elseif( empty($fields) 		){ throw new Exception( "Campos de sele��o n�o foi informado!"				); }
			$terms = self::condicional($where);
			$SQL   = "SELECT ".$fields." FROM ".$table." ".$terms;
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
        }  catch ( PDOException $e ){
            echo 'N�o foi poss�vel listar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}
/* 
*	Lista com configura��o de paramentros em forma de array, com parametros j� predefinidos
*/
	public static function findAll( $config = array() ){
	 	try {
	 		if( !is_array($config) 	){ throw new Exception( "Configura��es n�o foram informadas como ARRAY!"		); }
	 		elseif( empty($config) 	){ throw new Exception( "Configura��es n�o foram informadas!"					); }
	 		$default = array(

				'tabela'   => '',
				'condicao' => array(),
				'campo'    => '*'	,
				'ordena'   => ''	,
				'agrupa'   => ''	,
				'limite'   => ''

	 		);
			$instrucoes = array_merge( $default, $config );
			$table  	= $instrucoes[ 'tabela'		];
			$where  	= $instrucoes[ 'condicao'	];
			$fields 	= $instrucoes[ 'campo'		];
			$order  	= $instrucoes[ 'ordena'		];
			$groupby  	= $instrucoes[ 'agrupa'		];
			$limit  	= $instrucoes[ 'limite'		];
			if( 	empty($table) 		){ throw new Exception( "Nome da tabela n�o foi informado!"					); }
			elseif( !is_array($where) 	){ throw new Exception( "Condi��es n�o foram informadas como ARRAY!"		); }
			elseif( empty($fields) 		){ throw new Exception( "Campos de sele��o n�o foi informado!"				); }
			$terms   	= ( empty($where) 	) 	? null : self::condicional($where);
			$groupby 	= ( empty($groupby)	)	? null :  ' GROUP BY('.$groupby.') ';
			$order   	= ( empty($order)	)	? null :  ' ORDER BY '.$order.' ';
			$limit   	= ( empty($limit)	)	? null :  ' LIMIT '.$limit.' ';
			$SQL     	= "SELECT ".$fields." FROM " . $table . $terms .  $groupby . $order  . $limit;
			$stmt    	= BD::getConn()->prepare($SQL);
			$stmt->execute();     
			$total   	= $stmt->rowCount();
            if( $total > 0 ){  return $stmt; }else{ return false; }
	 		
	 	} catch (Exception $e) {
	 		 echo 'N�o foi poss�vel listar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Fun��o para inser dados no banco que recebe NOME DA TABELA, CAMPOS EM UMA ARRAY, VALORES EM UMA ARRAY,
* Retorna ultimo ID registrado
*/	
	public static function inserir( $tabela , $campos  , $valores ){
		try{
			if( 	empty($tabela)		){ throw new Exception( "Nome da tabela n�o foi informado!"					); }
			elseif( !is_array($campos)	){ throw new Exception( "Campos n�o foram informadas como ARRAY!"			); }
			elseif( empty($campos)		){ throw new Exception( "Campos n�o foram informado!"						); }
			elseif( !is_array($valores)	){ throw new Exception( "Valores n�o foram informadas como ARRAY!"			); }
			foreach($campos as $nome_campo){ $complemento .= $nome_campo . " = ? , "; }
			$complemento = rtrim( $complemento , " , " );
			$strSQL      = "INSERT INTO $tabela SET $complemento ";
			$stmt        = BD::getConn()->prepare($strSQL);
			$stmt->execute($valores);
			$total       = $stmt->rowCount();
	        if( $total > 0 ){
				$last_id = BD::getConn()->lastInsertId();
				return $last_id;
	        }else{  return false; }
	    }catch (Exception $e) {
	 		 echo 'N�o foi poss�vel inserir informa��es ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Fun��o para alterar dados no banco que recebe NOME DA TABELA, CAMPOS EM UMA ARRAY, VALORES EM UMA ARRAY,
* CONDICIONAL EM STRING
*/
	public static function alterar( $tabela , $campos  , $valores , $condicao ){
		try{
			if( 	empty($tabela)			){ throw new Exception( "Nome da tabela n�o foi informado!"					); }
			elseif( !is_array($campos)		){ throw new Exception( "Campos n�o foram informadas como ARRAY!"			); }
			elseif( empty($campos)			){ throw new Exception( "Campos n�o foram informado!"						); }
			elseif( !is_array($valores)		){ throw new Exception( "Valores n�o foram informadas como ARRAY!"			); }
			elseif( !is_array($condicao)	){ throw new Exception( "Condi��o n�o foram informadas como ARRAY!"			); }
			elseif( empty($condicao)		){ throw new Exception( "Condi��o n�o foi informada!"						); }
			foreach($campos as $nome_campo){ $complemento .= $nome_campo . " = ? , "; }
			$complemento = rtrim( $complemento , " , " );
			$terms       = self::condicional($condicao);
			$strSQL      = "UPDATE $tabela SET $complemento $terms";
			$stmt        = BD::getConn()->prepare($strSQL);
			$stmt->execute($valores);
			$total       = $stmt->rowCount();
			if( $total > 0 ){  return $stmt; }else{ return false; }
	    }catch (Exception $e) {
 		 	echo 'N�o foi poss�vel alterar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Fun��o para excluir uma tabela que � recebida por parametro e condicional em forma de STRING
*/	
	public static function excluir( $tabela ,  $condicao ){
		try{
			if( 	empty($tabela)			){ throw new Exception( "Nome da tabela n�o foi informado!"				); }
			elseif( empty($condicao)		){ throw new Exception( "Condi��o n�o foi informada!"					); }
			elseif( !is_array($condicao)	){ throw new Exception( "Condi��o n�o foram informadas como ARRAY!"		); }
			$terms	= self::condicional($condicao);
			$strSQL = "DELETE FROM  $tabela $terms";
			$stmt   = BD::getConn()->query($strSQL);
	        $total  = $stmt->rowCount();
	        if( $total > 0 ){  return true; }else{ return false; }
	    }catch (Exception $e) {
 		 	echo 'N�o foi poss�vel excluir informa��es ERRO: <br />' . nl2br( $e->getMessage() );
	 	}	        
	}
/*
* Fun��o que retorna o total de linhas de uma SQL que � recebido por parametro
*/
	public static function total ($SQL){
		try{
			if( empty($SQL) ){ throw new Exception( "SQL n�o foi informado!"  ); }
			$stmt  	= BD::getConn()->prepare($SQL);
			$stmt->execute();
			return	$stmt->rowCount();
		}catch (Exception $e) {
 		 	echo 'N�o foi poss�vel totalizar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
	 	}
	}
/*
* Fun��o que retorna o total de linhas de uma SQL que � recebido por parametro
*/
	public static function totalRow ( $table , $where = array() , $fields = "id" ){
		try{
        	$terms = null;
            if( 	empty($table) 		){ throw new Exception( "Nome da tabela n�o foi informado!"					); }
            elseif( !is_array($where) 	){ throw new Exception( "Condi��es n�o foram informadas como ARRAY!"		); }
            elseif( empty($fields) 		){ throw new Exception( "Campos de sele��o n�o foi informado!"				); }
			$terms = self::condicional($where);
			$SQL   = "SELECT ".$fields." FROM ".$table." ".$terms;
			$stmt  = BD::getConn()->prepare($SQL);
			$stmt->execute();
			$total = $stmt->rowCount();
			return $total;
        }  catch ( PDOException $e ){
            echo 'N�o foi poss�vel listar informa��es ERRO: <br />' . nl2br( $e->getMessage() );
        }
	}

        
        
        
        
        
}
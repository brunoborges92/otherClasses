<?php
/**
* CLasse que vai montar uma pesquisa de informa��es com pagina��o
*/
class Loadinfo
{
   

    /* SQL inicial que pode ser adicionado condi��o pelo metodo getParamentros */
    private $SQL                = "SELECT * FROM intrvirt_db.tarefa"; 
    private $total;
    /* total de itens mostrados antes de acionar pagina��o */
    private $total_mostrar      = 8; 
    /* links 1 2 3 4 quantidade */
    private $num_link           = 5; 
    private $pagina; 
    private $paginaAtual; 
    private $inicio;   
    private $LINK_DESPUBLICADO;            
    private $LINK;              
             

    function __construct()
    {
        $this->pagina               = Url::getURLpag();
        $this->paginaAtual          = ( $this->pagina > 0 ) ? (int)$this->pagina : 1;
        $this->inicio               = ( $this->total_mostrar * $this->paginaAtual ) - $this->total_mostrar;
        $this->LINK_DESPUBLICADO    = ( defined('PUBLICADO') AND PUBLICADO == false ) ? "site_projeto/" : "";
        $this->LINK                 = DOMINIO . DS . $this->LINK_DESPUBLICADO;
        $this->total                = SQLcontrole::total( $this->SQL );
    }


    /* fun��o que adiciona parametros na busca */
    private function getParamentros( $parm = null )
    {
        return ( !empty( $parm ) ) ? $this->SQL .= " ". $parm : $this->SQL;
    }


    public function Listar()
    {

        $SQLstrT        = $this->getParamentros( " LIMIT ". $this->inicio ." , ".$this->total_mostrar );
        $consulta       = SQLcontrole::listar( $SQLstrT );                 
        $fetchAll       = $consulta->fetchAll(PDO::FETCH_OBJ);
        /* Variavel que vai conter todo o conteudo que ir� retornar */
        $result    = null;

        if( $fetchAll ){

            foreach ( $fetchAll as $value ) {
                $result .= "ID - " . $value->id . "<br>";
            }

        }else{

            $result .= '<p align="center" >N�o foram encontradas informa��es cadastradas!</p>';

        }


        return $result;
    }


    public function geraPaginacao()
    {

        $paginacao = new Paginacao( $this->paginaAtual, $this->total , $this->total_mostrar , $this->num_link );
        /* Transforma array recebida em URL SEO array( valor , valor ) */
        $paginacao->setParametros(array( 'teste' ));
        /* Transforma array recebida em URL SEO array( valor , valor ) */
        # $paginacao->setParametrosGET(array( 'busca' ,  $_POST['busca'] ));
        /* informa a url ou pagina que ter� a paginacao */
        $paginacao->setUrl( $this->LINK ); //seta o nome da p�gina ou nome do arquivo
        return $paginacao->gerarPaginacao();
    }



}
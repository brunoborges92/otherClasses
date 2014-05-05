<?php
/**
 * Classe de integra��o que importa as tags e o embed de um v�deo do Youtube
 *
 * @author Rafael Wendel Pinheiro <rafaelwendel@hotmail.com> <www.rafaelwendel.com>
 * @version 1.0
 */
 

/**** USANDO  A CLASSE
$you = new Youtube ("http://www.youtube.com/watch?v=dx0yreHVju4");
echo $you->get_embed_video(1000,500);
*****/


class Youtube {
 
     /**
     * URL do v�deo no Youtube
     * @access private
     * @var String
     */
    private $link;
 
    /**
     * Armazena as tags referentes ao v�deo
     * @access private
     * @var array
     */
    private $tags = array();
 
    /**
     * Armazena informa��es referentes � erros
     * @access private
     * @var array
     */
    private $erro = array();
 
 
    /**
     * M�todo construtor da classe Youtube.
     * @access public
     * @param String $link URL do v�deo no Youtube (OBS: N�o utilize URL encurtada)
     * @return void
     */
    public function __construct($link = '') {
        if($link != ''){
            $this->setLink($link);
            $this->loadTags();
        }
    }
 
    /**
     * Pegar a URL do v�deo que est� sendo utilizado
     * @access public
     * @return String a URL do v�deo
     */
    public function getLink() {
        return $this->link;
    }
 
    /**
     * Definir a URL do v�deo no Youtube
     * @access public
     * @param String $link a URL do v�deo
     * @return mixed false em caso de URL inv�lida
     */
    public function setLink($link) {
        if(!strstr($link, "youtube.com")){
            $this->setErro('URL inv�lida');
            return false;
        }
        $this->link = $link;
    }
 
    /**
     * Capturar o(s) erro(s)
     * @access public
     * @return Array
     */
    public function getErro() {
        return $this->erro;
    }
 
 
    /**
     * Definir uma mensagem de erro
     * @access public
     * @param String $erro
     * @return void
     */
    public function setErro($erro) {
        $this->erro[] = $erro;
    }
 
    /**
     * Armazena as tags importadas na vari�vel $tags
     * @access private
     * @param Array $tags
     * @return void
     */
    private function setTags($tags){
        if(is_array($tags)){
            foreach($tags as $prop => $value){
               $this->tags[$prop] = $value;
            }
            $this->repairTags();
        }
    }
 
    /**
     * Capturar as tags do v�deo
     * @access public
     * @return mixed Arrays com tags ou false em caso de array vazio
     */
    public function getTags() {
        if(count($this->tags) > 0){
            return $this->tags;
        }
        else{
            return false;
        }
    }
 
    /**
     * Chamar a execu��o da URL e setar as tags em caso de sucesso
     * @access private
     * @return mixed False em casso de erros
     */
    private function loadTags() {
        if(empty ($this->link)){
            $this->setErros('URL inv�lida');
            return false;
        }
        $propertys = $this->get_propertys_tags();
        if(in_array('noindex', $propertys)){
            $this->setErro('URL inv�lida');
            return false;
        }
        $this->setTags($propertys);
    }
 
    /**
     * Repara os nomes dos indices do array $tags
     * @access private
     * @return void
     */
    private function repairTags(){
        if(count($this->tags) > 0){
            $tags = $this->tags;
            foreach ($tags as $prop => $value){
                $nome = explode(':', $prop);
                $nome = $nome[count($nome) - 1];
 
                $new[$nome] = $value;
            }
            $this->tags = $new;
        }
    }
 
    /**
     * Pega o conte�do do link do Youtube via CURL
     * @access private
     * @return String $data Conte�do lido
     */
    private function file_get_contents_curl(){
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->link);
 
        $data = curl_exec($ch);
        curl_close($ch);
 
        return $data;
    }
 
    /**
     * L� o conte�do recuperado e extrai as tags do v�deo
     * @access private
     * @return Array As Tags do v�deo
     */
    private function get_propertys_tags(){
        $html = $this->file_get_contents_curl();
 
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
 
        $metas = $doc->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++){
            $meta = $metas->item($i);
            $prop_tags[$meta->getAttribute('property')] = $meta->getAttribute('content');
        }
        return $prop_tags;
    }
 
    /**
     * Retorna o c�digo de incorpora��o do video
     * @param int $width largura do embed
     * @param int $height altura do embed
     * @return String C�digo embed do v�deo
     */
    public function get_embed_video($width = 396, $height = 297){
        $cod_video = explode('watch?v=', $this->link);
        $cod_video = $cod_video[1];
        return "<iframe width=\"$width\" height=\"$height\" src=\"http://www.youtube.com/embed/$cod_video\" frameborder=\"0\" allowfullscreen></iframe>";
    }
}

?>


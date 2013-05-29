<?php
/**
 * Ftp
 *
 * Cliente FTP
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Ftp
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Ftp
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Ftp
{
    /**
     * Host de conexão
     *
     * @var string
     */
    private $_host;

    /**
     * Porta de conexão
     *
     * @var integer
     */
    private $_porta = 21;

    /**
     * Login de conexão
     *
     * @var string
     */
    private $_usr;

    /**
     * Senha de usuário
     *
     * @var string
     */
    private $_pwd;

    /**
     * Resource da conexão
     *
     * @var resource
     */
    private $_res;

    /**
     * Se a conexão é passiva ou não
     *
     * @var boolean
     */
    private $_passivo = true;

    /**
     * Status da conexão
     *
     * @var boolean
     */
    private $_status = false;

    /**
     * Tempo máximo da conexão
     *
     * @var integer
     */
    private $_timeout = 30;

    /**
     * Configura o host de conexão
     *
     * @access  public
     * @param  string $strHost
     * @return void
     */
    public function setHost($strHost)
    {
        $this->_host = $strHost;
    }

    /**
     * Configura a porta de conexão
     *
     * @access  public
     * @param  integer $intPorta
     * @return void
     */
    public function setPorta($intPorta)
    {
        $this->_porta = $intPorta;
    }

    /**
     * Configura o usuaŕio
     *
     * @access  public
     * @param  string $strUsuario
     * @return void
     */
    public function setUsuario($strUsuario)
    {
        $this->_usr = $strUsuario;
    }

    /**
     * Seta a senha
     *
     * @access  public
     * @param  string $strSenha
     * @return void
     */
    public function setSenha($strSenha)
    {
        $this->_pwd = $strSenha;
    }

    /**
     * Configura o timeout (tempo máximo) da conexão
     *
     * @access  public
     * @param  integer $intTimeout
     * @return void
     */
    public function setTimeout($intTimeout)
    {
        $this->_timeout = $intTimeout;
    }

    /**
     * Abre a conexão com o servidor de ftp
     *
     * @access public
     * @return resourceId
     */
    public function connect()
    {
        $this->_res = ftp_connect($this->_host, $this->_porta, $this->_timeout);

        $this->_status = ftp_login($this->_res, $this->_usr, $this->_pwd);

        ftp_pasv($this->_res, $this->_passivo);
    }

    /**
     * Encerra a conexão
     *
     * @access public
     * @return boolean
     */
    public function disconnect()
    {
        return ftp_close($this->_res);
    }

    /**
     * Baixa um arquivo
     *
     * @access  public
     * @param  string  $strRemoto
     * @param  string  $strLocal
     * @param  integer $intModo   FTP_BINARY or FTP_ASCII
     * @return boolean
     */
    public function get($strRemoto, $strLocal, $intModo=FTP_BINARY)
    {
        return ftp_get($this->_res, $strLocal, $strRemoto, $intModo);
    }

    /**
     * Envia um arquivo local para ao servidor de ftp
     *
     * @access  public
     * @param  string  $strRemoto
     * @param  string  $strLocal
     * @param  integer $intModo   FTP_BINARY or FTP_ASCII
     * @return boolean
     */
    public function send($strRemoto, $strLocal, $intModo=FTP_BINARY)
    {
        return ftp_put($this->_res, $strRemoto, $strLocal, $intModo);
    }

    /**
     * Remove um arquivo
     *
     * @access  public
     * @param  string $strFilename
     * @return void
     */
    public function delete($strFilename = null)
    {
        return ftp_delete($this->_res, $strFilename);
    }

    /**
     * Retorna o diretório atual do servidor de ftp
     *
     * @access public
     * @return string
     */
    public function pwd()
    {
        return ftp_pwd($this->_res);
    }

    /**
     * Lista os arquivos dentro do diretório
     *
     * @access public
     * @param  string $strDir
     * @return array
     */
    public function ls($strDir = null)
    {
        return ftp_nlist($this->_res, $strDir);
    }

    /**
     * Lista os arquivos dentro de forma detalhada
     *
     * @access public
     * @param  string $strDir
     * @return array
     */
    public function lsDetail($strDir = null)
    {
        return ftp_rawlist($this->_res, $strDir);
    }
}

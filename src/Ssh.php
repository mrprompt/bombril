<?php
/**
 * Ssh
 *
 * Cliente SSH
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Ssh
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 **/

/**
 * @category   Classes
 * @subpackage Ssh
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Ssh
{
    /**
     * Host de conexão
     *
     * @static
     * @var string
     */
    private static $_host;

    /**
     * Porta de conexão
     *
     * @static
     * @var integer
     */
    private static $_porta = 22;

    /**
     * Login de Usuário
     *
     * @static
     * @var string
     */
    private static $_usuario;

    /**
     * Senha de usuário
     *
     * @static
     * @var string
     */
    private static $_senha;

    /**
     * Resource da conexão
     *
     * @static
     * @var resource
     */
    private static $_res;

    /**
     * Configura o host de conexão
     *
     * @access  public
     * @param  string $_host
     * @return void
     */
    public static function setHost($_host = null)
    {
        if ($_host !== null) {
            self::$_host = $_host;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna o host setado
     *
     * @static
     * @access  public
     * @return string
     */
    public static function getHost()
    {
        return self::$_host;
    }

    /**
     * Configura a porta de conexão
     *
     * @access  public
     * @param  integer $_porta
     * @return void
     */
    public static function setPorta($_porta = null)
    {
        if ($_porta !== null && is_int($_porta)) {
            self::$_porta = $_porta;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna a porta da conexão
     *
     * @static
     * @access  public
     * @return integer
     */
    public static function getPorta()
    {
        return self::$_porta;
    }

    /**
     * Seta o usuário para conexão
     *
     * @static
     * @access  public
     * @param  string $_usuario
     * @return void
     */
    public static function setUsuario($_usuario = null)
    {
        if ($_usuario !== null && is_string($_usuario)) {
            self::$_usuario = $_usuario;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna o usuário da conexão
     *
     * @static
     * @access  public
     * @return string
     */
    public static function getUsuario()
    {
        return self::$_usuario;
    }

    /**
     * Seta a senha do usuário para conexão
     *
     * @access  public
     * @param  string $_senha
     * @return void
     */
    public static function setSenha($_senha)
    {
        if ($_senha !== null && is_string($_senha)) {
            self::$_senha = $_senha;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna a senha utilizada na conexão
     *
     * @static
     * @access  public
     * @return string
     */
    public static function getSenha()
    {
        return self::$_senha;
    }

    /**
     * Abre a conexão com o servidor de ftp
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function connect()
    {
        self::$_res = ssh2_connect(self::getHost(), self::getPorta());

        ssh2_auth_password(self::$_res, self::getUsuario(), self::getSenha());

        return ssh2_sftp(self::$_res);
    }

    /**
     * Salva o arquivo informado pelo usuário no diretório destino
     * para o diretório local da máquina
     *
     * @static
     * @access  public
     * @param  string  $strArquivo
     * @param  string  $strDestino
     * @return boolean
     */
    public static function get($strArquivo = null, $strDestino = null)
    {
        return ssh2_scp_recv(
            self::$_res,
            $strArquivo,
            $strDestino
        );
    }

    /**
     * Envia um arquivo local para ao servidor de ftp
     *
     * @static
     * @access  public
     * @param  string  $strArquivo
     * @param  string  $strDestino
     * @return boolean
     */
    public static function send($strArquivo = null, $strDestino = null)
    {
        return ssh2_scp_send(
            self::$_res,
            $strArquivo,
            $strDestino
        );
    }

    /**
     * Retorna
     *
     * @static
     * @access  public
     * @param string $strArquivo
     * @param   string [$strServerPath]
     * @return void
     */
    public static function delete($strArquivo = null)
    {
        return ssh2_sftp_unlink(
            self::$_res,
            $strArquivo
        );
    }

    /**
     * Executa um comando remoto
     *
     * @static
     * @access public
     * @param  string $strComando
     * @return mixed
     */
    public static function execute($strComando = null)
    {
        return ssh2_exec(
            self::$_res,
            $strComando
        );
    }

    /**
     * Cria um diretório
     *
     * @static
     * @access  public
     * @param  string  $strDir
     * @param  integer $intPermissao
     * @param  boolean $booRecursive
     * @return boolean
     */
    public static function mkdir($strDir = null, $intPermissao = '0777', $booRecursive = false)
    {
        return ssh2_mkdir(
            self::$_res,
            $strDir,
            $intPermissao,
            $booRecursive
        );
    }
}

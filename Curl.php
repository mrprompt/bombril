<?php
 /**
 * Curl
 *
 * Cliente HTTP baseado em cURL
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Curl
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Curl
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Curl
{
    /**
     * O objeto da conexão utilizando cURL
     *
     * @static
     * @access private
     * @var    object
     */
    private static $_objCurl;

    /**
     * Inicia a conexão
     *
     * @static
     * @access public
     * @return void
     */
    public static function connect()
    {
        // Iniciando o objecto Curl
        self::$_objCurl = curl_init();

        // Retornar a transferência ao objeto
        self::setOption(CURLOPT_RETURNTRANSFER, 1);

        // Sempre utilizar uma nova conexão
        self::setOption(CURLOPT_FRESH_CONNECT, 1);

        // Retornar Header
        self::setOption(CURLOPT_HEADER, false);

        // Modo verboso
        self::setOption(CURLOPT_VERBOSE, 0);

        // Mostrar o corpo da requisição
        self::setOption(CURLOPT_NOBODY, 0);

        // Seguir redirecionamentos
        self::setOption(CURLOPT_FOLLOWLOCATION, 1);
    }

    /**
     * Fecha a conexão
     *
     * @static
     * @access public
     * @return void
     */
    public static function close()
    {
        curl_close(self::$_objCurl);
    }

    /**
     * Executa uma chamada
     *
     * @static
     * @access public
     * @return mixed
     */
    public static function execute()
    {
        $strTemp = curl_exec(self::$_objCurl);

        self::close();

        return $strTemp;
    }

    /**
     * Busca informações
     *
     * @static
     * @access public
     * @return mixed
     */
    public static function info()
    {
        return curl_getinfo(self::$_objCurl);
    }

    /**
     * Retorna o erro do cURL
     *
     * @static
     * @access public
     * @return mixed
     */
    public static function error()
    {
        return curl_error(self::$_objCurl);
    }

    /**
     * Configura uma opção do cURL
     *
     * @static
     * @access public
     * @param string $option
     * @param mixed  $value
     */
    public static function setOption($option, $value)
    {
        curl_setopt(self::$_objCurl, $option, $value);
    }

    /**
     * Seta o timeout
     *
     * @static
     * @access public
     * @param integer $seconds
     */
    public static function setTimeout($seconds)
    {
        self::setOption(CURLOPT_TIMEOUT, $seconds);
    }

    /**
     * Faz uma requisição HTTP pelo método GET
     *
     * @static
     * @access  public
     * @param  string  $strUrl
     * @param  boolean $booAutenticar
     * @param  string  $strUsuario
     * @param  string  $strSenha
     * @return string
     */
    public static function get()
    {
        // buscando argumentos
        $arrArgs = func_get_args();

        if (is_array($arrArgs[0])) {
            $arrArgs = $arrArgs[0];
            $strUrl         = isset($arrArgs[0]) ? $arrArgs[0] : null;
            $booAutenticar  = isset($arrArgs[1]) ? $arrArgs[1] : false;
            $strUsuario     = isset($arrArgs[2]) ? $arrArgs[2] : null;
            $strSenha       = isset($arrArgs[3]) ? $arrArgs[3] : null;
        } else {
            $strUrl         = isset($arrArgs[0]) ? $arrArgs[0] : null;
            $booAutenticar  = isset($arrArgs[1]) ? $arrArgs[1] : false;
            $strUsuario     = isset($arrArgs[2]) ? $arrArgs[2] : null;
            $strSenha       = isset($arrArgs[3]) ? $arrArgs[3] : null;
        }

        if ($strUrl !== null) {
            self::connect();
            self::setOption(CURLOPT_URL, $strUrl);

            // Somente se a autenticação for necessária
            if ($booAutenticar === true &&
                $strUsuario !== null &&
                $strSenha !== null) {
                // Habilitando o método POST
                self::setOption(CURLOPT_POST, true);

                // Enviar os campos como POST
                self::setOption(CURLOPT_POSTFIELDS, 1);

                // Enviando usuário e senha
                self::setOption(CURLOPT_USERPWD, $strUsuario . ':' . $strSenha);
            }

            return self::execute();
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Faz uma requisição HTTP pelo método POST
     *
     * @static
     * @access  public
     * @param  string $strUrl
     * @return mixed
     */
    public static function post()
    {
        // buscando argumentos
        $arrArgs = func_get_args();

        if (is_array($arrArgs[0])) {
            $arrArgs = $arrArgs[0];
            $strUrl         = isset($arrArgs[0]) ? $arrArgs[0] : null;
            $strConteudo    = isset($arrArgs[1]) ? $arrArgs[1] : null;
            $booAutenticar  = isset($arrArgs[2]) ? $arrArgs[2] : false;
            $strUsuario     = isset($arrArgs[3]) ? $arrArgs[3] : null;
            $strSenha       = isset($arrArgs[4]) ? $arrArgs[4] : null;
        } else {
            $strUrl         = isset($arrArgs[0]) ? $arrArgs[0] : null;
            $strConteudo    = isset($arrArgs[1]) ? $arrArgs[1] : null;
            $booAutenticar  = isset($arrArgs[2]) ? $arrArgs[2] : false;
            $strUsuario     = isset($arrArgs[3]) ? $arrArgs[3] : null;
            $strSenha       = isset($arrArgs[4]) ? $arrArgs[4] : null;
        }

        if ($strUrl !== null) {
            // Conectando
            self::connect();

            // Abrindo a url
            self::setOption(CURLOPT_URL, $strUrl);

            // Habilitando o método POST
            self::setOption(CURLOPT_POST, true);

            // Somente se a autenticação for necessária
            if ($booAutenticar === true && $strUsuario !== null &&
                $strSenha !== null) {

                // Autenticando
                self::setOption(CURLOPT_USERPWD, $strUsuario . ':' . $strSenha);
            }

            self::setOption(CURLOPT_POSTFIELDS, $strConteudo);

            return self::execute();
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

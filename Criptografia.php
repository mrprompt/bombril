<?php
/**
 * Criptografia
 *
 * Classe para criptografia de strings
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Criptografia
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Criptografia
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Criptografia
{
    /**
     * Chave de encriptação
     *
     * @static
     * @access  private
     * @var     string
     */
    private static $_chave  = 'default';

    /**
     * Interno, inicialização dos modos CBC, CFB, OFB
     *
     * @static
     * @access  private
     * @var     integer
     */
    private static $_strCryptIv;

    /**
     * Configura a chave de encriptação
     *
     * @static
     * @access  public
     * @param  string $chave Chave de encriptação
     * @return void
     */
    public static function setChave($chave = null)
    {
        if ($chave === null) {
            throw new Exception('Chave inválida.');
        }

        self::$_chave = $chave;
    }

    /**
     * Retorna a chave atual utilizada
     *
     * @static
     * @access  public
     * @return string
     */
    public static function getChave()
    {
        return self::$_chave;
    }

    /**
     * Cria o IV de conexão
     *
     * @static
     * @access private
     * @return void
     */
    private static function createIv()
    {
        $intIvSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);

        self::$_strCryptIv = mcrypt_create_iv($intIvSize, MCRYPT_DEV_URANDOM);
    }

    /**
     * Encripa o valor do cookie
     *
     * @static
     * @access  public
     * @param  string $strValor
     * @param  string $chave
     * @return string
     */
    public static function encode($strValor = null, $chave = null)
    {
        if ($strValor !== null) {
            if ($chave !== null) {
                self::setChave($chave);
            }

            // criando o modo IV
            self::createIv();

            $iv    = self::$_strCryptIv;
            $chave = self::getChave();
            $size  = MCRYPT_RIJNDAEL_256;
            $modo  = MCRYPT_MODE_ECB;

            $strTemp = mcrypt_encrypt($size, $chave, $strValor, $modo, $iv);

            return base64_encode($strTemp);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna o valor decriptado
     *
     * @static
     * @access  public
     * @param  string $strValor
     * @param  string $chave
     * @return string
     */
    public static function decode($strValor = null, $chave = null)
    {
        if ($strValor !== null) {
            self::setChave($chave);
            $chave = self::getChave();

            self::createIv();
            $iv = self::$_strCryptIv;

            $valor = base64_decode($strValor);
            $size = MCRYPT_RIJNDAEL_256;
            $modo = MCRYPT_MODE_ECB;

            $strSaida = mcrypt_decrypt($size, $chave, $valor, $modo, $iv);

            return rtrim($strSaida, "\0");
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

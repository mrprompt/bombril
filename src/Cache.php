<?php
/**
 * Cache
 *
 * Cache em memória utilizando a engine do MemCache
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Cache
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Cache
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Cache
{
    /**
     * Objeto da instância do Memcache
     *
     * @var object
     */
    private static $_obj;

    /**
     * Host de conexão
     *
     * @var string
     */
    private static $_host  = 'localhost';

    /**
     * Porta de conexão
     *
     * @var integer
     */
    private static $_porta = 11211;

    /**
     * Singleton para pegar a conexão do servidor cache
     *
     * @static
     * @access  public
     * @return objDb
     */
    public static function getConnection ()
    {
        if (!self::$_obj instanceof Memcache) {
            // iniciando o memcache
            self::$_obj = new Memcache();

            // conectando ao servidor
            self::$_obj->pconnect(self::$_host, self::$_porta);
        }

        return self::$_obj;
    }

    /**
     * Retorna uma série de informações sobre o servidor
     * de Cache
     *
     * @static
     * @access  public
     * @return array
     */
    public static function getStats()
    {
        return self::$_obj->getExtendedStats();
    }

    /**
     * Configura o host de conexão
     *
     * @static
     * @access  public
     * @param  string $_host
     * @return void
     */
    public static function setHost($_host = 'localhost')
    {
        self::$_host = $_host;
    }

    /**
     * Retorna o host de conexão
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
     * Set a porta de conexão
     *
     * @static
     * @access  public
     * @param  integer $_porta
     * @return void
     */
    public static function setPort($_porta = 11211)
    {
        self::$_porta = $_porta;
    }

    /**
     * Retorna a porta de conexão do Host
     *
     * @static
     * @access public
     * @return integer
     */
    public static function getPort()
    {
        return self::$_porta;
    }
}

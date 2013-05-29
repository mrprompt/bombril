<?php
/**
 * Session
 *
 * Sessão em memória utilizando a engine do MemCache
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Session
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 **/

/**
 * @see Cache
 */
require_once dirname(__FILE__) . '/Cache.class.php';

/**
 * @category   Classes
 * @subpackage Session
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Session
{
    /**
     * Nome da sessão
     *
     * @static
     * @var string
     */
    private static $_nome;

    /**
     * Se á foi iniciado ou não
     *
     * @static
     * @var boolean
     */
    private static $_started = false;

    /**
     * Construtor
     *
     * Inicia a conexão com o servidor de Cache em memória
     *
     * @access public
     * @param string $strPath O caminho padrão. Ex.: '/'
     * @param string $strNome O nome para sessão
     */
    public function __construct($strPath = '/', $strNome = 'PHPSID')
    {
        self::$_nome = preg_replace('/([^[:alnum:]])/', '', $strNome);

        $host = Cache::getHost();
        $port = Cache::getPort();

        $sessionSavePath = "tcp://$host:$port?persistent=1&weight=2";
        $sessionSavePath.= "&timeout=2&retry_interval=10";

        // setando o php.ini
        ini_set("realpath_cache_ttl", "600");
        ini_set("session.name", self::$_nome);
        ini_set("session.cache_limiter", "nocache");
        ini_set("session.cache_expire", "480");
        ini_set("session.gc_maxlifetime", "28801");
        ini_set("session.bug_compat_42", "0");
        ini_set("session.hash_function", "1");
        ini_set("session.use_trans_sid", "0");
        ini_set("session.hash_bits_per_character", "6");
        ini_set('session.save_handler', 'memcache');
        ini_set('session.save_path', $sessionSavePath);
    }

    /**
     * Inicia a sessão efetivamente
     *
     */
    public static function start()
    {
        if (!self::$_started) {
            session_start();

            self::$_started = true;
        }
    }

    /**
     * Seta uma variável de sessão
     *
     * @static
     * @access  public
     * @param string $strKey     A chave de acesso a variável
     * @param string $strContent O conteúdo da variável
     */
    public static function set($strKey = null, $strContent = null)
    {
        if ($strKey !== null && $strContent !== null) {
            $_SESSION[ $strKey ] = $strContent;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna o valor de uma variável de sessão
     *
     * @static
     * @access  public
     * @param  string $strKey O nome da variável
     * @return string
     */
    public static function get($strKey = null)
    {
        if (isset($_SESSION[ $strKey ])) {
            return $_SESSION[ $strKey ];
        } else {
            return null;
        }
    }

    /**
     * Apaga uma variável da sessão
     *
     * @static
     * @access  public
     * @param string $strKey O nome da variável a ser apagada
     */
    public static function delete($strKey = null)
    {
        unset($_SESSION[ $strKey ]);
    }

    /**
     * Encerra a sessão atual
     *
     * @static
     * @access public
     * @return void
     */
    public static function close()
    {
        session_destroy();
    }

    /**
     * get a Session ID normal string
     *
     * @static
     * @access  public
     * @return string
     */
    public static function getSid()
    {
        $suid = session_id();
        $sid  = ereg_replace("[^a-zA-Z0-9]", "", $suid);

        return $sid;
    }
}

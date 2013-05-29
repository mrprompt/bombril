<?php
/**
 * Config
 *
 * Lê as configurações do arquivo .ini
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Config
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Config
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Config
{
    /**
     * Lê o arquivo INI e retorna uma array com seus valores
     *
     * @static
     * @access  public
     * @param  string $filConfig O arquivo INI
     * @return array
     */
    public static function getConfig ($filConfig = 'conf/config.ini')
    {
        if (file_exists($filConfig)) {
            return parse_ini_file($filConfig, true);
        } else {
            throw new Exception('Arquivo de configuração não encontrado.');
        }
    }
}

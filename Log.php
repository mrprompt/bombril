<?php
/**
 * Log
 *
 * Insere um texto em um arquivo de log
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Log
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Log
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Log
{
    /**
     * Loga o erro ocorrido e redireciona para a tela de erro
     * setada no arquivo XML de configuração da pasta conf
     *
     * @static
     * @access  public
     * @param  string  $strArquivo
     * @param  string  $strLog
     * @return boolean
     */
    public static function insert ($strArquivo = null, $strLog = null)
    {
        $objDate = new DateTime();

        $filLog = str_replace('+Y-m-d-H+', $objDate->format('Y-m-d-H'), $strArquivo);
        $strLog = $objDate->format('Y-m-d H:i:s') . ' - ' . strip_tags($strLog) . "\n";

        return file_put_contents($filLog, $strLog, FILE_APPEND);
    }
}

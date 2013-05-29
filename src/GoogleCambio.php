<?php
/**
 * GoogleCambio
 *
 * Busca a cotação do dia via página do Google
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage GoogleCambio
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see Curl
 */
require_once 'Curl.php';

/**
 * @category   Classes
 * @subpackage GoogleCambio
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class GoogleCambio
{
    /**
     * Busca no Google o valor de câmbio da moeda em questão
     *
     * @static
     * @access  private
     * @param  string $strCotacao A moeda a ser cotada (Ex.: usd, euro)
     * @return float
     */
    private static function getCotacao($strCotacao = 'usd')
    {
        // Url padrão
        $strUrl= "http://www.google.com/search?q={$strCotacao}+in+brl";

        // Buscando o retorno, sem html
        $strRetorno = strip_tags(Curl::get($strUrl));

        // ER de filtro
        $strEr      = '/(\=)([[:space:]])([[:digit:]]+\.[[:digit:]]+)/e';

        // Buscando a cotação
        $arrRetorno = null;

        preg_match($strEr, $strRetorno, $arrRetorno);

        // Retorno
        $strRetorno = null;

        if (isset($arrRetorno[0])) {
            // Pegando somente o que interessa
            $strRetorno = str_replace('= ', '', $arrRetorno[0]);

            // Formatando
            $strRetorno = number_format($strRetorno, 2, ',', '.');
        }

        return $strRetorno;
    }

    /**
     * Retorna o valor do Dólar americano
     *
     * @static
     * @access public
     * @return float
     */
    public static function getValorDolar()
    {
        return self::getCotacao('usd');
    }

    /**
     * Retorna o valor do Euro
     *
     * @static
     * @access public
     * @return float
     */
    public static function getValorEuro()
    {
        return self::getCotacao('euro');
    }
}

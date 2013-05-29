<?php
/**
 * Bookmarks
 *
 * Lê o arquivo de favoritos exportado pelo Firefox/Chrome e retorna
 * uma array ordenada com os links
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Bookmarks
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see     String
 */
require_once dirname(__FILE__) . '/String.php';

/**
 * @category   Classes
 * @subpackage Bookmarks
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Bookmarks
{
    /**
     * Lê o arquivo de favoritos
     *
     * @static
     * @access public
     * @param  string $strArquivo
     * @return mixed
     */
    public static function read($strArquivo = null)
    {
        if ($strArquivo !== null && file_exists($strArquivo)) {
            $arrLinks   = array();
            $strLinks   = file_get_contents($strArquivo);

            // removendo muitas quebras de linha
            $strLinks   = preg_replace('/(\n|\r\n|\r)/', '', $strLinks);
            $strEr = '{<A[^>]*>(.*?)</A>}i';

            preg_match_all($strEr, $strLinks, $arrLinks, PREG_PATTERN_ORDER);

            return $arrLinks;
        } else {
            throw new Exception('Favoritos não encontrado.');
        }
    }

    /**
     * Busca todos os links na lista de Favoritos e retorna uma array
     * associativa ordenada por nome
     *
     * @static
     * @access private
     * @param  string  $strArquivo
     * @param  integer $intLimite
     * @return mixed
     */
    public static function getAll($strArquivo = null, $intLimite = 0)
    {
        $arrLinks   = self::read($strArquivo);
        $arrRetorno = array();
        $intIndice  = 0;
        $arrTemp    = array();

        // Limitando, caso necessário
        if ($intLimite !== 0) {
            $arrTempLinks = array_slice($arrLinks[0], 0, $intLimite);
            $arrTempNomes = array_slice($arrLinks[1], 0, $intLimite);

            $arrLinks[0] = $arrTempLinks;
            $arrLinks[1] = $arrTempNomes;
        }

        // percorrendo os links
        foreach ($arrLinks[0] as $strLink) {
            $strUrl  = String::getUrl($strLink);
            $strNome = urldecode($arrLinks[1][ $intIndice ]);

            $arrRetorno[ $strNome ] = $strUrl;

            $intIndice++;
        }

        // ordenando a array
        ksort($arrRetorno);

        return $arrRetorno;
    }

    /**
     * Retorna a lista de Favoritos ordenada randomicamente
     *
     * @static
     * @access public
     * @param  string $strArquivo
     * @return mixed
     */
    public static function getRandom($strArquivo = null, $intLimite = 0)
    {
        $arrRetorno = array();
        $arrLinks   = $arrLinksOrig = self::getAll($strArquivo, 0);

        // Limitando, caso necessário
        if ($intLimite !== 0) {
            $intInit      = rand(0, count($arrLinks));
            $arrTempLinks = array_slice($arrLinks, $intInit, $intLimite);

            $arrLinks = $arrTempLinks;
        }

        // randomizando
        shuffle($arrLinks);

        // re-indexando como no formato original
        foreach ($arrLinks as $strUrl) {
            $strNome = array_search($strUrl, $arrLinksOrig);

            $arrRetorno[ $strNome ] = $strUrl;
        }

        return $arrRetorno;
    }
}

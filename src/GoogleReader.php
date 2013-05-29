<?php
/**
 * GoogleReader
 *
 * Retorna os links compartilhados via Google Reader
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage GoogleReader
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see Curl
 */
require_once 'Curl.php';

/**
 * @category   Classes
 * @subpackage GoogleReader
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class GoogleReader
{
    /**
     * Objeto XML
     *
     * @static
     * @access  private
     * @var     object
     */
    private static $_objXml;

    /**
     * Lista os links compartilhados no Google Reader
     *
     * @static
     * @access  public
     * @param  integer $usuarioId
     * @param  integer $limite
     * @return array
     */
    public static function listarCompartilhados($usuarioId=null, $limite=null)
    {
        if ($usuarioId !== null) {
            // buscando o conteúdo
            $strUrl = 'http://www.google.com/reader/public/atom/user/';
            $strUrl.= $usuarioId . '/state/com.google/broadcast';

            // Abrindo o feed
            $tmpShares = Curl::get($strUrl);

            self::$_objXml = new SimpleXMLElement($tmpShares);

            $arrSaida = array();

            foreach (self::$_objXml->entry as $objItem) {
                // Pegando a categoria
                $arrBusca = self::$_objXml->xpath("/feed/entry/category");

                // título
                $strTitulo = $objItem->title;

                // Conteúdo
                $strConteudo = $objItem->content;

                // Autor
                $strAutor = $objItem->author->name;

                // Link
                $strLink = $objItem->link->attributes()->href;

                // ID
                $strId = $objItem->id;

                $arrSaida[] = array(
                    'strId'         => (string) sha1($strLink),
                    'strTitulo'     => (string) $strTitulo,
                    'strCategorias' => (string) implode(',', $arrBusca),
                    'strConteudo'   => (string) $strConteudo,
                    'strAutor'      => (string) $strAutor,
                    'strLink'       => (string) $strLink
                );
            }

            // Limitando se necessário
            if ($limite !== null) {
                return array_slice($arrSaida, 0, $limite);
            } else {
                return $arrSaida;
            }
        }
    }
}

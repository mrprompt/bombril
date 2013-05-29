<?php
/**
 * TinyUrl
 *
 * Criação de url curtas via Tiny Url API
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage TinyUrl
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see Cliente_Http
 */
require_once 'Curl.php';

/**
 * @category   Classes
 * @subpackage TinyUrl
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class TinyUrl
{
    /**
     * Cria uma url curta usando a API do TinyURL
     *
     * @param  string $url Url longa a ser diminuída
     * @return string
     */
    public static function create($url = null)
    {
        if ($url !== null) {
            $strUrl = "http://tinyurl.com/api-create.php?url=";
            $strUrl.= urlencode($url);

            $strRetorno = Curl::get($strUrl);

            if ( strlen($strRetorno) > 0 ) {
                return $strRetorno;
            } else {
                throw new Exception('Erro criando url curta.');
            }
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

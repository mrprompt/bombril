<?php
/**
 * IsGd
 *
 * Criação de url curtas via IsGd API
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @copyright  Thiago Paes <mrprompt@gmail.com> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * @copyright  Thiago Paes <mrprompt@gmail.com> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class IsGd
{
    /**
     * Cria uma url curta
     *
     * @param  string $url Url longa a ser diminuída
     * @return string
     */
    public static function create($url = null)
    {
        if ($url !== null) {
            $strUrl = "http://is.gd/api.php?longurl=";
            $strUrl.= urlencode($url);

            $client = new Zend_Http_Client();

            $strRetorno = $client->setUri($strUrl)
                                 ->request()
                                 ->getBody();

            return $strRetorno;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

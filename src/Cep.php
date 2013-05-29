<?php
/**
 * Cep
 *
 * Busca o endereço a partir de um CEP informado
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Cep
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
/**
 * @see Curl
 */
require_once 'Curl.php';

/**
 * @category   Classes
 * @subpackage Cep
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2010
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Cep
{
    /**
     * Busca o endereço a partir do CEP informado
     *
     * @static
     * @access public
     * @param  string           $cep
     * @return SimpleXMLElement
     */
    public static function get($cep = null)
    {
        if (strlen($cep) === 9) {
            $url = "http://ceplivre.pc2consultoria.com/index.php";
            $url .= "?module=cep&formato=xml&cep={$cep}";

            $retorno = Curl::get($url, false);

            try {
                $xml = new SimpleXMLElement($retorno);

                return $xml->cep;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

<?php
/**
 * Gravatar
 *
 * Mostra a imagem do usuário do Gravatar.com
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Gravatar
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Gravatar
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Gravatar
{
    // Gravatar Service
    const GRAV_URL = "http://www.gravatar.com/avatar.php?gravatar_id=";

    // Imagem default, pro caso do usuário for inexistente
    const GRAV_DEFAULT = "http://dailypicture.nl/img/gravatar.jpg";

    /**
     * Retorna a imagem setada no Gravatar.com para o e-mail especificado
     *
     * @static
     * @access  public
     * @param  string  $email address used for gravatar generation
     * @param  integer $size  image size
     * @return string
     */
    public static function get($strEmail = null, $size = 80)
    {
        if ( strlen($strEmail) > 0 ) {
            $strUrl = self::GRAV_URL . md5($strEmail) . "&amp;default=";
            $strUrl.= urlencode(self::GRAV_DEFAULT) . "&amp;size=" . $size;

            return $strUrl;
        } else {
            throw new Exception ('Parâmetros inválidos.');
        }
    }
}

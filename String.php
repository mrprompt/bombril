<?php
/**
 * String
 *
 * Funções úteis para tratamento de string
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage String
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 **/

/**
 * @category   Classes
 * @subpackage String
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class String
{
    /**
     * Transforma caracteres em caracteres estilo HTML
     *
     * @static
     * @access  public
     * @param  string $texto
     * @return string
     */
    public static function str2HTML($texto="")
    {
        $encoding = mb_detect_encoding($texto);

        return mb_convert_encoding($texto, 'HTML-ENTITIES', $encoding);
    }

    /**
     * Converte uma string para o encoding UTF-8
     *
     * @static
     * @access  public
     * @param  string $texto
     * @return string
     */
    public static function str2UTF8($texto="")
    {
        $encoding = mb_detect_encoding($texto);

        return mb_convert_encoding($texto, 'UTF8', $encoding);
    }

    /**
     * Criptografa uma string
     *
     * @static
     * @access  public
     * @param  string $plaintext Texto a ser Criptografado
     * @param  string $password  chave criptográfica
     * @return string
     */
    public static function strEncrypt($plaintext, $password)
    {
        return base64_encode($plaintext . $password);
    }

    /**
     * Decriptografa uma string
     *
     * @static
     * @access  public
     * @param  string $enctext  Texto Criptografado
     * @param  string $password chave criptográfica
     * @return string
     */
    public static function strDecrypt($enctext, $password)
    {
        $passLength = strlen($password);
        $strdecode  = base64_decode($enctext);

        return substr($strdecode, 0, -$passLength);
    }

    /**
     * Gera uma string aleatória contendo letras e/ou números
     *
     * @static
     * @access  public
     * @param  integer $length
     * @return string
     */
    public static function getRandomString($length = 10)
    {
        // you could repeat the alphabet to get more randomness
        $template   = "1234567890abcdefghijklmnopqrstuvwxyz";
        $rndstring  = null;

        for ($a = 0; $a <= $length; $a++) {
            $b          = rand(0, strlen($template) - 1);
            $rndstring .= $template[$b];
        }

        return $rndstring;
    }

    /**
     * Busca por um padrão de url no texto e transforma em link
     *
     * @static
     * @access  public
     * @param  string $strPost
     * @return string
     */
    public static function strToLink ($strPost = null)
    {
        if (strlen($strPost) > 0) {
            // Transformando URL no post em link
            $strER  = '_((http(s)?://|www\.)[a-z0-9-]+(\.[a-z0-9-]+)*';
            $strER .= '(:[0-9]+)?([[:alnum:]]|[[:punct:]])+)_i';

            if (preg_match($strER, $strPost, $arrLink)) {
                $strLink = $arrLink[0];

                if (!preg_match('|^(http(s)?://)|i', $arrLink[0])) {
                    $strLink = 'http://' . $arrLink[0];
                }

                $lnkTag  = ' <a href="' . $strLink . '">' . $strLink . '</a> ';
                $strPost = str_replace($arrLink[0], $lnkTag, $strPost);
            }
        }

        return $strPost;
    }

    /**
     * Busca apenas a url de uma linha
     *
     * @static
     * @access public
     * @param  string $strFavorito
     * @return string
     */
    public static function getUrl($strFavorito = null)
    {
        $tmpRetorno = null;

        if ($strFavorito !== null) {
            $strEr = '{(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*';
            $strEr.= '(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?}i';

            preg_match($strEr, $strFavorito, $arrLink);

            $tmpRetorno = $arrLink[0];
        }

        return $tmpRetorno;
    }
}

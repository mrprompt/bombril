<?php
/**
 * Numeros
 *
 * Funções úteis com números
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Numeros
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 * @since      2006
 **/

/**
 * @category   Classes
 * @subpackage Numeros
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Numeros
{
    /**
     * Insere 'zeros' em uma string, conforme o tamanho solicitado
     *
     * @static
     * @access  public
     * @param  integer $valor
     * @param  integer $tamanho
     * @param  integer $formato
     * @return integer
     */
    public static function comZeros($valor=null, $tamanho=0, $formato=null)
    {
        $modelo = null;

        for ($i=0; $i < $tamanho; $i++) {
            $modelo .= $formato;
        }

        $posNegativa  = strlen($valor) - (strlen($valor) * 2);

        return substr_replace($modelo, $valor, $posNegativa);
    }

    /**
     * Retorna um intervalo entre números
     *
     * @static
     * @access  public
     * @param  integer $inicio   Valor inicial
     * @param  integer $fim      Valor final
     * @param  integer $comzeros
     * @return string
     */
    public static function intervalo($inicio = 0, $fim = 100, $comzeros=0)
    {
        $_saida = array();

        for ($i=$inicio; $i < $fim; $i++) {
            $indice = $comzeros == 1 ? self::comZeros($i, 2, '0') : $i;

            $_saida[ $indice ] = $indice;
        }

        return $_saida;
    }
}

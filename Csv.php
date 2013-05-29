<?php
/**
 * Csv
 *
 * Lê um arquivo CSV e retorna um array
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Csv
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Csv
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Csv
{
    /**
     * Lê um arquivo CSV e retorna uma array com seu conteúdo
     *
     * @static
     * @access  public
     * @param  string $arq O arquivo CSV
     * @param  string $sep Separador de campos
     * @param  string $del Delimitador de campo
     * @return array
     */
    public static function getContent($arq, $sep=",", $del='"')
    {
        if (file_exists($arq)) {
            $saida       = array();
            $hanContatos = fopen($arq, 'r');
            $intLinha    = 0;

            while (($data = fgetcsv($hanContatos, 0, $sep, $del)) !== FALSE) {
                $num = count($data);

                for ($c=0; $c < $num; $c++) {
                    $saida[ $intLinha ] = $data;
                }

                $intLinha++;
            }

            fclose($hanContatos);

            return $saida;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

<?php
/**
 * Manutencao
 *
 * Classe pra controle de Manutencao
 *
 * Pôe a aplicação em modo de manutenção qdo encontrado o arquivo setado no
 * XML de configurações
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Manutencao
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Manutencao
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Manutencao
{
    /**
     * Método construtor
     *
     * Verifica se o arquivo de manutenção existe, em caso positivo,
     * imprime na tela o conteúdo do mesmo e pára a execução dos script
     *
     * @access public
     * @return void
     */
    public function __construct ($arrConfig)
    {
        // pegando o estado de manutenção geral
        $strUrl         = $arrConfig['url'];
        $strFileLocal   = $arrConfig['local'];
        $strFileGeral   = $arrConfig['geral'];

        // checando se todo o sistema está em manutenção
        if (file_exists($strFileLocal) || file_exists($strFileGeral)) {
            // Redirecionando para a url setada no XML
            if ( isset($strUrl) && !isset($_GET['bsid']) ) {
                header('Location:' . $strUrl . '?bsid=' . uniqid());
            }

            die();
        }
    }
}

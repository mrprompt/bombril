<?php
/**
 * Arquivos
 *
 * Lista os arquivos de um diretório, podendo filtrar os
 * mesmos pela extensão desejada
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Arquivos
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
namespace MrPrompt\Util\Filesystem;

/**
 * @category   Classes
 * @subpackage Arquivos
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Arquivos
{
    /**
     *  Lista todas as fotos do diretório
     *
     * @static
     * @access  public
     * @param  string  $strDiretorio
     * @param  string  $strExtensao
     * @param  boolean $booExtensao
     * @return array
     */
    public static function listar($strDiretorio=null, $strExtensao=null,
        $booExtensao=true)
    {
        if ($strDiretorio !== null) {
            if (!file_exists($strDiretorio)) {
               throw new Exception('Arquivo/Diretório não encontrado.');
            }

            $arrArquivos = array ();

            // varrendo os diretórios
            foreach ( new DirectoryIterator($strDiretorio) as $objDiretorio) {
                $strExtensaoFiltro = "(\.([[:alnum:]]){2,})$";
                $strArquivo        = basename($objDiretorio->getPathname());

                if ($strExtensao !== null) {
                    $strExtensaoFiltro = "(\.{$strExtensao})$";
                }

                if (($objDiretorio->isFile() || $objDiretorio->isDir()) &&
                     preg_match("/{$strExtensaoFiltro}/i", $strArquivo) ) {
                    if ($booExtensao !== true) {
                        $strEr      = "/{$strExtensaoFiltro}/i";
                        $strArquivo = preg_replace($strEr, "", $strArquivo);
                    }

                    $strCaminho = basename($objDiretorio->getPathname());

                    $arrArquivos[] = array (
                        'datModificacao'=> $objDiretorio->getMTime(),
                        'strArquivo'    => $strArquivo,
                        'strCaminho'    => $strCaminho,
                        'booDiretorio'  => $objDiretorio->isDir()
                    );
                }
            }

            // ordenando a array
            array_multisort($arrArquivos, SORT_DESC);

            return $arrArquivos;
        } else {
            throw new Exception('Parâmetros inválidos. ');
        }
    }

    /**
     * Força o download de um arquivo
     *
     * @static
     * @access  public
     * @param  string $strArquivo O caminho do arquivo
     * @return void
     */
    public static function download($strArquivo = null)
    {
        if ($strArquivo !== null) {
            $downloadSize  = filesize($strArquivo);
            $filename      = basename($strArquivo);

            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"{$filename}\"");
            header("Accept-Ranges: bytes");
            header("Content-Length: {$downloadSize}");

            readfile($strArquivo);
        }
    }
}

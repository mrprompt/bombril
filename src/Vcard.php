<?php
/**
 * Vcard
 *
 * Lê arquivos vCard
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Vcard
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Vcard
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Vcard
{
    /**
     * Recupera a agenda telefônica (Nome e Telefones) de um arquivo
     * no formato vCard
     *
     * @static
     * @access  public
     * @param  string $strArquivo
     * @return mixed
     */
    public static function getContent($strArquivo = null)
    {
        if ( file_exists($strArquivo) && is_readable($strArquivo) ) {
            // abrindo o arquivo e já convertendo para o encode padrão
            $strVcf = file_get_contents($strArquivo);

            // removendo muitas quebras de linha
            $strVcf = preg_replace('/(\n|\r\n|\r)/', '', $strVcf);

            // deixando cada contato em uma linha
            $strVcf = preg_replace('/(END:VCARD)/', "END:VCARD\n", $strVcf);

            // removendo os "quote printable"
            $strEr  = '/ENCODING=QUOTED-PRINTABLE:/i';
            $strVcf = quoted_printable_decode($strVcf);
            $strVcf = preg_replace($strEr, '', $strVcf);

            // buscando todos os contatos
            $arrVcf = null;
            $strEr  = '/(BEGIN:VCARD)(.+)(END:VCARD)/i';

            preg_match_all($strEr, $strVcf, $arrVcf);

            $arrOut = array();

            foreach ($arrVcf[0] as $arrTemp) {
                $strNome   = null;
                $arrTels   = null;

                // buscando o nome do contato
                preg_match('/(FN).+(TEL|CELL|HOME|WORK)/i', $arrTemp, $arrNome);

                // se foi achado um nome, limpando e retornando
                if (isset($arrNome[0]) && $strNome === null) {
                    $strEr   = '/(FN|TEL|CELL|HOME|WORK|;|:|[[:digit:]]{8,})/i';
                    $strNome = preg_replace($strEr, '', $arrNome[0]);
                }

                // Buscando todos os telefones
                $strEr = '/((HOME|CELL|TEL|WORK):([[:digit:]]){8,})/i';

                preg_match($strEr, $arrTemp, $arrTempTels);

                // limpando e inserindo na array de retorno
                if ( isset($arrTempTelefones[0]) ) {
                    $strEr = '/(HOME|CELL|TEL|:|;)/i';
                    $arrTels[] = preg_replace($strEr, '', $arrTempTels[0]);
                }

                // retornando os dados do contato
                $arrOut[] = array(
                    'strNome'       => htmlentities($strNome),
                    'arrTelefones'  => $arrTels,
                );
            }

            // retornando tudo
            return $arrOut;
        } else {
            throw new Exception('Não foi possível ler o vCard');
        }
    }
}

<?php
/**
 * Picasa
 *
 * Exibe as postagens efetuadas no Picasa através do Feed de Álbuns públicos
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Picasa
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 * */
/**
 * @see Curl
 */
require_once 'Curl.php';
/**
 * @category   Classes
 * @subpackage Picasa
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Picasa
{
    /**
     * Lista os álbuns públicos do usuário
     *
     * @static
     * @access public
     * @param  string $strUsuario Nome do usuário
     * @return array
     */
    public static function listarAlbuns($strUsuario = null, $intLimite = 0)
    {
        if (preg_match('/([[:alnum:]]|[[:punct:]])/', $strUsuario)) {
            // Url do Feed
            $strUrlAlbum = 'http://picasaweb.google.com/data/feed/base/';
            $strUrlAlbum .= 'user/' . $strUsuario . '?alt=rss&kind=album&';
            $strUrlAlbum .= 'hl=pt_BR&access=public';

            // Abrindo o Feed
            $strPicasaFeed = Curl::get($strUrlAlbum, false);

            // Lendo o XML
            $objPicasa = simplexml_load_string($strPicasaFeed);

            // Buscando todos os namespaces do XML
            $namespaces = $objPicasa->getNamespaces(true);

            // Registrando os mesmos
            foreach ($namespaces as $prefix => $ns) {
                $objPicasa->registerXPathNamespace($prefix, $ns);
            }

            // Criando um array com as thumbs de todos os álbuns
            $arrThumbs = $objPicasa->xpath('//media:group/media:thumbnail');

            // Contabilizando o total de álbuns para então percorrer a array
            $intTotalAlbums = count($objPicasa->channel->item);

            $arrAlbuns = array();

            for ($intIndice = 0; $intIndice < $intTotalAlbums; $intIndice++) {
                // Pegando a Id do álbum
                $strGuid = $objPicasa->channel->item[$intIndice]->guid;
                $strEr = '/^(.*\/' . $strUsuario . '\/albumid\/)/';
                $intId = preg_replace($strEr, '', $strGuid);
                $intId = preg_replace('/[^[:digit:]]/', '', $intId);

                // Título do álbum
                $strTitulo = $objPicasa->channel->item[$intIndice]->title;

                // Descrição do álbum
                $strDesc = $objPicasa->channel->item[$intIndice]->description;

                // Link do álbum
                $strLink = $objPicasa->channel->item[$intIndice]->link;
                $strEr = '/^(.*\/' . $strUsuario . '\/)/';
                $strLink = preg_replace($strEr, '', $strLink);
                $strUrl = $objPicasa->channel->item[$intIndice]->link;

                // Thumbnail
                $strThumb = $arrThumbs[$intIndice]->attributes()->url;

                $arrAlbuns[] = array(
                    'strFeed' => (string) $strUrlAlbum,
                    'strTitulo' => (string) $strTitulo,
                    'strDescricao' => (string) $strDesc,
                    'strLink' => (string) $strLink,
                    'strUrl' => (string) $strUrl,
                    'strThumb' => (string) $strThumb,
                    'intAlbumId' => $intId
                );
            }

            // Limitando, caso necessário
            if ($intLimite !== 0) {
                $arrSaidaLimitada = array_slice($arrAlbuns, 0, $intLimite);
                $arrAlbuns = $arrSaidaLimitada;
            }

            return $arrAlbuns;
        } else {
            throw new Exception('Parâmetro inválido.');
        }
    }

    /**
     * Lista o conteúdo de um álbum
     *
     * @static
     * @access public
     * @param  string  $strUsuario Nome do usuário
     * @param  integer $intAlbumId ID do álbum
     * @param  integer $intLargura Largura das thumbs (72, 144, 288)
     * @param  string  $strChave   Chave de acesso ao álbum, caso privado
     * @return array
     */
    public static function listarFotos($strUsuario = null, $intAlbumId = null,
            $intLargura = 72, $strChave = null)
    {
        if ($strUsuario !== null && $intAlbumId !== null) {
            $strUrlAlbum = "http://picasaweb.google.com";
            $strUrlAlbum .= "data/feed/base/user/{$strUsuario}";
            $strUrlAlbum .= "/albumid/{$intAlbumId}?alt=rss";
            $strUrlAlbum .= "&kind=photo&hl=pt_BR";

            if ($strChave !== null) {
                $strUrlAlbum .= '&authkey=' . $strChave;
            }

            $strPicasaFeed = Curl::get($strUrlAlbum, false);

            $arrAlbuns = array();

            if (strlen($strPicasaFeed) > 0) {
                // Lendo o XML
                $arrAlbuns = self::processaFeedAlbum(
                        $strPicasaFeed,
                        $intLargura,
                        $strUrlAlbum
                );
            }

            return $arrAlbuns;
        } else {
            throw new Exception('Parâmetro inválido. ' . __METHOD__);
        }
    }

    public static function processaFeedAlbum($xmlAlbum, $intLargura, $strUrlAlbum = null)
    {
        $objPicasa = simplexml_load_string($xmlAlbum);

        // selecionando o índice correto para a thumb
        switch ($intLargura) {
            case 288:
                $intLargura = 2;
                break;
            case 144:
                $intLargura = 1;
                break;
            default:
                $intLargura = 0;
                break;
        }
        $arrAlbuns = array();
        $i = 0;

        foreach ($objPicasa->channel->item as $entry) {
            // Padrão do media-rss
            $strMrss = 'http://search.yahoo.com/mrss/';
            $objMedia = $entry->children($strMrss);
            $objThumb = $objMedia->group->thumbnail[$intLargura];
            $arrThumb = $objThumb->attributes();

            $strAlbum = $objPicasa->channel->title;
            $strTitulo = $objPicasa->channel->item[$i]->title;
            $strDesc = $objPicasa->channel->item[$i]->description;
            $strDesc = strip_tags($strDesc);
            $strLink = $objPicasa->channel->item[$i]->link;

            // Pegando o arquivo
            $objFoto = $objMedia->group->content[0];
            $arrFoto = $objFoto->attributes();
            $strArquivo = urlencode($arrFoto['url']);

            $arrAlbuns[] = array(
                'strFeed' => (string) $strUrlAlbum,
                'strAlbum' => (string) $strAlbum,
                'strTitulo' => (string) $strTitulo,
                'strDescricao' => (string) $strDesc,
                'strLink' => (string) $strLink,
                'strThumb' => (string) $arrThumb['url'],
                'strArquivo' => (string) $strArquivo
            );

            $i++;
        }

        return $arrAlbuns;
    }
}

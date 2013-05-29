<?php
/**
 * Flickr
 *
 * Lista álbuns e fotos utilizando a API do Flickr
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Flickr
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see Curl
 */
require_once 'Curl.php';

/**
 * @category   Classes
 * @subpackage Flickr
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Flickr
{
    /**
     * Faz uma chamada a API e decodifica a resposta
     *
     * @static
     * @access  public
     * @param  array $arrParms Parâmetros necessários a chamada
     * @return array
     */
    private static function montaChamada($arrParms = array())
    {
        if (is_array($arrParms)) {
            $encParams = array();
            $arrParams = array (
                'format' => 'php_serial'
            );

            $arrParmsFinal = array_merge($arrParms, $arrParams);

            foreach ($arrParmsFinal as $k => $v) {
                $encParams[] = urlencode($k).'='.urlencode($v);
            }

            $strUrl = "http://api.flickr.com/services/rest/?";
            $strUrl.= implode('&', $encParams);

            $strRetorno = Curl::get($strUrl, false);

            return unserialize($strRetorno);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * buscando o nsid do usuario
     *
     * @static
     * @access  public
     * @param  string $strUsuario
     * @param  string $strChaveApi
     * @return array
     */
    public static function getUsuarioId($strUsuario = null, $strChaveApi = null)
    {
        if ($strUsuario !== null && $strChaveApi !== null) {
            $params = array (
                'api_key'	=> $strChaveApi,
                'method'	=> 'flickr.people.findByUsername',
                'username'	=> $strUsuario,
            );

            return self::montaChamada($params);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Lista o conteúdo de um álbum
     *
     * @static
     * @access  public
     * @param  string  $usuario    Nome do usuário
     * @param  integer $albumId    ID do álbum
     * @param  integer $intLargura Largura das thumbs (72, 144, 288)
     * @return array
     */
    public static function listarFotos($usuario = null, $strChave = null,
        $intTotalPorPagina = 10, $intPagina = 1)
    {
        if ($usuario !== null && $strChave !== null) {
            // buscando o nsid do usuario
            $arrUsuario = self::getUsuarioId($usuario, $strChave);

            // fotos públicas
            $arrParms = array(
                'api_key'       => $strChave,
                'method'        => 'flickr.people.getPublicPhotos',
                'user_id'       => $arrUsuario['user']['id'],
                'safe_search'   => 1,
                'per_page'      => $intTotalPorPagina,
                'page'          => $intPagina,
            );

            $arrChamada = self::montaChamada($arrParms);

            $ind = 0;

            foreach ($arrChamada['photos']['photo'] as $arrDet) {
                // buscando as thumbs
                $arrThumbs = self::photosGetSizes($strChave, $arrDet['id']);

                // anexando ao retorno original
                $arrChamada['photos']['photo'][ $ind ]['images'] = $arrThumbs;

                // incrementando o índice da foto antes de ir para a próxima
                $ind++;
            }

            return $arrChamada;
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Recupera as thumbs da foto
     *
     * @static
     * @access  public
     * @param  string $strChaveApi
     * @param  string $strPhotoId
     * @return array
     */
    public static function photosGetSizes($strChaveApi=null, $strPhotoId=null)
    {
        if ($strChaveApi !== null && $strPhotoId !== null) {
            $arrParms = array(
                'api_key'	=> $strChaveApi,
                'method'	=> 'flickr.photos.getSizes',
                'photo_id'	=> $strPhotoId,
            );

            return self::montaChamada($arrParms);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Recupera as informações do usuário
     *
     * @static
     * @access  public
     * @param  string $strChaveApi
     * @param  string $strNsidUsuario
     * @return array
     */
    public static function getUsuarioInfo($strChaveApi=null, $strUsuario=null)
    {
        if ($strChaveApi !== null && $strUsuario !== null) {
            // buscando o nsid do usuario
            $arrUsuario = self::getUsuarioId($strUsuario, $strChaveApi);

            $arrParms = array(
                'api_key'	=> $strChaveApi,
                'method'	=> 'flickr.people.getInfo',
                'user_id'	=> $arrUsuario['user']['id'],
            );

            $arrRetornoChamada = self::montaChamada($arrParms);

            if ($arrRetornoChamada['stat'] == 'ok') {
                $arrPessoa = $arrRetornoChamada['person'];
                $arrFotos  = $arrPessoa['photos'];

                return array (
                    'intId'             => $arrPessoa['id'],
                    'strNsid'           => $arrPessoa['nsid'],
                    'strUsuario'        => $arrPessoa['username']['_content'],
                    'strNome'           => $arrPessoa['realname']['_content'],
                    'strLocalizacao'    => $arrPessoa['location']['_content'],
                    'strFotosUrl'       => $arrPessoa['photosurl']['_content'],
                    'strPerfilUrl'      => $arrPessoa['profileurl']['_content'],
                    'strPrimeiraFoto'   => $arrFotos['firstdatetaken']['_content'],
                    'intPrimeiraFoto'   => $arrFotos['firstdate']['_content'],
                    'intFotosTotal'     => $arrFotos['count']['_content'],
                );
            }
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Lista os álbuns (sets) do usuário
     *
     * @static
     * @access  public
     * @param  string $strChaveApi
     * @param  string $strNsidUsuario
     * @return array
     */
    public static function getAlbums ($strChaveApi = null, $strUsuario = null)
    {
        if ($strChaveApi !== null && $strUsuario !== null) {
            // buscando o nsid do usuario
            $arrUsuario = self::getUsuarioId($strUsuario, $strChaveApi);

            $arrParms = array(
                'api_key'	=> $strChaveApi,
                'method'	=> 'flickr.photosets.getList',
                'user_id'	=> $arrUsuario['user']['id'],
            );

            return self::montaChamada($arrParms);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Lista as fotos de um determinado álbum
     *
     * @static
     * @access  public
     * @param string  $strChaveApi Chave API
     * @param string  $strUsuario  Usuário
     * @param string  $intAlbum    ID do álbum
     * @param string  $intLimite   Limite de resultados - default 12
     * @param string  $intPagina   Página - default = 1
     * @param string  $strTipo     Tipo (all = default, photos, videos)
     * @param integer $intFiltro   1 public, 2 friends, 3 family,
     *                              4 friends & family, 5 private
     * @return array
     */
    public static function getFotosFromAlbumId()
    {
        // parametros
        $arrArgs = func_get_args();

        if (count($arrArgs) >= 3) {
            $strChaveApi    = $arrArgs[0];
            $strUsuario     = $arrArgs[1];
            $intAlbum       = $arrArgs[2];
            $intLimite      = isset($arrArgs[3]) ? $arrArgs[3] : 12;
            $intPagina      = isset($arrArgs[4]) ? $arrArgs[4] : 1;
            $strTipo        = isset($arrArgs[5]) ? $arrArgs[5] : 'all';
            $intFiltro      = isset($arrArgs[6]) ? $arrArgs[6] : 1;

            // buscando o nsid do usuario
            $arrUsuario = self::getUsuarioId($strUsuario, $strChaveApi);

            $arrParms = array(
                'api_key'       => $strChaveApi,
                'method'        => 'flickr.photosets.getPhotos',
                'user_id'       => $arrUsuario['user']['id'],
                'photoset_id'   => $intAlbum,
                'per_page'      => $intLimite,
                'page'          => $intPagina,
                'media'         => $strTipo,
                'privacy_filter'=> $intFiltro
            );

            return self::montaChamada($arrParms);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

<?php
/**
 * Twitter
 *
 * Cliente de microblog baseado na API do Twitter
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Twitter
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see Curl
 */
require_once 'Curl.php';

/**
 * @see String
 */
require_once 'String.php';

/**
 * @see Data
 */
require_once 'Data.php';

/**
 * @category   Classes
 * @subpackage Twitter
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Twitter
{
    /**
     * Configura  header apropriado para o Twitter aceitar os parâmetros
     *
     * @static
     * @access private
     * @return void
     */
    private static function init()
    {
        // Iniciando a conexão
        Curl::connect();

        // Setando o header correto
        $arrParms = array(
            'Expect:',
            'X-Twitter-Client: ',
            'X-Twitter-Client-Version: ',
            'X-Twitter-Client-URL: '
        );

        Curl::setOption(CURLOPT_HTTPHEADER, $arrParms);
    }

    /**
     * Busca os usuários do Twitter em um texto (o que começa com @)
     *
     * @static
     * @access private
     * @param  string $strPost O texto onde será procurar algum login
     * @return string
     */
    private static function nickToLink($strPost)
    {
        $strEr = '/(@([[:alnum:]]|[[:digit:]]|[[:punct:]])*)/';
        $strFinal = '<a href="http://twitter.com/\0">\0</a>';

        $strPost = preg_replace($strEr, $strFinal, $strPost);

        return str_replace('/@', '/', $strPost);
    }

    /**
     * Lista as postagens do usuário e/ou seus seguidores/amigos
     *
     *
     * @static
     * @access  public
     * @param  string  $strUsuario Login do usuário
     * @param  string  $strSenha   Senha do usuário
     * @param  integer $intPagina  Página a ser exibida
     * @param  integer $intLimite  Limite de postagens
     * @return mixed
     */
    public static function listar($strUsuario = null, $strSenha = null,
                                   $intPagina = 1, $intLimite = 20)
    {
        // Ao menos o usuário é necessário
        if ( $strUsuario === null || strlen($strUsuario) == 0 ) {
            throw new Exception('Parâmetros incorretos. ' . __METHOD__);
        }

        self::init();

        $strUrl = "http://twitter.com/statuses/";
        $arrPost= null;

        // Todas as postagens, inclusive replies e amigos
        if (strlen($strSenha) > 0) {
            $strUrl .= "friends_timeline.json?page={$intPagina}";
            $strUrl .= "&count={$intLimite}";

            $arrPost = Curl::get($strUrl, true, $strUsuario, $strSenha);
        } else {
            // Se não for passada a senha, mostra a timeline do usuário apenas
            $strUrl .= "user_timeline/{$strUsuario}.json?page={$intPagina}";
            $strUrl .= "&count={$intLimite}";

            $arrPost = Curl::get($strUrl);
        }

        // retornando o resultado em um array
        return self::jsonToArray($arrPost);
    }

    /**
     * Atualiza o status
     *
     * @static
     * @access  public
     * @param  string  $strUsuario Usuário do serviço
     * @param  string  $strSenha   Senha do usuário do serviço
     * @param  string  $strStatus  Texto para publicação
     * @param  integer $intId      Publicação ao qual a msg é resposta
     * @return mixed
     */
    public static function inserir($strUsuario = null, $strSenha = null,
                                    $strStatus = null, $intId = null)
    {
        if ($strUsuario !== null && $strUsuario !== null &&
            $strUsuario !== null) {
            self::init();

            $strUrl  = "status=" . urlencode($strStatus);

            if ($intId !== null) {
                $strUrl .= "&in_reply_to_status_id={$intId}";
            }

            $strUrl = 'http://twitter.com/statuses/update.xml';

            $arrParams = array(
                $strUrl,
                $strStatus,
                true,
                $strUsuario,
                $strSenha
            );

            $arrStatus = Curl::post($arrParams);

            return self::xmlToArray($arrStatus);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Deleta um post
     *
     * @static
     * @access  public
     * @param  string $strUsuario Usuário do serviço
     * @param  string $strSenha   Senha do usuário do serviço
     * @param  string $intId      Id da publicação
     * @return mixed
     */
    public static function apagar($usuario=null, $senha=null, $intId=null)
    {
        if ($usuario !== null && $senha !== null && $intId !== null) {
            self::init();

            $strUrl = "http://twitter.com/statuses/destroy/{$intId}.xml";
            $xml = Curl::post($strUrl, null, true, $usuario, $senha);

            return self::xmlToArray($xml);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Carrega as informações do usuário
     *
     * @static
     * @access  public
     * @param  string $strUsuario
     * @return mixed
     */
    public static function infoUsuario($strUsuario = null)
    {
        if ($strUsuario !== null) {
            $arrUsuario = null;

            self::init();

            $strUrl = "http://twitter.com/users/show/{$strUsuario}.json";
            $strRetorno = Curl::get($strUrl);

            if ( strlen($strRetorno) > 0 ) {
                $objTwittes = json_decode($strRetorno, true);

                if ( isset($objTwittes['id']) ) {
                    $arrUsuario = array (
                        'intId'            => $objTwittes['id'],
                        'strAutor'         => $objTwittes['screen_name'],
                        'datCriacao'       => $objTwittes['created_at'],
                        'strAvatar'        => $objTwittes['profile_image_url'],
                        'strNome'          => $objTwittes['name'],
                        'strDescricao'     => $objTwittes['description'],
                        'strLocalizacao'   => $objTwittes['location'],
                        'intSeguidores'    => $objTwittes['followers_count'],
                        'intAmigos'        => $objTwittes['friends_count'],
                        'intFavoritos'     => $objTwittes['favourites_count'],
                        'intPostagens'     => $objTwittes['statuses_count']
                    );

                    return $arrUsuario;
                } else {
                    throw new Exception($objTwittes['error']);
                }
            } else {
                throw new Exception('Erro interno. Twitter baleiando.');
            }
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }

    /**
     * Retorna o conteúdo do xml do twitter como uma Array associativa
     *
     * @static
     * @access  public
     * @param  string $strXmlStatus O Xml de retorno
     * @return mixed
     */
    public static function xmlToArray($strXmlStatus)
    {
        $arrPosts  = array ();

        if ( strlen($strXmlStatus) > 0 ) {
            // Lendo o XML
            $objTwittes = new SimpleXMLIterator ($strXmlStatus);

            // Indo para o início da iteração para começar a percorrer os
            // atributos de retorno
            $objTwittes->rewind();

            while ( $objTwittes->key() !== false ) {
                $strPostTemp = (string) $objTwittes->current()->text;
                $strPostTemp = String::strToLink($strPostTemp);
                $strPostTemp = self::nickToLink($strPostTemp);
                $strAutor      = $objTwittes->current()->user->screen_name;
                $datPublicacao = $objTwittes->current()->created_at;
                $datPublicacao = Data::horaGmt2Br($datPublicacao);
                $urlAvatar = $objTwittes->current()->user->profile_image_url;
                $strNome = $objTwittes->current()->user->name;
                $strDesc = $objTwittes->current()->user->description;
                $strLocal= $objTwittes->current()->user->location;

                $arrPosts[] = array (
                    'intId'            => (int) $objTwittes->current()->id,
                    'strAutor'         => (string) $strAutor,
                    'strTexto'         => (string) $strPostTemp,
                    'datPublicacao'    => (string) $datPublicacao,
                    'strAvatar'        => (string) $urlAvatar,
                    'strNome'          => (string) $strNome,
                    'strDescricao'     => (string) $strDesc,
                    'strLocalizacao'   => (string) $strLocal
                );

                $objTwittes->next();
            }

            return $arrPosts;
        } else {
            throw new Exception('Retorno inválido.');
        }
    }

    /**
     * Retorna o conteúdo json do twitter como uma Array associativa
     *
     * @static
     * @access  public
     * @param  string $strJsonStatus
     * @return mixed
     */
    public static function jsonToArray($strJsonStatus)
    {
        $arrPosts  = array ();

        if ( strlen($strJsonStatus) > 0 ) {
            $arrTwittes = json_decode($strJsonStatus, true);
            $intTwittes = count($arrTwittes);

            if ( !isset($arrTwittes['error']) ) {
                for ($i = 0; $i < $intTwittes; $i++) {
                    $strPostTemp = (string) $arrTwittes[ $i ]['text'];
                    $strPostTemp = String::strToLink($strPostTemp);
                    $strPostTemp = self::nickToLink($strPostTemp);
                    $arrUsuario  = $arrTwittes[ $i ]['user'];
                    $datPublicacao = $arrTwittes[$i]['created_at'];
                    $datPublicacao = Data::horaGmt2Br($datPublicacao);
                    $strAvatar = $arrUsuario['profile_image_url'];
                    $strNome   = $arrUsuario['name'];
                    $strDesc   = $arrUsuario['description'];
                    $strLocal  = $arrUsuario['location'];
                    $strOrigem = $arrTwittes[ $i ]['source'];
                    $strAutor  = $arrUsuario['screen_name'];

                    $arrPosts[]  = array (
                        'intId'            => (int) $arrTwittes[ $i ]['id'],
                        'strAutor'         => (string) $strAutor,
                        'strTexto'         => (string) $strPostTemp,
                        'datPublicacao'    => (string) $datPublicacao,
                        'strAvatar'        => (string) $strAvatar,
                        'strNome'          => (string) $strNome,
                        'strDescricao'     => (string) $strDesc,
                        'strLocalizacao'   => (string) $strLocal,
                        'strOrigem'        => (string) $strOrigem
                    );

                    unset($objDateTime);
                }

                return $arrPosts;
            } else {
                throw new Exception($arrTwittes['error']);
            }
        } else {
            throw new Exception('Retorno inválido');
        }
    }

    /**
     * Efetua uma busca no Twitter
     *
     * @static
     * @access  public
     * @param  string  $strBusca
     * @param  string  $strLang
     * @param  integer $intSinceId
     * @param  integer $intLimite
     * @return mixed
     */
    public static function buscar($strBusca = null, $strLang = 'pt',
                                  $intSinceId = null, $intLimite = 15)
    {
        if ($strBusca !== null) {
            $arrRetorno = array ();

            self::init();

            if ($intSinceId !== null) {
                $strLang .= '&since_id=' . $intSinceId;
            }

            $strBuscaUrl  = 'http://search.twitter.com/search.json';
            $strBuscaUrl .= '?lang=' . $strLang .'&q=+' . urlencode($strBusca);
            $strBuscaUrl .= '&rpp=' . $intLimite;

            $strRetorno = Curl::get($strBuscaUrl);

            if (strlen($strRetorno) > 0) {
                $objBusca = json_decode($strRetorno);

                foreach ($objBusca->results as $arrBusca) {
                    $strTexto = $arrBusca->text;
                    $intUsuario = $arrBusca->to_user_id;
                    $strDestino = $arrBusca->to_user;
                    $strOrigem = $arrBusca->from_user;
                    $intFromUserId = $arrBusca->from_user_id;
                    $intId = $arrBusca->id;
                    $strLanguage    = $arrBusca->iso_language_code;
                    $strAvatar      = $arrBusca->profile_image_url;
                    $datCriacao = $arrBusca->created_at;
                    $datCriacao = Data::horaGmt2Br($datCriacao);

                    $arrRetorno[] = array (
                        'strTexto'      => (string) $strTexto,
                        'intToUserId'   => (int) $intUsuario,
                        'strToUser'     => (string) $strDestino,
                        'strFromUser'   => (string) $strOrigem,
                        'intId'         => (int) $intId,
                        'intFromUserId' => (int) $intFromUserId,
                        'strLanguage'   => (string) $strLanguage,
                        'strSource'     => (string) $arrBusca->source,
                        'strAvatar'     => (string) $strAvatar,
                        'datCriacao'    => (string) $datCriacao
                    );
                }

                return $arrRetorno;
            }
        } else {
            throw new Exception('Parâmetros inválidos');
        }
    }
}

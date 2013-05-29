<?php
/**
 * Buscape
 *
 * Busca produtos através do Webservice do Buscapé
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Buscape
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
/**
 * @see Curl
 */
require_once 'Curl.php';
/**
 * @category   Classes
 * @subpackage Buscape
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Buscape
{

    /**
     * URL de conexão
     *
     * @static
     * @access private
     */
    private static $_url;
    /**
     * Chave de segurança do afiliado
     *
     * @var string
     */
    private static $_chave;
    /**
     * Id do afiliado
     *
     * @var integer
     */
    private static $_id;

    /**
     * Valida os parâmetros de autenticação para o webservice
     *
     * @static
     * @access  private
     * @param array $arrAuth Um array com os índices
     *          strChave (chave do usuário)
     *          intId (ID do usuário)
     * @return void
     */
    private static function validaAutenticacao($arrAuth = null)
    {
        if (!is_array($arrAuth)) {
            throw new Exception('Parâmetros inválidos.');
        }

        if (!isset($arrAuth['strChave']) || !isset($arrAuth['intId'])) {
            throw new Exception('strChave/intId não definidos.');
        }

        self::$_chave = $arrAuth['strChave'];
        self::$_id = $arrAuth['intId'];
    }

    /**
     * Prepara a chamada ao Webservice
     *
     * @static
     * @access  private
     * @return mixed
     */
    private static function load()
    {
        $arrControle = array('{strServico}', '{strChave}', '{intAfiliado}');

        $arrArgs = func_get_args();
        $arrParametros = array($arrArgs[0], self::$_chave, self::$_id);

        // Tratamento os argumentos do serviço
        $strArgsMetodo = null;

        foreach ($arrArgs[1] as $strArgumento => $strParametro) {
            $strArgsMetodo .= '&' . $strArgumento . '=' . $strParametro;
        }

        // O Retorno pode ser tanto em XML (padrão) quanto JSON
        $strTipoRetorno = ( isset($arrArgs[2]) ? $arrArgs[2] : 'xml' );

        // Montando a url final
        $strUrl = "http://bws.buscape.com/service/";
        $strUrl.= "{strServico}/{strChave}/?affiliatedId={intAfiliado}";

        $strUrlServico = str_replace($arrControle, $arrParametros, $strUrl);
        $strUrlServico.= $strArgsMetodo . '&format=' . $strTipoRetorno;

        return Curl::get($strUrlServico);
    }

    /**
     * Efetua uma busca seguindo um padrão
     *
     * @static
     * @access  private
     * @param  string  $produto
     * @param  integer $pagina
     * @param  integer $limite
     * @return array
     */
    public static function buscar($produto, $auth, $pagina=1, $limite=30)
    {
        try {
            self::validaAutenticacao($auth);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $arrParms = array(
            'keyword' => urlencode($produto),
            'page' => $pagina,
            'results' => $limite
        );

        $produtos = self::load('findProductList', $arrParms, 'xml');

        // Tentando ler o XML de retorno
        try {
            $xml = new SimpleXMLElement($produtos);
        } catch (Exception $e) {
            throw new Exception('Retorno inválido');
        }

        $retorno = array();

        foreach ($xml->product as $produto) {
            // link
            $attLinksTemp = $produto->links->link[0]->attributes();
            $atributosLink = $attLinksTemp['url'];

            // nome do produto
            $strNome = $produto->productName;

            // atributos da thumb
            $atributosThumb = $produto->thumbnail->attributes();
            $strArquivo = $atributosThumb['url'];

            // id do produto
            $atributosProduto = $produto->attributes();
            $intId = $atributosProduto['id'];

            // Votos
            $intVotos = $produto->rating->userAverageRating->rating;

            // Comentários
            $intComentarios = $produto->rating->userAverageRating->numComments;

            // Preço do produto
            $floValor = (float) $produto->priceMin;
            $floValor = number_format($floValor, 2, ',', '.');

            // Categoria
            $intCategoria = $atributosProduto['categoryId'];
            $strCategoria = $xml->category->name;

            // Retorno
            $retorno[] = array(
                'titulo' => (string) $strNome,
                'arquivo' => (string) $strArquivo,
                'id' => (integer) $intId,
                'resumo' => (string) $strNome,
                'url' => (string) $atributosLink,
                'votos' => (integer) $intVotos,
                'nota' => (integer) $intVotos,
                'comentarios' => (integer) $intComentarios,
                'valor' => (string) $floValor,
                'tmp_name' => (string) $strArquivo,
                'categoryId' => (string) $intCategoria,
                'categoryNome' => (string) $strCategoria
            );
        }

        return $retorno;
    }

    /**
     * Lista as categorias
     *
     * @static
     * @access  public
     * @param integer $intCategoria Id da categoria
     * @param array   $arrAuth      Um array com os índices strChave
     *          (chave do usuário) e intId (ID do usuário)
     * @return array
     */
    public static function categorias($intCategoria = 0, $arrAuth = array())
    {
        try {
            self::validaAutenticacao($arrAuth);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // buscando
        $arrParms = array(
            'categoryId' => $intCategoria
        );

        $xmlCategorias = self::load('findCategoryList', $arrParms, 'xml');

        // Tentando ler o XML de retorno
        try {
            $objXml = new SimpleXMLElement($xmlCategorias);
        } catch (Exception $e) {
            throw new Exception('Erro buscando categorias. ');
        }

        $retorno = array();

        // Atributos da categoria mestra
        $atributosCategoria = $objXml->category->attributes();

        if ($objXml->subCategory) {
            foreach ($objXml->subCategory as $objCategoria) {
                $attLinksTemp = $objCategoria->links->link[0]->attributes();
                $atributosLink = $attLinksTemp['url'];

                // Categoria
                $strCategoria = $objXml->category->name;

                $atributosCategoria = $objCategoria->attributes();
                $intCategoria = $atributosCategoria['id'];
                $intCategoriaPai = $atributosCategoria['parentCategoryId'];

                // Url
                $atributosThumb = $objCategoria->thumbnail->attributes();
                $strUrl = $atributosThumb['url'];

                $retorno[] = array(
                    'categoria_nome' => (string) $strCategoria,
                    'categoria_id' => (integer) $intCategoria,
                    'parentCategoryId' => (integer) $intCategoriaPai,
                    'titulo' => (string) $objCategoria->name,
                    'arquivo' => (string) $strUrl,
                    'id' => (integer) $atributosCategoria['id'],
                    'url' => (string) $atributosLink,
                    'subCategoryName' => (string) $objCategoria->name
                );
            }
        } else {
            $atributosThumb = $objXml->category->thumbnail->attributes();
            $attLink = $objXml->category->links->link[0]->attributes();
            $atributosLink = $attLink['url'];

            $retorno[] = array(
                'categoria_nome' => (string) $objXml->category->name,
                'categoria_id' => (integer) $atributosCategoria['id'],
                'parentCategoryId' => (integer) $atributosCategoria['id'],
                'titulo' => (string) $objXml->category->name,
                'arquivo' => (string) $atributosThumb['url'],
                'id' => (integer) $atributosCategoria['id'],
                'url' => (string) $atributosLink
            );
        }

        return $retorno;
    }

}

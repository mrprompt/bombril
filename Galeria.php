<?php
/**
 * Galeria
 *
 * Galeria de Fotos baseada em XML
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Galeria
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Galeria
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Galeria
{
    /**
     * O objeto do SimpleXML
     *
     * @static
     * @access  private
     * @var     object
     */
    private static $_objXml;

    /**
     * O conteúdo do xml
     *
     * @static
     * @access  private
     * @var     string
     */
    private static $_strXml;

    /**
     * O caminho do arquivo xml
     *
     * @static
     * @access  private
     * @var     string
     */
    private static $_strFile;

    /**
     * Lê o XML com as galerias senão cria o mesmo
     *
     * @static
     * @access  public
     * @return object
     */
    private static function init()
    {
        $strDir = dirname($_SERVER['SCRIPT_FILENAME']);

        self::$_strFile = $strXml . '/galerias/galerias.xml';

        if ( !file_exists(self::$_strFile) ) {
            $strCabecalho = '<?xml version="1.0" encoding="UTF-8"?>';
            $strCabecalho.= '<galerias></galerias>';

            file_put_contents(self::$_strFile, $strCabecalho);
        }

        try {
            self::$_strXml = file_get_contents(self::$_strFile);

            self::$_objXml = new SimpleXMLElement(self::$_strXml);
        } catch (Exception $objException) {
            throw new Exception('Impossível ler xml ' . __METHOD__);
        }
    }

    /**
     * Adiciona uma nova galeria
     *
     * @static
     * @access  public
     * @param  array   $arrOpcoes
     * @return boolean
     */
    public static function adicionar($arrOpcoes = null)
    {
        if ( is_array($arrOpcoes) && sizeof($arrOpcoes) > 0 ) {
            self::init();

            reset($arrOpcoes);

            $novoAlbum = self::$_objXml->addChild('galeria');

            while ( key($arrOpcoes) !== null ) {
                $strChave = key($arrOpcoes);
                $strValor = current($arrOpcoes);

                $novoAlbum->addAttribute($strChave, $strValor);

                next($arrOpcoes);
            }

            return file_put_contents(self::$_strFile, self::$_objXml->asXML());
        }
    }

    /**
     * Lista todas as galerias obedecendo o critério de busca -
     * conforme o padrão criado no XML
     *
     * @static
     * @access  public
     * @param  array $criterio
     * @return array
     */
    public static function listar($criterio = null)
    {
        self::init();

        // Filtros de busca
        if ( is_array($criterio) ) {
            $strFiltro = null;

            foreach ($criterio as $strIndice => $strCriterio) {
                $strFiltro .= "[@{$strIndice}='{$strCriterio}']";
            }

            $_strXmlWhere = "/galerias/galeria{$strFiltro}";
        } else {
            $_strXmlWhere = "/galerias/galeria";
        }

        // Buscando comunidades
        $arrBusca   = self::$_objXml->xpath($_strXmlWhere);
        $arrSaida   = array();
        $arrIndices = array();

        reset($arrBusca);

        while ( key($arrBusca) !== null) {
            $curComunidade = current($arrBusca);
            $attComunidade = (array) $curComunidade->attributes();
            $arrComunidade = $attComunidade['@attributes'];

            if ( !in_array($arrComunidade['id'], $arrIndices) ) {
                $arrSaida[]     = $arrComunidade;
                $arrIndices[]   = $arrComunidade['id'];
            }

            next($arrBusca);
        }

        return $arrSaida;
    }

    /**
     * Remove uma galera/foto
     *
     * @static
     * @access  public
     * @param  string  $strId
     * @return boolean
     */
    public static function apagar($strId)
    {
        self::init();

        $galeria = self::$_objXml->xpath("/galerias/galeria[@id='{$strId}']");

        if ($galeria[0]) {
            $oNode = dom_import_simplexml($galeria[0]);
            $oNode->parentNode->removeChild($oNode);
        }

        $strNode = "/galerias/galeria[@galeria='{$strId}']";
        $galeria = self::$_objXml->xpath($strNode);

        if ($galeria[0]) {
            $oNode = dom_import_simplexml($galeria[0]);

            $oNode->parentNode->removeChild($oNode);
        }

        return file_put_contents(self::$_strFile, self::$_objXml->asXML());
    }
}

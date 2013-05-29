<?php
/**
 * Erro
 *
 * Classe para controle de erros.
 *
 * Loga erros e excessões ocorridas, salva num arquivo de logs e mostra
 * ou redireciona para uma página de erro pré-configurada.
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Erro
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
/**
 * @see Log
 */
require_once dirname(__FILE__) . '/Log.class.php';

/**
 * @see funcoes.php
 */
require_once dirname(__FILE__) . '/../../funcoes.php';

/**
 * @category   Classes
 * @subpackage Erro
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Erro extends Exception
{

    /**
     * Url de redirecionamento
     *
     * @access  private
     * @var     string
     */
    private static $_urlRedir;
    /**
     * Arquivo de log
     *
     * @access  private
     * @var     string
     */
    private static $_arquivo;
    /**
     * O arquivo de inclusão para mostrar na tela
     *
     * @access  private
     * @var     string
     */
    private static $_include;
    /**
     * E-mail que ir� receber o aviso de erro, separados por v�rgula
     *
     * @access private
     * @var    string
     */
    private static $_emails;
    /**
     * Mensagem de erro gerada
     *
     * @access private
     * @var    string
     */
    private static $_erro;
    /**
     * Tipo de erro gerado
     *
     * @access private
     * @var    string
     */
    private static $_erroTipo;

    /**
     * Ativa o controle de erros e excessões para o site
     *
     * @access public
     * @param  array $arrConfig Configurações de erro
     * @return void
     */
    public function __construct($arrConfig)
    {
        // carregando configurações
        self::$_arquivo  = $arrConfig['arquivo'];
        self::$_include  = $arrConfig['include'];
        self::$_urlRedir = $arrConfig['url'];
        self::$_emails   = $arrConfig['emails'];

        // desabilitando a mostragem de erros
        ini_set('display_errors', false);

        // setando o controle de erro
        set_error_handler('Erro::getStaticError', E_ERROR);

        // setando o controle de excessão
        set_exception_handler(array("Erro", "getStaticException"));
    }

    /**
     * Trata um erro e loga, redirecionando para a tela de Erro setada no XML
     *
     * @static
     * @access public
     * @param  integer $nivel Nível de erro que aconteceu
     * @param  string  $desc  Mensagem de erro
     * @param  string  $arq   Arquivo no qual o erro ocorreu
     * @param  integer $linha Número da linha na qual o erro ocorreu
     * @return void
     */
    public static function getStaticError($nivel, $desc, $arq=null, $linha=null)
    {
        $strMsg = "Error: {$desc} - {$arq}({$linha}) [{$nivel}]";

        Log::insert(self::$_arquivo, $strMsg);

        self::$_erro     = $strMsg;
        self::$_erroTipo = 'ERRO';

        self::showError();
    }

    /**
     * Trata uma excessão, logando e redirecionando para a Url setada no XML
     *
     * @static
     * @access  public
     * @param  object $objException Objeto da excessão
     * @return void
     */
    public static function getStaticException($objException)
    {
        $strMensagem = "Exception: {$objException->getMessage()} - ";
        $strMensagem.= "{$objException->getFile()}({$objException->getLine()})";
        $strMensagem.= " [{$objException->getCode()}]";

        Log::insert(self::$_arquivo, $strMensagem);

        self::$_erro     = $strMensagem;
        self::$_erroTipo = 'EXCESS�O';

        self::showError();
    }

    /**
     * Saída do Erro
     *
     * @static
     * @access public
     * @return void
     */
    private static function showError()
    {
        /**
         * Envio um e-mail aos respons�veis, caso esteja setado para tal
         */
        if (strlen(self::$_emails) > 0) {
            $emails = explode(',', self::$_emails);

            foreach ($emails as $email) {
                enviaEmailSmtp($email,
                               self::$_erroTipo . ' ' . date('d/m/Y'),
                               self::$_erro);
            }
        }

        /**
         * Redirecionando se o atributo url estiver setado
         *
         */
        if (strlen(self::$_urlRedir) > 0) {
            header('Location:' . self::$_urlRedir);
        }

        /**
         * Incluindo a tela de erro caso setado o atributo include
         *
         */
        if (strlen(self::$_include) > 0) {
            include_once self::$_include;

            die();
        }
    }

}

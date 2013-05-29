<?php
/**
 * Cookie
 *
 * Controle e tratamento de Cookies com conteúdo criptografado
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Cookie
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @see Criptografia
 */
require_once dirname(__FILE__) . '/Criptografia.class.php';

/**
 * @category   Classes
 * @subpackage Cookie
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Cookie
{
    /**
     * O nome do Cookie
     *
     * @access  private
     * @var     string
     */
    private $_nome = 'default';

    /**
     * Valor serializado do cookie
     *
     * @var string
     */
    private $_valor = null;

    /**
     * Validade do cookie, em segundos
     *
     * @access  private
     * @var     integer
     */
    private $_validade = 60;

    /**
     * Caminho do cookie
     *
     * @access  private
     * @var     string
     */
    private $_caminho = '/';

    /**
     * Domínio de validade;
     *
     * @access  private
     * @var     string
     */
    private $_dominio = 'localhost';

    /**
     * Ambiente seguro? Se sim, somente pode ser lido via HTTPS
     *
     * @access  private
     * @var     boolean
     */
    private $_seguro = 0;

    /**
     * Chave de encriptação
     *
     * @access  private
     * @var     string
     */
    private $_chave = 'default';

    /**
     * Configura a chave de criptografia do cookie
     *
     * @access  public
     * @param  string $chave
     * @return void
     */
    public function setChave($chave = null)
    {
        if ($chave === null) {
            throw new Exception('Chave inv�lida.');
        }

        $this->_chave = $chave;

        return $this;
    }

    /**
     * Recupera a chave de criptografia
     *
     * @access  public
     * @return string
     */
    public function getChave()
    {
        return $this->_chave;
    }

    /**
     * Configura o nome a ser dado ao cookie
     *
     * @access  public
     * @param string $nome
     */
    public function setNome($nome = null)
    {
        if ($nome === null) {
            throw new Exception('Nome invl�lido.');
        }

        $this->_nome = $nome;

        return $this;
    }

    /**
     * Retorna o nome do cookie
     *
     * @access public
     * @return string
     */
    public function getNome()
    {
        return $this->_nome;
    }

    /**
     * Validade do cookie
     *
     * @access  public
     * @param  timestamp $_validade Validade
     * @return void
     */
    public function setValidade($validade = 0)
    {
        $this->_validade = $validade;

        return $this;
    }

    /**
     * Retorna a validade do cookie
     *
     * @access public
     * @return integer
     */
    public function getValidade()
    {
        return $this->_validade;
    }

    /**
     * Valor a ser gravado
     *
     * @access  public
     * @param  string $_valor
     * @return void
     */
    public function setValor($valor = null)
    {
        if ($valor !== null) {
            $this->_valor = Criptografia::encode($valor, $this->_chave);
        }

        return $this;
    }

    /**
     * Retorna o valor do cookie
     *
     * @access public
     * @return string
     */
    public function getValor()
    {
        return $this->_valor;
    }

    /**
     * Configura o domínio ao qual o cookie irá pertencer
     *
     * @access  public
     * @param  string $dominio
     * @return void
     */
    public function setDominio($dominio = null)
    {
        $this->_dominio = $dominio;

        return $this;
    }

    /**
     * Lê o domínio configurado para o cookie
     *
     * @access public
     * @return string
     */
    public function getDominio()
    {
        return $this->_dominio;
    }

    /**
     * Define se o cookie está em um ambiente seguro, se verdadeiro, o mesmo
     * somente poderá ser lido via protocolo HTTPS
     *
     * @access  public
     * @param  boolean $seguro
     * @return void
     */
    public function setSeguro($seguro = false)
    {
        $this->_seguro = $seguro;

        return $this;
    }

    /**
     * Retorna o estado do cookie (seguro ou não)
     *
     * @access public
     * @return boolean
     */
    public function getSeguro()
    {
        return $this->_seguro;
    }

    /**
     * Configura o caminho (path) do cookie
     *
     * @access  public
     * @param  string $_caminho
     * @return void
     */
    public function setCaminho($caminho = null)
    {
        $this->_caminho = $caminho;

        return $this;
    }

    /**
     * Retorna o caminho configurado para o cookie
     *
     * @access public
     * @return string
     */
    public function getCaminho()
    {
        return $this->_caminho;
    }

    /**
     * Grava um cookie.
     *
     * @access  public
     * @return boolean
     */
    public function write()
    {
        setcookie("{$this->_nome}",
                  "{$this->_valor}",
                  "{$this->_validade}",
                  "{$this->_caminho}",
                  "{$this->_dominio}");
    }

    /**
     * Lê um cookie
     *
     * @access  public
     * @param  string $nome O nome do cookie criado via setCookie
     * @return mixed
     */
    public function read($nome = null)
    {
        if ($nome === null) {
            throw new Exception('Parâmetros inválidos.');
        }

        $retorno = null;

        if (isset($_COOKIE[$nome])) {
            $retorno = Criptografia::decode($_COOKIE[$nome], $this->getChave());
        }

        return $retorno;
    }

    /**
     * Remove um cookie
     *
     * @access  public
     * @param  string  $nome
     * @return boolean
     */
    public function delete($nome = null)
    {
        if ($nome !== null) {
            return $this->write($nome, null);
        } else {
            throw new Exception('Parâmetros inválidos.');
        }
    }
}

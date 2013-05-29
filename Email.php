<?php
/**
 * Email
 *
 * Envio simples de e-mail
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Email
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 * @since      2007-04-02
 */

/**
 * @category   Classes
 * @subpackage Email
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Email
{
    /**
     * Envia um e-mail no formato texto puro
     *
     * @static
     * @access public
     * @param  string  $para
     * @param  string  $titulo
     * @param  string  $mensagem
     * @param  string  $remetente
     * @return boolean
     */
    public static function enviar($para, $titulo, $mensagem, $remetente="")
    {
        $_sender = $_SERVER['SERVER_NAME'];

        $headers  = "MIME-Version: 1.0\n";
        $headers .= "From: $remetente\n";
        $headers .= "Reply-To: $remetente\n";
        $headers .= "Date: ".date("r")."\n";
        $headers .= "Subject: $titulo\n";
        $headers .= "Return-Path: $remetente\n";
        $headers .= "Delivered-to: $remetente\n";
        $headers .= "Content-type: text/plain; charset=UTF-8\n";
        $headers .= "Sender: $remetente\n";
        $headers .= "Importance: High\n";
        $headers .= "X-Priority: 1\n";
        $headers .= "X-Sender: $remetente\n";
        $headers .= "X-MSMail-Priority: High\n";
        $headers .= "X-Mailer: php_".phpversion()."\n";
        $headers .= "Organization: self::nome - $_sender\n";
        $headers .= "Message-ID: <".date("YmdHis")."@".$_sender.">\n";

        return mail("$para", "$titulo", "$mensagem", "$headers");
    }
}

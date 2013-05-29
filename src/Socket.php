<?php
 /**
 * Socket
 *
 * Cliente Http baseado em Sockets
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Socket
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Socket
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Socket
{
    /**
     * Envia um POST
     *
     * thankz to php.net manual and comments
     *
     * @static
     * @access  public
     * @param string $url      Destino do POST
     * @param array  $data     Conteúdo
     * @param string $referrer Url de Referência (referer)
     *
     * @return mixed
     */
    public static function postContent($url, $data, $referrer=null)
    {
        // parsing the given URL
        $urlInfo = parse_url($url);

        // Building referrer
        if (!empty($referrer)) {
            // if not given use this script as referrer
            $referrer = $_SERVER["SCRIPT_URI"];
        }

        // making string from $data
        foreach ($data as $key => $value) {
            $values[] = "$key=" . urlencode($value);
        }

        $dataString = implode("&", $values);

        // Find out which port is needed - if not given use standard (=80)
        if (!isset($urlInfo["port"])) {
            $urlInfo["port"]=80;
        }

        // building POST-request:
        $request  = null;
        $request .= "POST {$urlInfo["path"]} HTTP/1.1\n";
        $request .= "Host: {$urlInfo["host"]}\n";
        $request .= "Referer: {$referrer}\n";
        $request .= "Content-type: application/x-www-form-urlencoded\n";
        $request .= "Content-length: " . strlen($dataString) . "\n";
        $request .= "Connection: close\n";
        $request .= "\n";
        $request .= $dataString . "\n";

        $fp = fsockopen($urlInfo["host"], $urlInfo["port"]);

        fputs($fp, $request);

        $result = array();

        while ( !feof($fp) ) {
            $result[] = fgets($fp);
        }

        fclose($fp);

        return $result;
    }

    /**
     * Pega o conteúdo de uma url (como file_get_contents)
     *
     * @static
     * @access  public
     * @param  string  $server Url de busca
     * @param  integer $port   Porta, o padrão é 80
     * @param  string  $file   Arquivo final
     * @return string
     */
    public static function getContent($server, $port="80", $file="/")
    {
        $cont  = "";
        $ip    = gethostbyname($server);
        $fp    = fsockopen($ip, $port);

        if (!$fp) {
            return false;
        } else {
            $com  = "GET $file HTTP/1.1\r\nAccept: **\r\nAccept-Language: ";
            $com .= "de-ch\r\nAccept-Encoding: gzip, deflate\r\nUser-Agent: ";
            $com .= "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\n";
            $com .= "Host: $server:$port\r\nConnection: Keep-Alive\r\n\r\n";

            fputs($fp, $com);

            while (!feof($fp)) {
                $cont .= fread($fp, 500);
            }

            fclose($fp);

            return substr($cont, strpos($cont, "\r\n\r\n") + 4);
        }
    }
}

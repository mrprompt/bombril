<?php
/**
 * Thumb
 *
 * Classe para tratamento de imagens com GD
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @category   Classes
 * @subpackage Thumb
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */

/**
 * @category   Classes
 * @subpackage Thumb
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2009
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
class Thumb
{
    /**
     * Cria uma miniatura da imagem (thumbnail)
     *
     * @static
     * @access  public
     * @param  string  $orig Imagem original
     * @param  string  $dest Imagem de destino (thumbnail)
     * @param  integer $larg Largura da imagem final
     * @param  integer $alt  Altura da imagem final
     * @return string
     */
    public static function create($orig, $dest, $larg=120, $alt=120)
    {
        // tentando recuperar o tipo
        $arqInfo = getimagesize($orig);

        // largura da imagem original
        $imL = $arqInfo[0];

        // altura da imagem
        $imA = $arqInfo[1];

        // tipo da imagem
        $tipo = $arqInfo[2];

        // objeto da imagem
        $img = null;

        switch ($tipo) {
            case "1":
                $img = ImageCreateFromGIF($orig);
                break;

            case "2":
                $img = ImageCreateFromJPEG($orig);
                break;

            case "3":
                $img = ImageCreateFromPNG($orig);
                break;
        }

        if ($img !== null) {
            // largura da nova imagem
            $lrg = ($imL * $alt) / $imA;

            // Criando a cópia em memória da imagem
            $dst = imagecreatetruecolor($lrg, $alt);

            // interlaçando a imagem
            imageinterlace($dst, 1);

            // Copiando a imagem redimensionada
            imagecopyresampled($dst, $img, 0, 0, 0, 0, $lrg, $alt, $imL, $imA);

            // Salvando como JPEG
            imagejpeg($dst, $dest, 100);

            // Liberando a memória
            imagedestroy($img);
            imagedestroy($dst);
        }

        return $dest;
    }
}

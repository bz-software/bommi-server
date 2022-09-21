<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use app\helpers\ControllerHelper;

class CoverUpload extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [
                ['imageFile'], 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 
                'minWidth' => 430, 
                'underWidth' => "A imagem '{file}' é muito pequena, sua largura não pode ser menor que {limit}px",
                'minHeight' => 430, 
                'underHeight' => "A imagem '{file}' é muito pequena, sua altura não pode ser menor que {limit}px",
                'extensions' => 'jpg, png', 
                'wrongExtension' => 'Só arquivos com as seguintes extensões são permitidos: {extensions}',
                'maxSize' => 1024 * 1024 * 2
            ],
        ];
    }
    
    public function upload($idSistema)
    {  
        $path = $idSistema . '_' . md5(date('d-m-Y H:i:s')) . '.' . $this->imageFile->extension;
        if ($this->validate()) {
            $this->imageFile->saveAs(ControllerHelper::pathToSystemCover(true) . $path , false);
            return $path;
        } else {
            return false;
        }
    }
}
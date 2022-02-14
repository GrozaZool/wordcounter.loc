<?php

namespace common\models;

use yii\base\Model;
use yii\web\UploadedFile;

/** @property UploadedFile $textFile */

/**
 * Class UploadForm
 * @package common\models
 */
class UploadForm extends Model
{
    /** @var UploadedFile */
    public $textFile;

    public const DEFAULT_ALLOWED_EXTENSIONS = ['txt'];

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['textFile'], function ($attribute) {
                $allowedExtensions = FileType::find()->select('extension')->column();
                if (empty($allowExtensions)) {
                    $allowedExtensions = self::DEFAULT_ALLOWED_EXTENSIONS;
                }

                if (!in_array($this->$attribute->extension, $allowedExtensions)) {
                    $this->addError($attribute, "{$this->$attribute->extension} extension not allowed");
                    return false;
                }

                return true;
            }, 'skipOnEmpty' => false, 'skipOnError' => false],
            [
                ['textFile'],
                'file',
                'skipOnEmpty' => false,
                'skipOnError' => false,
                'maxSize' => 1024 * 1024 * 15 // максимальный размер в байтах. (15 МБ)
            ],
        ];
    }

    /**
     * @return array ['name' => '', 'size' => '', 'extension' => '', 'wordCount' => '']
     */
    public function getFileInfo(): array
    {
        $wordCount = 0;
        $charList = 'АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя';

        $str = @file_get_contents($this->textFile->tempName);

        if ($str) {
            $wordCount = str_word_count(trim($str), 0, $charList);
        }

        return [
            'name'      => $this->textFile->baseName,
            'size'      => $this->textFile->size,
            'extension' => $this->textFile->extension,
            'wordCount' => $wordCount
        ];
    }
}
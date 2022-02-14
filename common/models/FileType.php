<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fileType".
 *
 * @property int $id
 * @property string $extension
 * @property int $createdAt
 *
 * @property File[] $files
 */
class FileType extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'fileType';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['extension', 'createdAt'], 'required'],
            [['createdAt'], 'integer'],
            [['extension'], 'string', 'max' => 12],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'extension' => 'Extension',
            'createdAt' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Files]].
     *
     * @return ActiveQuery
     */
    public function getFiles(): ActiveQuery
    {
        return $this->hasMany(File::class, ['fileType' => 'id']);
    }
}

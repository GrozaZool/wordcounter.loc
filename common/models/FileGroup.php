<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fileGroup".
 *
 * @property int $id
 * @property string $name
 * @property string $queryCondition
 * @property int $createdAt
 *
 * @property File $file
 */
class FileGroup extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'fileGroup';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'createdAt'], 'required'],
            [['createdAt'], 'integer'],
            [['name', 'queryCondition'], 'string', 'max' => 200],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id'        => 'ID',
            'name'      => 'Имя группы',
            'queryCondition' => 'Условие попадания в группу',
            'createdAt' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[File]].
     *
     * @return ActiveQuery
     */
    public function getFile(): ActiveQuery
    {
        return $this->hasOne(File::class, ['fileGroup' => 'id']);
    }
}

<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "groupRelation".
 *
 * @property int $fileId
 * @property int $fileGroupId
 *
 * @property File $file
 * @property FileGroup $fileGroup
 */
class GroupRelation extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'groupRelation';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['fileId', 'fileGroupId'], 'required'],
            [['fileId', 'fileGroupId'], 'integer'],
            [['fileId', 'fileGroupId'], 'unique', 'targetAttribute' => ['fileId', 'fileGroupId']],
            [['fileId'], 'exist', 'skipOnError' => true, 'targetClass' => File::class, 'targetAttribute' => ['fileId' => 'id']],
            [['fileGroupId'], 'exist', 'skipOnError' => true, 'targetClass' => FileGroup::class, 'targetAttribute' => ['fileGroupId' => 'id']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'fileId' => 'File ID',
            'fileGroupId' => 'File Group ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFile(): ActiveQuery
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFileGroup(): ActiveQuery
    {
        return $this->hasOne(FileGroup::class, ['id' => 'fileGroupId']);
    }
}

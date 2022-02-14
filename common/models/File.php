<?php

namespace common\models;

use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $name
 * @property int|null $fileType
 * @property int|null $wordCount
 * @property int $fileSize
 * @property int $createdAt
 *
 * @property FileType $type
 * @property string $fileGroup
 */
class File extends ActiveRecord
{
    public $fileGroup;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'file';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['fileType', 'wordCount', 'fileSize', 'createdAt'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['fileType'],
                'exist',
                'skipOnError' => true,
                'targetClass' => FileType::class,
                'targetAttribute' => ['fileType' => 'id']
            ],
        ];
    }

    /**
     * @return array
     */
    public function behaviors() : array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'createdAt',
            ]
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id'        => 'ID',
            'name'      => 'Название файла',
            'fileType'  => 'Расширение файла',
            'wordCount' => 'Кол-во слов',
            'fileSize'  => 'Размер',
            'createdAt' => 'Дата создания',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function getGroups(): ActiveQuery
    {
        return $this->hasMany(FileGroup::class, ['id' => 'fileGroupId'])
            ->viaTable(GroupRelation::tableName(), ['fileId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(FileType::class, ['id' => 'fileType']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroupRelations(): ActiveQuery
    {
        return $this->hasMany(GroupRelation::class, ['fileId' => 'id']);
    }


    /**
     * Присваиваем группы
     */
    public function assignGroup() : void
    {
        $groups = FileGroup::find()->all();

        /** @var FileGroup $group */
        foreach ($groups as $group) {

            $isExist = self::find()
                ->where(['id' => $this->id])
                ->andWhere($group->queryCondition)
                ->asArray()
                ->limit(1)
                ->one();

            if ($isExist) {
                $groupRelation = new GroupRelation();
                $groupRelation->fileId = $this->id;
                $groupRelation->fileGroupId = $group->id;
                $groupRelation->save();
            }
        }
    }
}

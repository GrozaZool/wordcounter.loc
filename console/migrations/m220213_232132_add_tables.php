<?php

use yii\db\Migration;

/**
 * Class m220213_232132_add_tables
 */
class m220213_232132_add_tables extends Migration
{
    /**
     * @return false|mixed|void
     * @throws Throwable
     */
    public function safeUp()
    {
        $timestamp = time();

        /*
        Пример того как можно вставлять в yii2.
        $this->createTable('{{%file}}', [
            'id'        => $this->primaryKey(),
            'name'      => $this->string(255)->notNull(),
            'fileType'  => $this->integer(11)->null(),
            'wordCount' => $this->bigInteger(15)->defaultValue(0),
            'fileSize'  => $this->integer(11)->notNull()->defaultValue(0),
            'createdAt' => $this->integer(11)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
        */

        /**
         * Я же, буду использовать SQL запросы.
         */

        /** @noinspection ALL */
        $file = "
            CREATE TABLE `file` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `fileType` int(11) DEFAULT NULL,
              `wordCount` bigint(15) DEFAULT '0',
              `fileSize` int(11) NOT NULL DEFAULT '0',
              `createdAt` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fileType` (`fileType`),
              CONSTRAINT `file_ibfk_2` FOREIGN KEY (`fileType`) REFERENCES `fileType` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ";

        /** @noinspection ALL */
        $fileGroup = "
            CREATE TABLE `fileGroup` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(200) NOT NULL,
              `queryCondition` varchar(200) NOT NULL,
              `createdAt` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8
        ";

        /** @noinspection ALL */
        $fileType = "
            CREATE TABLE `fileType` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `extension` varchar(12) NOT NULL,
              `createdAt` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8
        ";

        /** @noinspection ALL */
        $groupRelation = "
            CREATE TABLE `groupRelation` (
              `fileId` int(11) NOT NULL,
              `fileGroupId` int(11) NOT NULL,
              UNIQUE KEY `uniqFileGroup` (`fileId`,`fileGroupId`) USING BTREE,
              KEY `fileId` (`fileId`),
              KEY `fileGroupId` (`fileGroupId`),
              CONSTRAINT `grouprelation_ibfk_1` FOREIGN KEY (`fileId`) REFERENCES `file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `grouprelation_ibfk_2` FOREIGN KEY (`fileGroupId`) REFERENCES `fileGroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ";

        /** @noinspection ALL */
        $fileGroupData = "
            INSERT INTO `fileGroup` 
                (`id`, `name`, `queryCondition`, `createdAt`) 
            VALUES
                (1, 'Размер файла больше 1 МБ', 'fileSize >= 1048576', {$timestamp}),
                (2, 'Размер файла меньше 1 МБ', 'fileSize <= 1048576', {$timestamp}),
                (3, 'Меньше 10 слов', 'wordCount < 10', {$timestamp}),
                (4, 'Больше 10 слов', 'wordCount > 10', {$timestamp}),
                (5, 'Ровно 10 слов', 'wordCount = 10', {$timestamp})
        ";

        /** @noinspection ALL */
        $fileTypeData = "
            INSERT INTO `fileType` 
                (`id`, `extension`, `createdAt`) 
            VALUES
                (1, 'txt', {$timestamp}),
                (2, 'csv', {$timestamp})
        ";


        $this->execute($fileType);
        $this->execute($fileGroup);

        $this->execute($file);

        $this->execute($fileGroupData);
        $this->execute($fileTypeData);

        $this->execute($groupRelation);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220213_232132_add_tables cannot be reverted.\n";

        return false;
    }
}

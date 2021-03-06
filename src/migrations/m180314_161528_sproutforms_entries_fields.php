<?php

namespace barrelstrength\sproutforms\migrations;

use craft\db\Migration;
use craft\db\Query;
use barrelstrength\sproutforms\fields\formfields\Entries;
use craft\fields\Entries as CraftEntries;

/**
 * m180314_161528_sproutforms_entries_fields migration.
 */
class m180314_161528_sproutforms_entries_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $entriesFields = (new Query())
            ->select(['id', 'handle', 'settings'])
            ->from(['{{%fields}}'])
            ->where(['type' => CraftEntries::class])
            ->andWhere('context LIKE "%sproutForms:%"')
            ->all();

        foreach ($entriesFields as $entryField) {
            $settings = json_decode($entryField['settings'], true);
            $settings['source'] = $settings['source'] ?? null;
            $settings['targetSiteId'] = null;
            $settings['localizeRelations'] = false;
            $settingsAsJson = json_encode($settings);

            $this->update('{{%fields}}', ['type' => Entries::class, 'settings' => $settingsAsJson], ['id' => $entryField['id']], [], false);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180314_161528_sproutforms_entries_fields cannot be reverted.\n";
        return false;
    }
}

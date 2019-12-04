<?php

/**
 * 888888ba                 dP  .88888.                    dP
 * 88    `8b                88 d8'   `88                   88
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b.
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P'
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2017 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Core_Update_Abstract');

/**
 * This migration script should deal with all backward compatibility issues by transferring all old prepare data to new AM format.
 * Since it is possible to reformat existing data, create missing information from existing and shop data and delete
 * unnecessary data, all backward compatibility issues are solved here leaving new plugin code free of legacy code.
 *
 * Populates new magnalister_meinpaket_prepare.ShopVariation column based on magnalister_meinpaket_prepare.VariationConfiguration
 * column and data from magnalister_meinpaket_variantmatching.ShopVariation.
 *
 * Deletes all magnalister_meinpaket_variantmatching records that have nonempty
 * magnalister_meinpaket_variantmatching.CustomIdentifier column value.
 *
 * Converts old ShopVariation column data to new AM format (leaves encoded MP code since there is no way of decoding it back).
 *
 * Decodes codes stored in magnalister_meinpaket_prepare.VariationConfiguration and
 * magnalister_meinpaket_variantmatching.Identifier.
 */
class ML_MeinPaket_Update_PrepareForStandardAM extends ML_Core_Update_Abstract
{
    private $prepareRecordsToUpdate = array();
    private $variantMatchingRecordsToUpdate = array();

    public function execute()
    {
        $this->migratePrepareData();
        $this->migrateVariantMatchingData();

        return parent::execute();
    }

    public function getParameters()
    {
        // Loop execute method until needExecution can't find any not updated records
        return array('again' => true);
    }

    public function needExecution()
    {
        // In case of new plugin, where variantmatching or prepare table does not exist, migration script should not do anything
        if (
            !MLDatabase::getDbInstance()->tableExists('magnalister_meinpaket_prepare') ||
            !MLDatabase::getDbInstance()->tableExists('magnalister_meinpaket_variantmatching')
        ) {
            return false;
        }

        $this->addShopVariationAndVariationThemeColumnsIfNotExists();

        $this->prepareRecordsToUpdate = MLDatabase::getDbInstance()->fetchArray("
             SELECT *
               FROM  `magnalister_meinpaket_prepare`
              WHERE     NULLIF(ShopVariation, '') IS NULL
                    AND NULLIF(VariationConfiguration, '') IS NOT NULL  
                    AND VariationConfiguration != '0'
              LIMIT 100
         ");

        // Until all prepare records are not migrated do not do variant matching records
        if (count($this->prepareRecordsToUpdate) > 0) {
            return true;
        }

        $this->migrateNoneToSplitAllInPrepare();

        // All custom identifier specifics are moved to prepare table, so first delete all custom identifier records from variant matching table
        $this->deleteCustomIdentifierRecordsFromVariantMatching();

        // Detect old records by searching new DataType property in ShopVariation matching configuration
        $this->variantMatchingRecordsToUpdate = MLDatabase::getDbInstance()->fetchArray("
             SELECT *
               FROM `magnalister_meinpaket_variantmatching`
              WHERE `ShopVariation` NOT LIKE '%DataType%' AND `ShopVariation` != '[]'
              LIMIT 100
         ");

        return count($this->variantMatchingRecordsToUpdate) > 0;
    }

    private function addShopVariationAndVariationThemeColumnsIfNotExists()
    {
        if (!MLDatabase::getDbInstance()->columnExistsInTable('ShopVariation', 'magnalister_meinpaket_prepare')) {
            MLDatabase::getDbInstance()->query('
                ALTER TABLE `magnalister_meinpaket_prepare` 
                ADD COLUMN `ShopVariation` text  Null COMMENT ""
          ');
        }

        if (!MLDatabase::getDbInstance()->columnExistsInTable('variation_theme', 'magnalister_meinpaket_prepare')) {
            MLDatabase::getDbInstance()->query('
                ALTER TABLE `magnalister_meinpaket_prepare` 
                ADD COLUMN `variation_theme` varchar(200)  Null COMMENT ""
          ');
        }
    }

    private function migrateNoneToSplitAllInPrepare()
    {
        MLDatabase::getDbInstance()->query('
            UPDATE `magnalister_meinpaket_prepare`
            SET `VariationConfiguration`=\'splitAll\',
                `ShopVariation`=\'[]\',
                `variation_theme`=\'{\"splitAll\":[]}\'
            WHERE `VariationConfiguration` = 0 and `ShopVariation` IS NULL;
          ');
    }

    private function deleteCustomIdentifierRecordsFromVariantMatching()
    {
        MLDatabase::getDbInstance()->query("
          DELETE FROM `magnalister_meinpaket_variantmatching`
          WHERE
              NULLIF(CustomIdentifier, '') IS NOT NULL AND 
              `CustomIdentifier` != '0'
      ");
    }

    protected function migratePrepareData()
    {
        if (empty($this->prepareRecordsToUpdate)) {
            return;
        }

        $dataForReplace = array();
        foreach ($this->prepareRecordsToUpdate as $prepareData) {
            list($identifier, $customIdentifier) = explode(':', $prepareData['VariationConfiguration']);
            $shopVariationRaw = MLDatabase::getDbInstance()->fetchOne("
                SELECT ShopVariation
                FROM  `magnalister_meinpaket_variantmatching`
                WHERE 
                   Identifier = '" . MLDatabase::getDbInstance()->escape($identifier) . "' AND
                   CustomIdentifier = '" . MLDatabase::getDbInstance()->escape($customIdentifier) . "' AND
                   mpID = '" . MLDatabase::getDbInstance()->escape($prepareData['mpID']) . "'
                LIMIT 1;
            ");

            $shopVariation = $this->getConvertedShopVariationAttributes(json_decode($shopVariationRaw, true));

            // After conversion custom identifier is not needed anymore, since all customizations are in ShopVariation column
            $prepareData['VariationConfiguration'] = base64_decode(str_replace('_', '=', $identifier));
            $prepareData['ShopVariation'] = json_encode($shopVariation);
            $dataForReplace[] = $prepareData;
        }

        if (!empty($dataForReplace)) {
            MLDatabase::getDbInstance()->batchinsert('magnalister_meinpaket_prepare', $dataForReplace, true);
        }
    }

    private function migrateVariantMatchingData()
    {
        if (empty($this->variantMatchingRecordsToUpdate)) {
            return;
        }

        foreach ($this->variantMatchingRecordsToUpdate as $variantMatchingData) {
            $shopVariation = $this->getConvertedShopVariationAttributes(json_decode($variantMatchingData['ShopVariation'], true));

            // Method batchinsert can't be used since we must change Identifier and that column is part of unique key
            MLDatabase::getDbInstance()->update(
                'magnalister_meinpaket_variantmatching',
                array(
                    'Identifier' => base64_decode(str_replace('_', '=', $variantMatchingData['Identifier'])),
                    'ShopVariation' => json_encode($shopVariation),
                ),
                array(
                    'mpID' => $variantMatchingData['mpID'],
                    'Identifier' => $variantMatchingData['Identifier'],
                )
            );
        }
    }

    /**
     * Converts AM data prepared with old system to a new AM format
     *
     * @param array $oldShopVariation Matched attributes data from old system
     *
     * @return array Old prepared data but in new AM format
     */
    protected function getConvertedShopVariationAttributes($oldShopVariation) {
        $shopVariation = array();
        foreach ($oldShopVariation as $mpCode => $matchedAttribute) {
            $shopVariation[$mpCode] = array(
                'AttributeName' => $mpCode,
                'Code' => $matchedAttribute['Code'],
                'Kind' => $matchedAttribute['Kind'],
                'Required' => true,
                'DataType' => $matchedAttribute['Kind'] === 'Matching' ? 'select' : 'text',
                'Values' => array(),
                'Error' => false,
            );

            $shopValues = MLFormHelper::getShopInstance()->getAttributeOptions($matchedAttribute['Code']);
            $index = 1;
            foreach ($matchedAttribute['Values'] as $shopKey => $mpKey) {
                if (empty($mpKey) || !array_key_exists($shopKey, $shopValues)) {
                    continue;
                }

                $mpKey = ($matchedAttribute['Kind'] === 'Matching') ? $mpKey : $shopValues[$mpKey];
                $shopVariation[$mpCode]['Values'][$index] = array(
                    'Shop' => array(
                        'Key' => $shopKey,
                        'Value' => $shopValues[$shopKey],
                    ),
                    'Marketplace' => array(
                        'Key' => $mpKey,
                        'Value' => $mpKey,
                        'Info' => "{$mpKey} - (automatisch zugeordnet)",
                    ),
                );
                $index++;
            }
        }

        // Mark matching data as encoded, since decoding can't be done because saved code is lower case of base64_encode result
        if (!empty($shopVariation)) {
            $shopVariation['mpKeyIsEncoded'] = true;

            return $shopVariation;
        }

        return $shopVariation;
    }
}
<?php

namespace Webindiainc\Prx\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $installer = $setup;
        $installer->startSetup();



        /**
         * Create table 'prx_lensusage'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_lensusage'))
		->addColumn(
            'lensusage_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Lensusage Id'
        )
		->addColumn(
            'lensusage_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
		->addColumn(
            'lensusage_subtitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sub Title'
        )
		->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )
		/*->addColumn(
            'lensusage_has_prescription',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Is Prescription'
        )*/
		->addColumn(
            'lensusage_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,2',
            ['nullable' => false],
            'Price'
        )
		->addColumn(
            'lensusage_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )
		->addColumn(
            'youtube_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Youtube Content'
        )
		->addColumn(
            'lensusage_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Active Status'
        )
		->addColumn(
            'lensusage_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Products'
        )
		->setComment(
            'Row Data Table'
        );
        $installer->getConnection()->createTable($table);
		
		
		
		/**
         * Create table 'prx_lenstype'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_lenstype'))
		->addColumn(
            'lenstype_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Lenstype Id'
        )
		->addColumn(
            'lensusage_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lensusage ID'
        )
		->addColumn(
            'lenstype_identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Identifier'
        )
		->addColumn(
            'lenstype_position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lenstype Position'
        )
		->addColumn(
            'lenstype_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
		->addColumn(
            'lenstype_subtitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sub Title'
        )
		->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )
		->addColumn(
            'lenstype_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,2',
            ['nullable' => false],
            'Price'
        )
		->addColumn(
            'lenstype_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )
		->addColumn(
            'youtube_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Youtube Content'
        )
		->addColumn(
            'lenstype_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Active Status'
        )
		->addColumn(
            'lenstype_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Products'
        )
		->addIndex(
            $installer->getIdxName(
                $installer->getTable($installer->getTable('prx_lenstype')),
                ['lenstype_identifier'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['lenstype_identifier'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )
		/* ->addForeignKey(
			$installer->getFkName($installer->getTable('prx_lenstype'), 'lensusage_id', $installer->getTable('prx_lensusage'), 'lensusage_id'),
				'lensusage_id',
				$installer->getTable('prx_lensusage'),
				'lensusage_id',
				\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		) */
		->setComment(
            'Lenstype Table'
        );
        $installer->getConnection()->createTable($table);
		
		
		
		/**
         * Create table 'prx_lensthickness'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_lensthickness'))
		->addColumn(
            'lensthickness_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Lensthickness Id'
        )
		/* ->addColumn(
            'lensusage_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lensusage ID'
        ) */
		->addColumn(
            'lenstype_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lenstype ID'
        )
		->addColumn(
            'lensthickness_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
		->addColumn(
            'lensthickness_subtitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sub Title'
        )
		->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )
		->addColumn(
            'lensthickness_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '10,2',
            ['nullable' => false],
            'Price'
        )
		->addColumn(
            'lensthickness_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )
		->addColumn(
            'youtube_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Youtube Content'
        )
		->addColumn(
            'lensthickness_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Active Status'
        )
		->addColumn(
            'lensthickness_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Products'
        )
		/* ->addForeignKey(
			$installer->getFkName($installer->getTable('prx_lensthickness'), 'lenstype_id', $installer->getTable('prx_lenstype'), 'lenstype_id'),
				'lenstype_id',
				$installer->getTable('prx_lenstype'),
				'lenstype_id',
				\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
		) */
		->setComment(
            'Lensthickness Table'
        );
        $installer->getConnection()->createTable($table);
		
		
		
		/**
         * Create table 'prx_lenstintcolor'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_lenstintcolor'))
		->addColumn(
            'lenstintcolor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Lenstintcolor Id'
        )
		/* ->addColumn(
            'lensusage_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lensusage ID'
        )*/
		->addColumn(
            'lenstype_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Lenstype ID'
        )
		/*->addColumn(
            'lensthickness_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lensthickness ID'
        )*/
		->addColumn(
            'lenstintcolor_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
		->addColumn(
            'lenstintcolor_subtitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sub Title'
        )
		->addColumn(
            'color_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Color Code'
        )
		/*->addColumn(
            'lenstintcolor_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Price'
        )*/
		->addColumn(
            'lenstintcolor_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )
		->addColumn(
            'lenstintcolor_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Active Status'
        )
		->addColumn(
            'lenstintcolor_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Products'
        )
		->setComment(
            'Lenstintcolor Table'
        );
        $installer->getConnection()->createTable($table);
		
		
				
		/**
         * Create table 'prx_lenstintstrength'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_lenstintstrength'))
		->addColumn(
            'lenstintstrength_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Lenstintstrength Id'
        )
		/* ->addColumn(
            'lensusage_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lensusage ID'
        )*/
		->addColumn(
            'lenstype_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Lenstype ID'
        )
		/*->addColumn(
            'lensthickness_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Lensthickness ID'
        )*/
		->addColumn(
            'lenstintstrength_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
		->addColumn(
            'lenstintstrength_subtitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sub Title'
        )
		/*->addColumn(
            'lenstintstrength_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Price'
        )*/
		->addColumn(
            'lenstintstrength_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )
		->addColumn(
            'lenstintstrength_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Active Status'
        )
		->addColumn(
            'lenstintstrength_products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Products'
        )
		->setComment(
            'Lenstintstrength Table'
        );
        $installer->getConnection()->createTable($table);
		
		
		
		/**
         * Create table 'prx_lensprescription'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_lensprescription'))
		->addColumn(
            'lensprescription_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Lenslensprescription Id'
        )
		->addColumn(
            'lensprescription_identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Identifier'
        )
		->addColumn(
            'lensprescription_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
		->addColumn(
            'lensprescription_subtitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sub Title'
        )
		->addColumn(
            'lensprescription_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )
		->addColumn(
            'lensprescription_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Active Status'
        )
		->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Position'
        )
		->setComment(
            'Lensprescription Table'
        );
        $installer->getConnection()->createTable($table);
		
		
		
		
		/**
         * Create table 'prx_customerdata'
        **/
        $table = $installer->getConnection()->newTable($installer->getTable('prx_customerdata'))
		->addColumn(
            'prx_saveid',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Prx Save Id'
        )
		->addColumn(
            'prx_savename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Prx Save Filename'
        )
		->addColumn(
            'prx_customerid',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Customer ID'
        )
		->addColumn(
            'prx_savedata',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Prx Data Value'
        )
		->addColumn(
            'prescription_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Prx Prescription Image'
        )
		->setComment(
            'prx customer data'
        );
        $installer->getConnection()->createTable($table);
		
		
		
        $installer->endSetup();
    }
}

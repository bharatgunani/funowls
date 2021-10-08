<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lenstype;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;

class Save extends \Magento\Backend\App\Action
{

    protected $lenstypeFactory;
    protected $lenstypeCollection;
	protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webindiainc\Prx\Model\LenstypeFactory $lenstypeFactory,
		\Webindiainc\Prx\Model\ResourceModel\Lenstype\Collection $lenstypeCollection,
		UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->lenstypeFactory = $lenstypeFactory;
        $this->lenstypeCollection = $lenstypeCollection;
		$this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
		
		/* image section :start */
		if( isset($data['image']['value']) && $data['image']['value'] !='' ) {
			$imagepath = $data['image']['value'];
			unset($data['image']['value']);
			if( isset($data['image']['delete']) && $data['image']['delete']==1 ) {
				$data['image'] = '';
			} else {
				$data['image'] = $imagepath;
			}
		}
		if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            try{
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('prx/lenstypelogo');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $imagePath = 'prx/lenstypelogo' . $result['file'];
                $data['image'] = $imagePath;
            } catch (\Exception $e) {
            }
        }
		/* image section :end */
		
		$data['lenstype_products'] = '';
		
		if( isset($data['products']) && $data['products'] !='' ) {
			$data['lenstype_products'] = ',' . str_replace('&', ',', $data['products']) . ',';
		}
		
        if (!$data) {
            $this->_redirect('prx/lenstype/addrow');
            return;
        }
		
		if(isset($data['lenstype_identifier']) && $data['lenstype_identifier']!='') {
			$isIdentifierExist = $this->lenstypeCollection->addFieldToFilter('lenstype_identifier', $data['lenstype_identifier']);
			if($isIdentifierExist->getSize() > 0) {
				$this->messageManager->addError(__('Duplicate Identifier.'));
				$this->_redirect('prx/lenstype/addrow');
				return;
			}
		}
		
        try {
            $rowData = $this->lenstypeFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setLensusageId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
		
		// Check if 'Save and Continue'
		if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/addrow', ['id' => $rowData->getId(), '_current' => true]);
			return;
		}
        $this->_redirect('prx/lenstype/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webindiainc_Prx::save');
    }
}

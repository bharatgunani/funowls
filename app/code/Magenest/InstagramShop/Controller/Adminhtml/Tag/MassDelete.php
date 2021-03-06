<?php

namespace Magenest\InstagramShop\Controller\Adminhtml\Tag;

use Magenest\InstagramShop\Model\TaggedPhotoFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var TaggedPhotoFactory
     */
    protected $_photoFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param TaggedPhotoFactory $photoFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TaggedPhotoFactory $photoFactory,
        PageFactory $resultPageFactory
    )
    {
        $this->_photoFactory = $photoFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids)) {
            $this->messageManager->addNoticeMessage(__('Please select photo(s).'));
        } else {
            try {
                $photo = $this->_photoFactory->create();
                foreach ($ids as $id) {
                    $photo->load($id)->delete();
                }
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were deleted.'), count($ids));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_InstagramShop::tag');
    }
}

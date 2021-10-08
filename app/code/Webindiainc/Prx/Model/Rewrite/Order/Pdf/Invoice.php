<?php

	namespace Webindiainc\Prx\Model\Rewrite\Order\Pdf;

	use Magento\Sales\Model\Order\Pdf\Invoice as CoreInvoice;

	class Invoice extends CoreInvoice
	{
		
		protected function _drawHeader(\Zend_Pdf_Page $page)
		{
			/* Add table head */
			$this->_setFontRegular($page, 10);
			$page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
			$page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
			$page->setLineWidth(0.5);
			$page->drawRectangle(25, $this->y, 570, $this->y - 15);
			$this->y -= 10;
			$page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

			//columns headers
			$lines[0][] = ['text' => __('Products'), 'feed' => 35];

			$lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

			$lines[0][] = ['text' => __('Qty'), 'feed' => 435, 'align' => 'right'];

			$lines[0][] = ['text' => __('Price'), 'feed' => 373, 'align' => 'right'];

			$lines[0][] = ['text' => __('Tax'), 'feed' => 495, 'align' => 'right'];

			$lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

			$lineBlock = ['lines' => $lines, 'height' => 5];

			$this->drawLineBlocks($page, [$lineBlock], ['table_header' => false]);
			$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
			$this->y -= 20;
		}
	
		protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $page->drawText(__('Order # ') . $order->getRealOrderId(), 35, $top -= 30, 'UTF-8');
            $top +=15;
        }

        $top -=30;
        $page->drawText(
            __('Order Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress(
                $this->addressRenderer->format($order->getShippingAddress(), 'pdf')
            );
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
			$shippingMethodnew = str_replace("Shipping -", "-", $shippingMethod);
            $page->drawText(__('Ship to: ' . $order->getRealOrderId() . ' ' . $shippingMethodnew), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y - 25);
            $page->drawRectangle(275, $this->y, 570, $this->y - 25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method:'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');

            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "("
                . __('Total Shipping Charges')
                . " "
                . $order->formatPriceTxt($order->getShippingAmount())
                . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }
	
		public function getPdf($invoices = [])
		{
			$this->_beforeGetPdf();
			$this->_initRenderer('invoice');

			$pdf = new \Zend_Pdf();
			$this->_setPdf($pdf);
			$style = new \Zend_Pdf_Style();
			$this->_setFontBold($style, 10);

			foreach ($invoices as $invoice) {
				if ($invoice->getStoreId()) {
					$this->_localeResolver->emulate($invoice->getStoreId());
					$this->_storeManager->setCurrentStore($invoice->getStoreId());
				}
				$page = $this->newPage();
				$order = $invoice->getOrder();
				//$order->setShippingDescription('Shipping');
				/* Add image */
				$this->insertLogo($page, $invoice->getStore());
				/* Add address */
				$this->insertAddress($page, $invoice->getStore());
				/* Add head */
				$this->insertOrder(
					$page,
					$order,
					$this->_scopeConfig->isSetFlag(
						self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
						\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
						$order->getStoreId()
					)
				);
				/* Add document text and number */
				$this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
				/* Add table */
				$this->_drawHeader($page);
				/* Add body */
				foreach ($invoice->getAllItems() as $item) {
					if ($item->getOrderItem()->getParentItem()) {
						continue;
					}
					/* Draw item */
					$this->_drawItem($item, $page, $order);
					$page = end($pdf->pages);
				}
				
				
				
				//Custom Order Prx Data
				foreach ($order->getAllItems() as $item) {
					$itemData = $item->getData();
					$sku = $item->getSku();
					if(isset($itemData['product_options']['info_buyRequest']['prxdata'])) {
						$prxdata = $itemData['product_options']['info_buyRequest']['prxdata'];
						
						$rightsph = $rightcyl = $rightaxis = $rightadd = $leftsph = $leftcyl = $leftaxis = $leftadd = $pdcommon = '';
						if( isset($prxdata['send_via']) ) {
							if( isset($prxdata['send_via_title']) && $prxdata['send_via_title'] != '' ) {
								$send_by = $prxdata['send_via_title'];
							} else {
								$send_by = $prxdata['send_via'];
							}
							extract($prxdata);
							
							$this->_setFontRegular($page, 10);
							$page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
							$page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
							$page->setLineWidth(0.5);
							$page->drawRectangle(25, $this->y, 570, $this->y - 15);
							$this->y -= 10;
							$page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
							
							if( $prxdata['send_via'] == 'enter_online' ) {
							
								$lines[0][] = ['text' => 'Rx- ' . $sku, 'feed' => 35, 'font' => 'bold'];
								$lines[0][] = ['text' => 'Sphere', 'feed' => 135];
								$lines[0][] = ['text' => 'Cylinder', 'feed' => 170];
								$lines[0][] = ['text' => 'Axis', 'feed' => 205];
								$lines[0][] = ['text' => 'ADD', 'feed' => 240];
								
								$lines[1][] = ['text' => 'Right Eye(OD)', 'feed' => 35];
								$lines[1][] = ['text' => $rightsph, 'feed' => 135];
								$lines[1][] = ['text' => $rightcyl, 'feed' => 170];
								$lines[1][] = ['text' => $rightaxis, 'feed' => 205];
								$lines[1][] = ['text' => $rightadd, 'feed' => 240];
								
								$lines[2][] = ['text' => 'Left Eye(OS)', 'feed' => 35];
								$lines[2][] = ['text' => $leftsph, 'feed' => 135];
								$lines[2][] = ['text' => $leftcyl, 'feed' => 170];
								$lines[2][] = ['text' => $leftaxis, 'feed' => 205];
								$lines[2][] = ['text' => $leftadd, 'feed' => 240];
								
								$lines[3][] = ['text' => 'Pupil Distance:', 'feed' => 35];
								$lines[3][] = ['text' => $pdcommon, 'feed' => 135];
								
								$lineBlock2 = ['lines' => $lines, 'height' => 18];
								$this->drawLineBlocks($page, [$lineBlock2]);
								$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
$page = end($pdf->pages);
								unset($lines[0]);
								unset($lines[1]);
								unset($lines[2]);
								unset($lines[3]);
							} else {
								$lines[0][] = ['text' => 'Rx- ' . $sku, 'feed' => 35, 'font' => 'bold'];
								$lines[0][] = ['text' => __('Prescription Send by: ') . $send_by, 'feed' => 135];
								
								$lineBlock2 = ['lines' => $lines, 'height' => 20];
								$this->drawLineBlocks($page, [$lineBlock2], ['table_header' => true]);
								$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
$page = end($pdf->pages);
								unset($lines[0]);
							}
						}
					}
					
				}
				
				
				
				
				/* Add totals */
				$this->insertTotals($page, $invoice);
				if ($invoice->getStoreId()) {
					$this->_localeResolver->revert();
				}
			}
			$this->_afterGetPdf();
			return $pdf;
		}
		
		
	protected function insertTotals($page, $source) {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
					if( isset($totalData['label']) && $totalData['label'] == 'Grand Total:') {
						$totalData['label'] = 'Paid Grand Total:';
					}
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }
		
	
	public function _drawItem(
		\Magento\Framework\DataObject $item,
		\Zend_Pdf_Page $page,
		\Magento\Sales\Model\Order $order
	) {
		$type = $item->getOrderItem()->getProductType();
		$renderer = $this->_getRenderer($type);
		$renderer->setOrder($order);
		$renderer->setItem($item);
		$renderer->setPdf($this);
		$renderer->setPage($page);
		$renderer->setRenderedModel($this);

		$renderer->draw();

		return $renderer->getPage();
	}

}
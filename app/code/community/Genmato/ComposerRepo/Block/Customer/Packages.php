<?php

class Genmato_ComposerRepo_Block_Customer_Packages extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $customer = $this->getCustomer();

        $collection = Mage::getResourceModel('genmato_composerrepo/customer_packages_collection')
            ->addFieldToFilter('main_table.status', array('eq'=>1))
            ->addFieldToFilter('packages.status', array('eq'=>1))
            ->addFieldToFilter('main_table.customer_id', array('eq'=>$customer->getId()));

        $packageTable = Mage::getSingleton('core/resource')->getTableName('genmato_composerrepo/packages');
        $collection->getSelect()
            ->join(
                array('packages'=>$packageTable),
                'main_table.package_id=packages.entity_id',
                array('name','description','version')
                );
        $salesTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $collection->getSelect()
            ->join(
                array('sales'=>$salesTable),
                'main_table.order_id=sales.entity_id',
                array('increment_id')
            );
        $this->setPackages($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'composerrepo.keys.pager')
            ->setCollection($this->getPackages());
        $this->setChild('pager', $pager);
        $this->getPackages()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    protected function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
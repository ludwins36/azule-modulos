<?php
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_InventoryAbstract');
class ML_Idealo_Controller_Idealo_Listings_Inventory extends ML_Listings_Controller_Widget_Listings_InventoryAbstract {
    protected $aParameters=array('controller');
    
    public static function getTabTitle () {
        return MLI18n::gi()->get('ML_GENERIC_INVENTORY');
    }
    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
        
    public function __construct() {
        parent::__construct();
        $this->sCurrency = MLCurrency::gi()->getDefaultIso();
    }
    protected function getFields() { 
        $aFields = parent::getFields();
        unset($aFields['ItemID'], $aFields['DateAdded']);
        return $aFields;
    }
    
    public function render() {
        $this->includeView('widget_listings_inventory');
        return $this;
    }
        
}
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
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Shop_Model_ProductListDependency_CategoryFilter_Abstract');
class ML_Prestashop_Model_ProductListDependency_CategoryFilter extends ML_Shop_Model_ProductListDependency_CategoryFilter_Abstract {
    
    /**
     * key=>value for filtering (eg. validation and form-select)
     * @var array|null
     */
    protected $aFilterValues = null;
    
    /**
     * all categories
     * @var array|null
     */
    protected $aCategories = null;


    /**
     * @param ML_Database_Model_Query_Select $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = (int)$this->getFilterValue();
        if (
            !empty($sFilterValue) 
            && $sFilterValue !== 1 //root-category
            && array_key_exists($sFilterValue, $this->getFilterValues())
        ) {
            $aCats = $this->getPrestaCategories();
            $sWhere = ' cp.id_category='.$sFilterValue;// filter exact match for corrupted nested sets, to get min. selected category
            if(trim($aCats[$sFilterValue]['nleft']) != '' && trim($aCats[$sFilterValue]['nright']) != ''){
                $sWhere .= '
                    ||(
                        c.nleft >= '.$aCats[$sFilterValue]['nleft'].'
                        AND c.nright <= '.$aCats[$sFilterValue]['nright']. '
                    )
                    ';
            }
            $mQuery
                ->where($sWhere)
                ->join(array(
                    _DB_PREFIX_ . 'category_product' , 'cp' , 'p.`id_product` = cp.`id_product`'
                ), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
                ->join(array(//nested set
                    _DB_PREFIX_ . 'category' , 'c' , 'cp.`id_category` = c.`id_category`'
                ), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
            ;
        }
    }
    
    protected function getPrestaCategories () {
        if ($this->aCategories === null) {
            $this->aCategories = array();
            $aCatQuery = MLDatabase::getDbInstance()->fetchArray('
                SELECT 
                    category.id_category AS value, 
                    CONCAT(
                        REPEAT(
                            "'.str_repeat('&nbsp;', 5).'",
                            category.level_depth
                        ),
                        category_lang.name,
                        if (
                            shop.name is null, 
                            "", 
                            CONCAT(
                                " (",
                                GROUP_CONCAT(shop.name SEPARATOR " | "),
                                ")"
                            )
                        ) 
                    ) AS label,
                    category.nleft,
                    category.nright
                FROM
                    `'._DB_PREFIX_.'category` category
                INNER JOIN
                    '._DB_PREFIX_.'category_lang AS category_lang ON category.id_category = category_lang.id_category
                LEFT JOIN
                    '._DB_PREFIX_.'shop as shop on category.id_category = shop.id_category
                WHERE 
                    (category.active = 1 || category.id_category = 1)
                    AND (category_lang.id_lang = "'.((int) Context::getContext()->language->id).'")
                    AND (category_lang.id_shop = category.id_shop_default)
                GROUP BY 
                    nleft,
                    nright
                ORDER BY 
                    category.nleft,
                    category.position
            ');
            foreach ($aCatQuery as $aRow){
                $this->aCategories[$aRow['value']] = $aRow;
            }
        }
        return $this->aCategories;
    }

    /**
     * key=>value for categories
     * @return array
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $aCats = array();
            foreach ($this->getPrestaCategories() as $aCat) {
                $aCats[$aCat['value']] = array(
                    'value' => $aCat['value'],
                    'label' => $aCat['label'],
                );
            }
            $this->aFilterValues = $aCats;
        }
        return $this->aFilterValues;
    }
    
    /**
     * some wrong subcategory, that is not deleted correctly by Prestashop made problem for default category filter
     * so we always set Root category as default
     * @return string
     */
    public function getFilterValue() {
        $sValue = parent::getFilterValue();
        if($sValue === null){
            if(array_key_exists('1', $this->getFilterValues())){
                return '1';
            }
        }else{
            return $sValue;
        }
    }

}

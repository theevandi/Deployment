<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Market Market
 * @ingroup     UnaModules
 *
 * @{
 */

class BxMarketSearchResult extends BxBaseModTextSearchResult
{
    function __construct($sMode = '', $aParams = array())
    {
        parent::__construct($sMode, $aParams);

        $this->aCurrent = array(
            'name' => 'bx_market',
            'module_name' => 'bx_market',
            'object_metatags' => 'bx_market',
            'title' => _t('_bx_market_page_title_browse'),
            'table' => 'bx_market_products',
            'ownFields' => array('id', 'title', 'text', 'price_single', 'price_recurring', 'duration_recurring', 'thumb', 'cover', 'author', 'added'),
            'searchFields' => array(),
            'restriction' => array(
                'author' => array('value' => '', 'field' => 'author', 'operator' => '='),
        		'featured' => array('value' => '', 'field' => 'featured', 'operator' => '<>'),
        		'except' => array('value' => '', 'field' => 'id', 'operator' => 'not in'),
        		'status' => array('value' => 'active', 'field' => 'status', 'operator' => '='),
        		'statusAdmin' => array('value' => 'active', 'field' => 'status_admin', 'operator' => '='),
            ),
            'paginate' => array('perPage' => getParam('bx_market_per_page_browse'), 'start' => 0),
            'sorting' => 'last',
            'rss' => array(
                'title' => '',
                'link' => '',
                'image' => '',
                'profile' => 0,
                'fields' => array (
                    'Guid' => 'link',
                    'Link' => 'link',
                    'Title' => 'title',
                    'DateTimeUTS' => 'added',
                    'Desc' => 'text',
                ),
            ),
            'ident' => 'id',
        );

        $this->sFilterName = 'bx_market_filter';
        $this->oModule = $this->getMain();

        $CNF = &$this->oModule->_oConfig->CNF;

        $sSearchFields = getParam($CNF['PARAM_SEARCHABLE_FIELDS']);
        $this->aCurrent['searchFields'] = !empty($sSearchFields) ? explode(',', $sSearchFields) : '';

        $oProfileAuthor = null;

        switch ($sMode) {

            case 'author':
                if(!$this->_updateCurrentForAuthor($sMode, $aParams, $oProfileAuthor)) {
                    $this->isError = true;
                    break;
                }
                break;

            case 'favorite':
                if(!$this->_updateCurrentForFavorite($sMode, $aParams, $oProfileAuthor)) {
                    $this->isError = true;
                    break;
                }
                break;

            case 'public':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_LATEST']);
                $this->aCurrent['title'] = _t('_bx_market_page_title_browse_latest');
                $this->aCurrent['rss']['link'] = 'modules/?r=market/rss/' . $sMode;
                break;

            case 'featured':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_FEATURED']);
                $this->aCurrent['title'] = _t('_bx_market_page_title_browse_featured');
                $this->aCurrent['restriction']['featured']['value'] = '0';
                $this->aCurrent['rss']['link'] = 'modules/?r=market/rss/' . $sMode;
                $this->aCurrent['sorting'] = 'featured';
                break;

            case 'popular':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_POPULAR']);
                $this->aCurrent['title'] = _t('_bx_market_page_title_browse_popular');
                $this->aCurrent['rss']['link'] = 'modules/?r=market/rss/' . $sMode;
                $this->aCurrent['sorting'] = 'popular';
                break;

            case 'updated':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_UPDATED']);
                $this->aCurrent['title'] = _t('_bx_market_page_title_browse_updated');
                $this->aCurrent['rss']['link'] = 'modules/?r=market/rss/' . $sMode;
                $this->aCurrent['sorting'] = 'updated';
                break;

            case '': // search results
                $this->sBrowseUrl = BX_DOL_SEARCH_KEYWORD_PAGE;
                unset($this->aCurrent['paginate']['perPage'], $this->aCurrent['rss']);
                break;

            default:
                $sMode = '';
                $this->isError = true;
        }

        $this->processReplaceableMarkers($oProfileAuthor);

        $this->addConditionsForPrivateContent($CNF, $oProfileAuthor);
    }

    public function setMetaType($s)
    {
        $this->_sMetaType = $s;
        if(!empty($this->_sMetaType) && $this->_sMetaType == 'keyword')
            $this->aCurrent['title'] = _t('_bx_market_page_title_browse_keyword', bx_process_pass(bx_get('keyword')));
    }

    public function setCategoryObject($s)
    {
        $this->_sCategoryObject = $s;
        if(!empty($this->_sCategoryObject) && $o = BxDolCategory::getObjectInstance($this->_sCategoryObject))
            $this->aCurrent['title'] = _t('_bx_market_page_title_browse_category', $o->getCategoryTitle((int)bx_get('keyword')));
    }

    function displayResultBlock ()
    {
    	return BxDolPayments::getInstance()->getCartJs() . parent::displayResultBlock();
    }

    function getAlterOrder()
    {
        $aSql = array();
        switch ($this->aCurrent['sorting']) {
            case 'last':
                $aSql['order'] = ' ORDER BY `bx_market_products`.`added` DESC';
                break;
            case 'featured':
                $aSql['order'] = ' ORDER BY `bx_market_products`.`featured` DESC';
                break;
            case 'updated':
                $aSql['order'] = ' ORDER BY `bx_market_products`.`changed` DESC';
                break;
            case 'popular':
                $aSql['order'] = ' ORDER BY `bx_market_products`.`views` DESC';
                break;
        }
        return $aSql;
    }
}

/** @} */

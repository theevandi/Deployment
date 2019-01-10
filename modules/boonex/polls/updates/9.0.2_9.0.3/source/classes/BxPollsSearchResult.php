<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Polls Polls
 * @ingroup     UnaModules
 *
 * @{
 */

class BxPollsSearchResult extends BxBaseModTextSearchResult
{
    protected $sUnitViewDefault = 'extended';

    function __construct($sMode = '', $aParams = array())
    {
        parent::__construct($sMode, $aParams);

        $this->aCurrent = array(
            'name' => 'bx_polls',
            'module_name' => 'bx_polls',
            'object_metatags' => 'bx_polls',
            'title' => _t('_bx_polls_page_title_browse'),
            'table' => 'bx_polls_entries',
            'ownFields' => array('id', 'text', 'thumb', 'author', 'anonymous', 'hidden_results', 'comments', 'added'),
            'searchFields' => array(),
            'restriction' => array(
                'author' => array('value' => '', 'field' => 'author', 'operator' => '='),
        		'featured' => array('value' => '', 'field' => 'featured', 'operator' => '<>'),
        		'status' => array('value' => 'active', 'field' => 'status', 'operator' => '='),
        		'statusAdmin' => array('value' => 'active', 'field' => 'status_admin', 'operator' => '='),
            ),
            'paginate' => array('perPage' => getParam('bx_polls_per_page_browse'), 'start' => 0),
            'sorting' => 'last',
            'rss' => array(
                'title' => '',
                'link' => '',
                'image' => '',
                'profile' => 0,
                'fields' => array (
                    'Guid' => 'link',
                    'Link' => 'link',
                    'Title' => 'text',
                    'DateTimeUTS' => 'added',
                    'Desc' => '',
                ),
            ),
            'ident' => 'id',
        );

        $this->sFilterName = 'bx_polls_filter';
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
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_HOME']);
                $this->aCurrent['title'] = _t('_bx_polls_page_title_browse_recent');
                $this->aCurrent['rss']['link'] = 'modules/?r=polls/rss/' . $sMode;
                break;

            case 'featured':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_HOME']);
                $this->aCurrent['title'] = _t('_bx_polls_page_title_browse_featured');
                $this->aCurrent['restriction']['featured']['value'] = '0';
                $this->aCurrent['rss']['link'] = 'modules/?r=polls/rss/' . $sMode;
                $this->aCurrent['sorting'] = 'featured';
                break;

            case 'popular':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_POPULAR']);
                $this->aCurrent['title'] = _t('_bx_polls_page_title_browse_popular');
                $this->aCurrent['rss']['link'] = 'modules/?r=polls/rss/' . $sMode;
                $this->aCurrent['sorting'] = 'popular';
                break;

            case 'updated':
                $this->sBrowseUrl = BxDolPermalinks::getInstance()->permalink($CNF['URL_UPDATED']);
                $this->aCurrent['title'] = _t('_bx_polls_page_title_browse_updated');
                $this->aCurrent['rss']['link'] = 'modules/?r=polls/rss/' . $sMode;
                $this->aCurrent['sorting'] = 'updated';
                break;

            case '': // search results
                $this->sBrowseUrl = BX_DOL_SEARCH_KEYWORD_PAGE;
                $this->aCurrent['title'] = _t('_bx_polls');
                unset($this->aCurrent['paginate']['perPage'], $this->aCurrent['rss']);
                break;

            default:
                $sMode = '';
                $this->isError = true;
        }

        $this->processReplaceableMarkers($oProfileAuthor);

        $this->addConditionsForPrivateContent($CNF, $oProfileAuthor);
    }

    function addCustomParts ()
    {
        $this->oModule->_oTemplate->addJs(array('entry.js'));
        $this->oModule->_oTemplate->addCss(array('entry.css'));

        return $this->oModule->_oTemplate->getJsCode('entry');
    }
    
    
        
    function getAlterOrder()
    {
        $aSql = array();
        switch ($this->aCurrent['sorting']) {
            case 'last':
                $aSql['order'] = ' ORDER BY `bx_polls_entries`.`added` DESC';
                break;
            case 'featured':
                $aSql['order'] = ' ORDER BY `bx_polls_entries`.`featured` DESC';
                break;
            case 'updated':
                $aSql['order'] = ' ORDER BY `bx_polls_entries`.`changed` DESC';
                break;
            case 'popular':
                $aSql['order'] = ' ORDER BY `bx_polls_entries`.`views` DESC';
                break;
        }
        return $aSql;
    }
}

/** @} */

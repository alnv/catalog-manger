<?php

namespace CatalogManager;


class ContentCatalogEntity extends \ContentElement {


    protected $arrFields = [];
    protected $arrCatalog = [];
    protected $strTemplate = 'ce_catalog_entity';


    public function generate() {

        if ( TL_MODE == 'BE' ) {

            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper( $GLOBALS['TL_LANG']['CTE']['catalogCatalogEntity'][0] ) . ' ###';

            return $objTemplate->parse();
        }

        $this->catalogEntityId = $this->catalogEntityId ?: '0';

        if ( !$this->catalogTablename || !$this->catalogEntityId ) {

            return '';
        }

        if ( !$this->Database->tableExists( $this->catalogTablename ) ) {

            return '';
        }

        if ( $this->catalogEntityTemplate ) {

            $this->strTemplate = $this->catalogEntityTemplate;
        }
        
        return parent::generate();
    }


    protected function compile() {

        $objFieldBuilder = new CatalogFieldBuilder();
        $objFieldBuilder->initialize( $this->catalogTablename );

        $this->arrCatalog = $objFieldBuilder->getCatalog();
        $arrFields = $objFieldBuilder->getCatalogFields();

        foreach ( $arrFields as $strFieldname => $strValue ) {

            if ( !is_numeric( $strFieldname ) ) {

                $this->arrFields[ $strFieldname ] = $strValue;
            }
        }

        $arrQuery = [

            'table' => $this->catalogTablename,
            'where' => [
                [
                    'field' => 'id',
                    'operator' => 'equal',
                    'value' => $this->catalogEntityId
                ]
            ],
            'joins' => [],
            'pagination' => [

                'limit' => 1,
                'offset' => 0
            ]
        ];

        if ( is_array( $this->arrCatalog['operations'] ) && in_array( 'invisible', $this->arrCatalog['operations'] ) ) {

            $dteTime = \Date::floorToMinute();

            $arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => 0
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'start',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'start',
                    'operator' => 'lte',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'stop',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'stop',
                    'operator' => 'gt',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                'field' => 'invisible',
                'operator' => 'not',
                'value' => '1'
            ];
        }

        foreach ( $this->arrFields as $strFieldname => $arrField ) {

            if ( in_array( $arrField['type'], [ 'select', 'checkbox', 'radio' ] ) ) {

                if ( isset( $arrField['optionsType'] ) && in_array( $arrField['optionsType'], [ 'useDbOptions', 'useForeignKey' ] )  ) {

                    if ( !$arrField['multiple'] ) {

                        $arrQuery['joins'][] = [

                            'multiple' => false,
                            'type' => 'LEFT JOIN',
                            'field' => $strFieldname,
                            'table' => $this->catalogTablename,
                            'onTable' => $arrField['dbTable'],
                            'onField' => $arrField['dbTableKey']
                        ];

                        $objChildFieldBuilder = new CatalogFieldBuilder();
                        $objChildFieldBuilder->initialize( $arrField['dbTable'] );

                        $this->mergeFields( $objChildFieldBuilder->getCatalogFields( true, null ), $arrField['dbTable'] );
                    }
                }
            }
        }

        if ( $this->arrCatalog['pTable'] ) {

            $arrQuery['joins'][] = [

                'field' => 'pid',
                'onField' => 'id',
                'multiple' => false,
                'table' => $this->catalogTablename,
                'onTable' => $this->arrCatalog['pTable']
            ];

            $objParentFieldBuilder = new CatalogFieldBuilder();
            $objParentFieldBuilder->initialize( $this->arrCatalog['pTable'] );

            $this->mergeFields( $objFieldBuilder->getCatalogFields( true, null ), $this->arrCatalog['pTable'] );
        }

        $this->import( 'SQLQueryBuilder' );

        $objEntity = $this->SQLQueryBuilder->execute( $arrQuery );

        if ( !$objEntity->numRows ) {

            return null;
        }

        $arrEntity = $objEntity->row();
        $this->Template->fields = $this->getTemplateFields();

        foreach ( $arrEntity as $strFieldname => $strValue ) {

            if ( isset( $this->arrFields[ $strFieldname ] ) ) {

                $arrField = $this->arrFields[ $strFieldname ];

                if ( $arrField['multiple'] && in_array( $arrField['optionsType'], [ 'useDbOptions', 'useForeignKey' ] ) ) {

                    $this->Template->{$strFieldname} = $this->getJoinedEntities( $strValue, $arrField );

                    continue;
                }

                $this->Template->{$strFieldname} = Toolkit::parseCatalogValue( $strValue, $arrField, $arrEntity );
            }
        }

        if ( is_array( $this->arrCatalog['cTables'] ) && !empty( $this->arrCatalog['cTables'] ) ) {

            foreach ( $this->arrCatalog['cTables'] as $strChildTable ) {

                $this->Template->{$strChildTable} = $this->getChildrenEntities( $arrEntity['id'], $strChildTable );
            }
        }

        $strMasterUrl = '';

        if ( $this->catalogRedirectType ) {

            switch ( $this->catalogRedirectType ) {

                case 'internal':

                    $objPage = $this->getPage();

                    if ( $objPage !== null ) {

                        $strMasterUrl = $this->generateFrontendUrl( $objPage->row() );
                    }

                    $this->catalogRedirectTarget = '';

                    break;

                case 'master':

                    $objPage = $this->getPage();

                    if ( $objPage !== null ) {

                        $strMasterUrl = $this->generateFrontendUrl( $objPage->row(), ( $arrEntity['alias'] ? '/' . $arrEntity['alias'] : '' ) );
                    }

                    $this->catalogRedirectTarget = '';

                    break;

                case 'link':

                    $strMasterUrl = \Controller::replaceInsertTags( $this->catalogRedirectUrl );

                    break;
            }
        }

        $this->Template->masterUrl = $strMasterUrl;
        $this->Template->masterUrlText = $this->getLinkText();
        $this->Template->masterUrlTarget = $this->catalogRedirectTarget;
        $this->Template->masterUrlTitle = \Controller::replaceInsertTags( $this->catalogRedirectTitle );
    }


    protected function getPage() {

        $objPage = null;

        if ( !$this->catalogRedirectPage ) {

            return null;
        }

        return \PageModel::findByPK( $this->catalogRedirectPage );
    }


    protected function getLinkText() {

        $strText = \Controller::replaceInsertTags( $this->catalogRedirectText );

        if ( $strText ) {

            return $strText;
        }

        return $GLOBALS['TL_LANG']['MSC']['CATALOG_MANAGER']['detailLink'];
    }


    protected function mergeFields( $arrFields, $strTablename ) {

        foreach ( $arrFields as $strFieldname => $arrField ) {

            if ( is_numeric( $strFieldname ) ) {

                continue;
            }

            $this->arrFields[ $strTablename . ucfirst( $strFieldname ) ] = $arrField;
        }
    }


    protected function getTemplateFields() {

        $arrReturn = [];

        foreach ( $this->arrFields as $strFieldname => $arrField ) {

            $strLabel = $strFieldname;

            if ( is_array( $arrField['_dcFormat'] ) && isset( $arrField['_dcFormat']['label'] ) ) {

                $strLabel = $arrField['_dcFormat']['label'][0];
            }

            $arrReturn[ $strFieldname ] = $strLabel;
        }

        if ( is_array( $this->arrCatalog['cTables'] ) && !empty( $this->arrCatalog['cTables'] ) ) {

            foreach ( $this->arrCatalog['cTables'] as $strTable ) {

                $objFieldBuilder = new CatalogFieldBuilder();
                $objFieldBuilder->initialize( $strTable );
                $arrCatalog = $objFieldBuilder->getCatalog();
                $arrReturn[ $strTable ] = $arrCatalog['name'];
            }
        }

        return $arrReturn;
    }


    protected function getJoinedEntities( $strValue, $arrField ) {

        $arrReturn = [];
        $objFieldBuilder = new CatalogFieldBuilder();
        $objFieldBuilder->initialize( $arrField['dbTable'] );
        $arrFields = $objFieldBuilder->getCatalogFields( true, null );
        $arrOrderBy = Toolkit::parseStringToArray( $arrField['dbOrderBy'] );
        $arrCatalog = $objFieldBuilder->getCatalog();

        $arrQuery = [

            'table' => $arrField['dbTable'],
            'where' => [

                [
                    'operator' => 'findInSet',
                    'field' => $arrField['dbTableKey'],
                    'value' => explode( ',', $strValue )
                ]
            ],
            'orderBy' => []
        ];

        if ( is_array( $arrCatalog['operations'] ) && in_array( 'invisible', $arrCatalog['operations'] ) ) {

            $dteTime = \Date::floorToMinute();

            $arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => 0
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'start',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'start',
                    'operator' => 'lte',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'stop',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'stop',
                    'operator' => 'gt',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                'field' => 'invisible',
                'operator' => 'not',
                'value' => '1'
            ];
        }

        if ( is_array( $arrOrderBy ) && !empty( $arrOrderBy ) ) {

            foreach ( $arrOrderBy as $arrOrder ) {

                $arrQuery['orderBy'][] = [

                    'field' => $arrOrder['key'],
                    'order' => $arrOrder['value']
                ];
            }
        }

        $objEntities = $this->SQLQueryBuilder->execute( $arrQuery );

        if ( !$objEntities->numRows ) return $arrReturn;

        while ( $objEntities->next() ) {

            $arrReturn[] = Toolkit::parseCatalogValues( $objEntities->row(), $arrFields );
        }

        return $arrReturn;
    }


    protected function getChildrenEntities( $strValue, $strTable ) {

        $arrReturn = [];
        $objFieldBuilder = new CatalogFieldBuilder();
        $objFieldBuilder->initialize( $strTable );
        $arrFields = $objFieldBuilder->getCatalogFields( true, null );
        $arrCatalog = $objFieldBuilder->getCatalog();

        $arrQuery = [

            'table' => $strTable,
            'where' => [

                [
                    'field' => 'pid',
                    'operator' => 'equal',
                    'value' => $strValue
                ]
            ],
            'orderBy' => []
        ];

        if ( is_array( $arrCatalog['operations'] ) && in_array( 'invisible', $arrCatalog['operations'] ) ) {

            $dteTime = \Date::floorToMinute();

            $arrQuery['where'][] = [

                'field' => 'tstamp',
                'operator' => 'gt',
                'value' => 0
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'start',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'start',
                    'operator' => 'lte',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                [
                    'value' => '',
                    'field' => 'stop',
                    'operator' => 'equal'
                ],

                [
                    'field' => 'stop',
                    'operator' => 'gt',
                    'value' => $dteTime
                ]
            ];

            $arrQuery['where'][] = [

                'field' => 'invisible',
                'operator' => 'not',
                'value' => '1'
            ];
        }

        if ( !empty( $arrCatalog['sortingFields'] ) ) {

            $numFlag = (int) $arrCatalog['flag'] ?: 1;

            foreach ( $arrCatalog['sortingFields'] as $strSortingField ) {

                $arrQuery['orderBy'][] = [

                    'field' => $strSortingField,
                    'order' => ( $numFlag % 2 == 0 ) ? 'DESC' : 'ASC'
                ];
            }
        }

        $objEntities = $this->SQLQueryBuilder->execute( $arrQuery );

        if ( !$objEntities->numRows ) return $arrReturn;

        while ( $objEntities->next() ) {

            $arrReturn[] = Toolkit::parseCatalogValues( $objEntities->row(), $arrFields );
        }

        return $arrReturn;
    }
}
<?phpnamespace CatalogManager;class SQLQueryBuilder extends CatalogController{    protected $strQuery = '';    protected $strTable = '';    protected $arrQuery = [];    protected $arrValues = [];    protected $blnDistance = false;    protected $strJoinType = 'JOIN';    protected $arrMultipleValues = [];    public function __construct()    {        parent::__construct();        if (\Config::get('catalogJoinType')) {            $this->strJoinType = \Config::get('catalogJoinType');        }        $this->import('Database');    }    public function getQuery($arrQuery)    {        $this->arrValues = [];        $this->arrQuery = $arrQuery;        $this->strTable = $arrQuery['table'];        $this->createSelectQuery();        return $this->strQuery;    }    public function execute($arrQuery)    {        $this->getQuery($arrQuery);        if (isset($GLOBALS['TL_HOOKS']['catalogManagerOverwriteQuery']) && is_array($GLOBALS['TL_HOOKS']['catalogManagerOverwriteQuery'])) {            foreach ($GLOBALS['TL_HOOKS']['catalogManagerOverwriteQuery'] as $arrCallback) {                if (is_array($arrCallback)) {                    $this->import($arrCallback[0]);                    $this->strQuery = $this->{$arrCallback[0]}->{$arrCallback[1]}($arrQuery, $this->strQuery, $this->arrValues);                }            }        }        return $this->Database->prepare($this->strQuery)->execute($this->arrValues);    }    public function tableExist($strTable)    {        if (!$strTable || !$this->Database->tableExists($strTable)) {            return false;        }        return true;    }    public function getWhereQuery($arrQuery)    {        $this->arrValues = [];        $this->arrQuery = $arrQuery;        $this->strTable = $arrQuery['table'];        return $this->createWhereStatement();    }    public function getValues()    {        return $this->arrValues;    }    protected function createSelectQuery()    {        $this->strQuery = sprintf('SELECT %s FROM %s%s%s%s%s%s',            $this->createSelectionStatement(),            $this->strTable,            $this->createJoinStatement(),            $this->createWhereStatement(),            $this->createHavingDistanceStatement(),            $this->createOrderByStatement(),            $this->createPaginationStatement()        );    }    protected function equal($strField, $intLevel = 0)    {        return sprintf('%s.`%s` = ?', $this->strTable, $strField);    }    protected function not($strField, $intLevel = 0)    {        return sprintf('%s.`%s` != ?', $this->strTable, $strField);    }    protected function multiSelectRegexp($strField, $intLevel = 0)    {        return $this->regexp($strField, $intLevel);    }    protected function regexp($strField, $intLevel = 0)    {        return sprintf('LOWER(CAST(%s.`%s` AS CHAR)) REGEXP LOWER(?)', $this->strTable, $strField);    }    protected function regexpExact($strField, $intLevel = 0)    {        return $this->regexp($strField);    }    protected function multiSelectFindInSet($strField, $intLevel = 0)    {        return $this->findInSet($strField, $intLevel);    }    protected function findInSet($strField, $intLevel = 0)    {        return sprintf('FIND_IN_SET(?,LOWER(CAST(%s.`%s` AS CHAR)))', $this->strTable, $strField);    }    protected function findInSetExact($strField, $intLevel = 0)    {        return $this->findInSet($strField, $intLevel);    }    protected function gt($strField, $intLevel = 0)    {        return sprintf('LOWER(CAST(%s.`%s` AS SIGNED)) > ?', $this->strTable, $strField);    }    protected function gte($strField, $intLevel = 0)    {        return sprintf('LOWER(CAST(%s.`%s` AS SIGNED)) >= ?', $this->strTable, $strField);    }    protected function lt($strField, $intLevel = 0)    {        return sprintf('LOWER(CAST(%s.`%s` AS SIGNED)) < ?', $this->strTable, $strField);    }    protected function lte($strField, $intLevel = 0)    {        return sprintf('LOWER(CAST(%s.`%s` AS SIGNED)) <= ?', $this->strTable, $strField);    }    protected function contain($strField, $intLevel = 0)    {        $strPlaceholder = $this->arrMultipleValues[$strField . '::' . $intLevel] ? implode(',', array_fill(0, $this->arrMultipleValues[$strField . '::' . $intLevel], '?')) : '?';        return sprintf('LOWER(%s.`%s`) IN (' . $strPlaceholder . ')', $this->strTable, $strField);    }    protected function notContain($strField, $intLevel = 0)    {        $strPlaceholder = $this->arrMultipleValues[$strField . '::' . $intLevel] ? implode(',', array_fill(0, $this->arrMultipleValues[$strField . '::' . $intLevel], '?')) : '?';        return sprintf('LOWER(%s.`%s`) NOT IN (' . $strPlaceholder . ')', $this->strTable, $strField);    }    protected function containExact($strField, $intLevel = 0)    {        return $this->contain($strField, $intLevel);    }    protected function between($strField, $intLevel = 0)    {        return sprintf('LOWER(%s.`%s`) BETWEEN ? AND ?', $this->strTable, $strField);    }    protected function isEmpty($strField, $intLevel = 0)    {        return sprintf("(%s.%s IS NULL OR %s.%s = ?)", $this->strTable, $strField, $this->strTable, $strField);    }    protected function isNotEmpty($strField, $intLevel = 0)    {        return sprintf("%s.%s != ?", $this->strTable, $strField);    }    protected function createSelectionStatement()    {        $strSelectionStatement = '*';        if (!$this->arrQuery['joins'] || empty($this->arrQuery['joins']) || !is_array($this->arrQuery['joins'])) {            return $strSelectionStatement . $this->getDistanceField();        }        $arrCount = [];        $strSelectionStatement = sprintf('%s.*', $this->strTable);        foreach ($this->arrQuery['joins'] as $intIndex => $arrJoin) {            if (empty($arrJoin)) continue;            if (!$intIndex) $strSelectionStatement .= ',';            if (!isset($arrCount[$arrJoin['onTable']])) {                $arrCount[$arrJoin['onTable']] = 0;            }            $arrCount[$arrJoin['onTable']] += 1;            $arrColumnAliases = [];            $arrForeignColumns = $this->getForeignColumnsByTablename($arrJoin['onTable']);            foreach ($arrForeignColumns as $strForeignColumn) {                $arrColumnAliases[] = sprintf('%s'.($arrCount[$arrJoin['onTable']]>1?$arrCount[$arrJoin['onTable']] : '').'.`%s` AS %s', $arrJoin['onTable'], $strForeignColumn, $arrJoin['onTable'] .($arrCount[$arrJoin['onTable']]>1 ?$arrCount[$arrJoin['onTable']]:''). (ucfirst($strForeignColumn)));            }            $strSelectionStatement .= ($intIndex ? ',' : '') . implode(',', $arrColumnAliases);        }        return $strSelectionStatement . $this->getDistanceField();    }    protected function createHavingDistanceStatement()    {        if (!$this->arrQuery['distance'] || empty($this->arrQuery['distance']) || !is_array($this->arrQuery['distance'])) return '';        return sprintf(' HAVING _distance < %s', $this->arrQuery['distance']['value']);    }    protected function createJoinStatement()    {        $strJoinStatement = '';        if (!$this->arrQuery['joins'] || empty($this->arrQuery['joins']) || !is_array($this->arrQuery['joins'])) {            return $strJoinStatement;        }        $arrCount = [];        foreach ($this->arrQuery['joins'] as $intIndex => $arrJoin) {            $strType = $arrJoin['type'] ? $arrJoin['type'] : $this->strJoinType;            if (!isset($arrCount[$arrJoin['onTable']])) {                $arrCount[$arrJoin['onTable']] = 0;            }            $arrCount[$arrJoin['onTable']] += 1;            if (!$arrJoin['table'] || !$arrJoin['field'] || !$arrJoin['onTable'] || !$arrJoin['onField']) {                continue;            }            if ($arrJoin['multiple']) {                $strJoinStatement .= sprintf(($intIndex ? ' ' : '') . ' %s %s'.($arrCount[$arrJoin['onTable']]>1?' AS ' . $arrJoin['onTable'] . $arrCount[$arrJoin['onTable']] : '').' ON FIND_IN_SET(%s'.($arrCount[$arrJoin['onTable']]>1?$arrCount[$arrJoin['onTable']]:'').'.`%s`,%s.`%s`)', $strType, $arrJoin['onTable'], $arrJoin['onTable'], $arrJoin['onField'], $arrJoin['table'], $arrJoin['field']);            } else {                $strJoinStatement .= sprintf(($intIndex ? ' ' : '') . ' %s %s'.($arrCount[$arrJoin['onTable']]>1?' AS ' . $arrJoin['onTable'] . $arrCount[$arrJoin['onTable']] : '').' ON %s.`%s` = %s'.($arrCount[$arrJoin['onTable']]>1?$arrCount[$arrJoin['onTable']]:'').'.`%s`', $strType, $arrJoin['onTable'], $arrJoin['table'], $arrJoin['field'], $arrJoin['onTable'], $arrJoin['onField']);            }        }        return $strJoinStatement;    }    protected function createWhereStatement()    {        $strStatement = '';        if (!$this->arrQuery['where'] || empty($this->arrQuery['where']) || !is_array($this->arrQuery['where'])) {            return $strStatement;        }        $strStatement .= ' WHERE';        foreach ($this->arrQuery['where'] as $intLevel1 => $arrQuery) {            if (!Toolkit::isAssoc($arrQuery)) {                $intLevel2 = 0;                if ($intLevel1) $strStatement .= ' AND ';                if (!$intLevel2 && count($arrQuery) > 1) $strStatement .= ' ( ';                foreach ($arrQuery as $arrOrQuery) {                    if ($intLevel2) $strStatement .= strpos($arrOrQuery['operator'], 'Exact') !== false ? ' AND ' : ' OR ';                    $this->createMultipleValueQueries($strStatement, $arrOrQuery, $intLevel1);                    $intLevel2++;                }                if ($intLevel2 && $intLevel2 == count($arrQuery) && count($arrQuery) > 1) $strStatement .= ' ) ';            } else {                if ($intLevel1) $strStatement .= ' AND ';                $this->createMultipleValueQueries($strStatement, $arrQuery, $intLevel1);            }        }        return $strStatement;    }    protected function createMultipleValueQueries(&$strQuery, $arrQuery, $intLevel)    {        $this->setValue($arrQuery['value'], $arrQuery['field'], $intLevel, $arrQuery['operator']);        if (is_array($arrQuery['value']) && !empty($arrQuery['value']) && !in_array($arrQuery['operator'], ['between', 'contain', 'notContain'])) {            $strQuery .= ' ( ';            foreach ($arrQuery['value'] as $intIndex => $strValue) {                if ($intIndex) $strQuery .= strpos($arrQuery['operator'], 'Exact') !== false ? ' AND ' : ' OR ';                $strQuery .= ' ' . call_user_func_array(['SQLQueryBuilder', $arrQuery['operator']], [$arrQuery['field'], $intLevel]);            }            $strQuery .= ' ) ';        } else {            $strQuery .= ' ' . call_user_func_array(['SQLQueryBuilder', $arrQuery['operator']], [$arrQuery['field'], $intLevel]);        }    }    protected function createPaginationStatement()    {        if (!$this->arrQuery['pagination'] || empty($this->arrQuery['pagination']) || !is_array($this->arrQuery['pagination'])) {            return '';        }        $strOffset = $this->arrQuery['pagination']['offset'] ? intval($this->arrQuery['pagination']['offset']) : 0;        $strLimit = $this->arrQuery['pagination']['limit'] ? intval($this->arrQuery['pagination']['limit']) : 1000;        return sprintf(' LIMIT %s, %s', $strOffset, $strLimit);    }    protected function createOrderByStatement()    {        $arrOrderByStatements = [];        $arrAllowedModes = ['DESC', 'ASC'];        if (!$this->arrQuery['orderBy'] || empty($this->arrQuery['orderBy']) || !is_array($this->arrQuery['orderBy'])) {            return '';        }        foreach ($this->arrQuery['orderBy'] as $intIndex => $arrOrderBy) {            if (!$arrOrderBy['order']) $arrOrderBy['order'] = 'DESC';            if (!$arrOrderBy['field'] || !in_array($arrOrderBy['order'], $arrAllowedModes)) continue;            if ($arrOrderBy['field'] == '_distance' && !$this->blnDistance) {                continue;            }            $arrOrderByStatements[] = sprintf('%s`%s` %s', ($this->blnDistance ? '' : $this->strTable . '.'), $arrOrderBy['field'], $arrOrderBy['order']);        }        if (empty($arrOrderByStatements)) {            return '';        }        return ' ORDER BY ' . implode(',', $arrOrderByStatements);    }    private function setValue($varValue, $strFieldname = '', $intLevel = 0, $strOperator='')    {        if (is_array($varValue)) {            foreach ($varValue as $strValue) {                if (is_string($strValue) && $strValue != '') {                    $strValue = trim($strValue);                }                $this->arrValues[] = $this->replaceEvilChars($strValue, $strOperator);            }            $this->arrMultipleValues[$strFieldname . '::' . $intLevel] = count($varValue);        } else {            $this->arrValues[] = $this->replaceEvilChars($varValue, $strOperator);        }    }    protected function replaceEvilChars($strValue, $strOperator='') {        if (!is_string($strValue) || empty($strValue)) {            return $strValue;        }        if ((strpos($strValue, '(') !== false || strpos($strValue, ')') !== false) && $strOperator != 'equal') {            $strValue = str_replace($strValue, '(', '\(');            $strValue = str_replace($strValue, ')', '\)');        }        return $strValue;    }    private function getForeignColumnsByTablename($strTable)    {        if (!$strTable || !$this->Database->tableExists($strTable)) {            return [];        }        return Toolkit::parseColumns($this->Database->listFields($strTable));    }    private function getDistanceField()    {        if (!$this->arrQuery['distance'] || empty($this->arrQuery['distance']) || !is_array($this->arrQuery['distance'])) {            return '';        }        $this->blnDistance = true;        return sprintf(            ",3956 * 1.6 * 2 * ASIN(SQRT(POWER(SIN((%s-abs(%s.`%s`)) * pi()/180 / 2),2) + COS(%s * pi()/180) * COS(abs(%s.`%s`) *  pi()/180) * POWER( SIN( (%s-%s.`%s`) *  pi()/180 / 2 ), 2 ))) AS _distance",            $this->arrQuery['distance']['latCord'],            $this->strTable,            $this->arrQuery['distance']['latField'],            $this->arrQuery['distance']['latCord'],            $this->strTable,            $this->arrQuery['distance']['latField'],            $this->arrQuery['distance']['lngCord'],            $this->strTable,            $this->arrQuery['distance']['lngField']        );    }}
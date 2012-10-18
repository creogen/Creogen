<?php

class Creogen_DataMapper
{
    /**
     * @var string
     */
    protected $_domainObject = null;

    /**
     * @var string
     */
    protected $_dbTableClass = null;

    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable = null;

    /**
     * @var array of Creogen_DataMapper
     */
    protected static $_instances = array();

    /**
     * @var Zend_Cache
     */
    protected static $_cache = null;

    /**
     * @throws Exception
     * @param Zend_Db_Table_Abstract|string $dbTable
     * @return DataMapper
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
        }

        if (!$dbTable instanceof Zend_Db_Table_Abstract)
        {
            throw new Exception('Invalid table data gateway provided');
        }

        $this->_dbTable = $dbTable;

        return $this;
    }

    /**
	 * @return string Template of cache key for current class
	 */
	public function getCacheKey()
	{
        $config = Zend_Registry::get('config');
        $domain = str_replace('.', '_', $config->domain);
		return sprintf('%s_%s_%%d', $domain, $this->_domainObject);
	}

    /**
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable)
        {
            $this->setDbTable( $this->_dbTableClass );
        }

        return $this->_dbTable;
    }

    /**
     * @param int $id
     * @return Creogen_DomainObject
     */
    public function find($id, $useCache = true)
    {
        $cacheKey = sprintf($this->getCacheKey(), $id);

        if ($useCache && self::$_cache && self::$_cache->test( $cacheKey ))
        {
            $properties = self::$_cache->load( $cacheKey );
        }
        else
        {
            $data = $this->getDbTable()->find($id);

            if ( 0 == count($data) )
            {
                return null;
            }

            $row = $data->current();
            $properties = $row->toArray();

            if ($useCache && self::$_cache)
            {
                self::$_cache->save( $properties, $cacheKey );
            }

        }

        /**
         * @var Creogen_DomainObject $object
         */
        $object = new $this->_domainObject;

        $object->setPropertyArray( $properties, true );

        return $object;
    }

    /**
     * @param int $id
     * @return Creogen_DomainObject
     */
    public function findOrCreate($id)
    {
        if ($id)
        {
            $object = $this->find($id);

            if ($object)
            {
                return $object;
            }
        }

        return new $this->_domainObject;
    }

    public function findBy($fieldName, $value)
    {
        $select = $this->getDbTable()->select()
            ->from($this->getDbTable(), array('id'))
            ->where(sprintf('%s = ?', $fieldName), $value)
            ->limit(1);

        $row = $this->getDbTable()->fetchRow($select);

        if (!$row) return null;

        $object = $this->find($row['id']);

        return $object;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $select = $this->getDbTable()->select()
            ->from($this->getDbTable(), array('id'));


        $resultSet = $this->getDbTable()->fetchAll($select);

        $objects = array();

        foreach ($resultSet as $row)
        {
            /**
             * @var Creogen_DomainObject $Object
             */

            $objects[] = $this->find($row->id);
        }

        return $objects;
    }

    /**
     * @param Creogen_DomainObject $object
     * @return void
     */
    public function save(Creogen_DomainObject $object)
    {
        if (null === ($id = $object->getId()))
        {
            $data = $object->getPropertyArray();
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $object->setId($id);
        }
        else
        {
            $data = $object->getModifiedProperties();
            if (count($data))
            {
                $this->getDbTable()->update($data, array('id = ?' => $id));
            }
        }

        if (self::$_cache)
        {
        	$cacheKey = sprintf($this->getCacheKey(), $object->getId());
        	self::$_cache->remove($cacheKey);
        }
    }

    /**
     * @param Creogen_DomainObject $object
     * @return bool|int
     */
    public function drop(Creogen_DomainObject $object)
    {
        if (!$id = $object->getId())
        {
            return false;
        }

        if (self::$_cache)
        {
            $cacheKey = sprintf($this->getCacheKey(), $id);
            self::$_cache->remove($cacheKey);
        }

        return $this->getDbTable()->delete(array('id = ?' => $id));
    }

    /**
     * @return Creogen_DataMapper
     */
    public static function getInstance($className)
    {
        if (empty(self::$_instances[$className]))
        {
            self::$_instances[$className] = new $className();
        }

        return self::$_instances[$className];
    }
    
    public static function setCache($cache)
    {
        self::$_cache = $cache;
    }

    public function getFieldNames()
    {
        $tableInfo = $this->getDbTable()->info();
        return $tableInfo['cols'];
    }
}
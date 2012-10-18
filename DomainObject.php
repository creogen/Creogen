<?php

class Creogen_DomainObject
{
    /**
     * @var array
     */
    protected $_params      = array();

    /**
     * @var array
     */
    protected $_modified    = array();

    /**
     * @return null|int
     */
    public function getId()
    {
        return isset($this->_params['id']) ? $this->_params['id'] : null;
    }

    public function setId($value)
    {
        $this->_params['id'] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $init Mark field as modified if false
     * @return Creogen_DomainObject
     */
    public function setProperty($name, $value, $init = false)
    {
        if (!$init && (!isset($this->_params[ $name ]) || $this->_params[ $name ] != $value))
        {
            $this->_modified[] = $name;
        }

        $this->_params[ $name ] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setProperty($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getProperty($name, $htmlspecialchars = false)
    {
        $val = isset($this->_params[ $name ]) ? $this->_params[ $name ] : null;
        return $htmlspecialchars ? htmlspecialchars($val, ENT_QUOTES) : $val;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * @return array
     */
    public function getPropertyArray()
    {
        return $this->_params;
    }

    /**
     * @return array
     */
    public function getModifiedProperties()
    {
        $return = array();

        foreach ($this->_modified as $name)
        {
            $return[ $name ] = $this->getProperty( $name );
        }

        return $return;
    }

    /**
     * @param array $data
     * @param bool $init Mark fields as modified if false
     * @return Creogen_DomainObject
     */
    public function setPropertyArray(array $data, $init = false)
    {
        foreach ($data as $name => $value)
        {
            $this->setProperty($name, $value, $init);
        }

        return $this;
    }
}
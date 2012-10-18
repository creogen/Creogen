<?php

class Creogen_Paginator
{
    protected $mapper = null;

    protected $paginator = null;

    protected $results = null;


    public function __construct($data, $mapper)
    {
        $this->paginator = Zend_Paginator::factory($data);
        $this->setMapper($mapper);
    }

    public function setMapper($mapper)
    {
        $this->mapper = $mapper;
    }

    public function getCachedResults()
    {
        if (!is_null($this->results))
        {
            return $this->results;
        }

        if (!count($this))
        {
            $this->results = array();
            return $this->results;
        }

        $ret = array();
        foreach ($this->paginator as $row)
        {
            $ret[] = $this->mapper->find($row['id']);
        }
        $this->results = $ret;

        return $this->results;
    }

    /**
     * @return Zend_Paginator_Adapter_Interface
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}
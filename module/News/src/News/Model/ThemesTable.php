<?php
namespace News\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class ThemesTable extends AbstractTableGateway
{
    protected $table ='avp_themes';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Themes());

        $this->initialize();
    }

    public function getListThemes() // Получаем список тем
    {
        $resultSet = $this->select();
        return $resultSet;
    }
    public function getArrayListThemes() { // Получаем список тем в виде массива
        $array = array();
        foreach ($this->getListThemes() as $row) {
            $array[$row->theme_id] = $row->theme_title;
        }
        return $array;
    }
}
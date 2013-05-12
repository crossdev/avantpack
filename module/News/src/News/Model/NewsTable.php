<?php
namespace News\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

class NewsTable extends AbstractTableGateway
{
    protected $table ='avp_news';

    private $newsPerPage = 5;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new News());

        $this->initialize();
    }

    public function setPager($request = array()) {
        $arFilter = array();
        if (!isset($request['page'])) {
            $request['page'] = 1;
        }

        if (!empty($request)) {
            if (isset($request['theme'])) { // Тема
                $arFilter[] = 'theme_id='.htmlspecialchars($request['theme']);
            }
            if (isset($request['year'])) {
                $arFilter[] = 'YEAR(date)='.htmlspecialchars($request['year']);
            }
            if (isset($request['month'])) {
                $arFilter[] = 'MONTH(date)='.htmlspecialchars($request['month']);
            }
        }
        $sql = new Sql($this->adapter);

        $select = $sql->select();
        $select->from($this->table);
        $select->where($arFilter);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        $numNews = $resultSet->initialize($result)->count();

        $arPager = array();
        if ($numNews > $this->newsPerPage) {
            if ($numNews % $this->newsPerPage >0) {
                $numPage = intval($numNews / $this->newsPerPage)+1;
            } else {
                $numPage = intval($numNews / $this->newsPerPage);
            }
            for ($i=1; $i<=$numPage; $i++) {
                $arPager[$i] = $request;
                $arPager[$i]['page'] = $i;
                if ($i == $request['page']) {
                    $arPager[$i]['active'] = 'Y';
                }
            }
        }
        return $arPager;
    }

    public function getListNews($request = array()) // Получаем список новостей
    {
        $limit = $this->newsPerPage;
        $arFilter = array();
        // Формируем фильтр
        if (!empty($request)) {
            if (isset($request['theme'])) { // Тема
                $arFilter[] = 'theme_id='.htmlspecialchars($request['theme']);
            }
            if (isset($request['year'])) {
                $arFilter[] = 'YEAR(date)='.htmlspecialchars($request['year']);
            }
            if (isset($request['month'])) {
                $arFilter[] = 'MONTH(date)='.htmlspecialchars($request['month']);
            }
            $offset = 0;
            if (isset($request['page'])) {
                $offset = (int) ($request['page']-1)*$limit;
            }

        }
        $sql = new Sql($this->adapter);

        $select = $sql->select();
        $select->from($this->table);
        $select->where($arFilter);
        //$select->where(array('theme_id' => 3));
        $select->order('date DESC');
        $select->limit($limit);
        $select->offset($offset);


        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        return $resultSet->initialize($result);
    }

    public function getTreeArchive() {
        $adapter = $this->adapter;
        $sql = '
            select YEAR(date) y, MONTH(date) m, count(news_id) c
            from '.$this->table.
            ' group by y,m
            order by y DESC, m DESC
        ';
        $statement = $adapter->query($sql);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        // Преобразуем
        $return = array();
        foreach ($resultSet->initialize($result)->toArray() as $k=>$v) {
            $return[$v['y']][$v['m']] = $v['c'];
        }
        return $return;
    }

    public function getArrayThemes() {
        $adapter = $this->adapter;
        $sql = '
            select t.theme_id, theme_title, count(news_id) c
            from avp_news n, avp_themes t
            where n.theme_id = t.theme_id
            group by  t.theme_id;
        ';
        $statement = $adapter->query($sql);
        $result = $statement->execute();
        $resultSet = new ResultSet();
        return $resultSet->initialize($result)->toArray();
    }

    public function getOneNews($news_id) // Получаем новость по news_id
    {
        $news_id  = (int) $news_id;

        $rowset = $this->select(array(
            'news_id' => $news_id,
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $news_id");
        }

        return $row;
    }

    public function saveNews(News $news) // Добавляем либо редактируем новость
    {
        $data = array(
            'title'  => $news->title,
            'date' => $news->date,
            'text' => $news->text,
            'theme_id' => $news->theme_id
        );

        $news_id = (int) $news->news_id;

        if ($news_id == 0) {
            $this->insert($data);
        } elseif ($this->getOneNews($news_id)) {
            $this->update(
                $data,
                array(
                    'news_id' => $news_id,
                )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteNews($news_id) // Удаляем новость по id
    {
        $this->delete(array(
            'news_id' => $news_id,
        ));
    }
}
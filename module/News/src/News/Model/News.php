<?php
namespace News\Model;

class News
{
    public $news_id;
    public $title;
    public $date;
    public $text;
    public $theme_id;

    public function exchangeArray($data)
    {
        $this->news_id     = (isset($data['news_id'])) ? $data['news_id'] : null;
        $this->title  = (isset($data['title'])) ? $data['title'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : null;
        $this->text = (isset($data['text'])) ? $data['text'] : null;
        $this->theme_id = (isset($data['theme_id'])) ? $data['theme_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
<?php
namespace News\Model;

class Themes
{
    public $theme_id;
    public $theme_title;

    public function exchangeArray($data)
    {
        $this->theme_id     = (isset($data['theme_id'])) ? $data['theme_id'] : null;
        $this->theme_title  = (isset($data['theme_title'])) ? $data['theme_title'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
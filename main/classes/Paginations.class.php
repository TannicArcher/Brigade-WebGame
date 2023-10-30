<?php

namespace Game;

class Paginations
{
    private $_perPage;
    private $_instance;
    public $_page;
    private $_limit;
    private $_totalRows = 0;

    public function __construct($perPage, $instance)
    {
        $this->_instance = $instance;
        $this->_perPage = $perPage;
        $this->setInstance();
    }

    public function start()
    {
        return ($this->_page * $this->_perPage) - $this->_perPage;
    }

    private function setInstance()
    {
        $this->_page = (int)(!isset($_GET[$this->_instance]) ? 1 : $_GET[$this->_instance]);
        $this->_page = ($this->_page == 0 ? 1 : $this->_page);
    }

    public function setTotal($_totalRows)
    {
        $this->_totalRows = $_totalRows;
    }

    public function getLast($ext = 0)
    {
        $rows = $this->_totalRows + $ext;
        $lastpage = ceil($rows / $this->_perPage);
        return $lastpage;
    }

    public function getLimit($order = 'id', $nav = "DESC")
    {
        return "ORDER BY {$order} {$nav} LIMIT " . $this->start() . ",$this->_perPage";
    }

    public function getLimitMany($order = null)
    {
        return "ORDER BY {$order} LIMIT " . $this->start() . ",$this->_perPage";
    }

    public function render($path = '?', $ext = null)
    {
        $adjacents = "2";
        $lastpage = ceil($this->_totalRows / $this->_perPage);
        $lpm1 = $lastpage - 1;
        if ($lastpage > 1) $pagination = "<div class='access-4 m-5'>Страницы</div><div class='nav'>";
        else $pagination = "<div class='nav'>";
        if ($lastpage > 1) {
            if ($lastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $this->_page)
                        $pagination .= "<a class='nav-block-out'>$counter</a>";
                    else
                        $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$counter" . "$ext'>$counter</a>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2)) {
                if ($this->_page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $this->_page)
                            $pagination .= "<a class='nav-block-out'>$counter</a>";
                        else
                            $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$counter" . "$ext'>$counter</a>";
                    }
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$lpm1" . "$ext'>$lpm1</a>";
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$lastpage" . "$ext'>$lastpage</a>";
                } elseif ($lastpage - ($adjacents * 2) > $this->_page && $this->_page > ($adjacents * 2)) {
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=1" . "$ext'>1</a>";
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=2" . "$ext'>2</a>";
                    for ($counter = $this->_page - $adjacents; $counter <= $this->_page + $adjacents; $counter++) {
                        if ($counter == $this->_page)
                            $pagination .= "<a class='nav-block-out'>$counter</a>";
                        else
                            $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$counter" . "$ext'>$counter</a>";
                    }
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$lpm1" . "$ext'>$lpm1</a>";
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$lastpage" . "$ext'>$lastpage</a>";
                } else {
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=1" . "$ext'>1</a>";
                    $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=2" . "$ext'>2</a>";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $this->_page)
                            $pagination .= "<a class='nav-block-out'>$counter</a>";
                        else
                            $pagination .= "<a class='nav-block' href='" . $path . "$this->_instance=$counter" . "$ext'>$counter</a>";
                    }
                }
            }
        }
        if ($this->_page != 0) $pagination .= "</div>";
        return $pagination;
    }
}
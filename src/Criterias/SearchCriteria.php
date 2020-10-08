<?php


namespace Josebasmtz\SystemCore\Criterias;


use Illuminate\Database\Query\Builder;
use Josebasmtz\SystemCore\Interfaces\ICriteria;

class SearchCriteria implements ICriteria
{
    /**
     * @var array
     */
    protected $keywords = [];

    /**
     * SearchCriteria constructor.
     * @param array|string $keywords
     */
    public function __construct($keywords)
    {
        if (is_string($keywords)) {
            $this->keywords = explode(' ', $keywords);
        }
        elseif (is_array($keywords)) {
            $this->keywords = $keywords;
        }

        $_keywords = [];

        foreach ($this->keywords as $keyword) {
            $valid = is_string($keyword) && str_replace(' ', '', $keyword) !== '';
            if (!$valid)
            {
                continue;
            }
            $_keywords[] = trim(preg_replace('/ +/', ' ', $keyword));
        }

        $this->keywords = $_keywords;
    }

    public function apply($model)
    {
        $model = $model->search($this->keywords);
        return $model;
    }
}
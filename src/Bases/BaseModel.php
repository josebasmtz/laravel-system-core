<?php


namespace Josebasmtz\SystemCore\Bases;


use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected $appends = [
        'text'
    ];

    protected $searchable = [];

    public function scopeSearch($query, array $items)
    {
        $query->where(function ($query) use ($items){
            foreach ($this->searchable as $column){
                foreach ($items as $item)
                {
                    $query->orWhere($column, 'like', "%$item%");
                }
            }
        });
        return $query;
    }

    public function getTextAttribute()
    {
        return static::class;
    }
}
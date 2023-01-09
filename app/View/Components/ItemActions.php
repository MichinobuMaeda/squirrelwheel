<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ItemActions extends Component
{
    /**
     * The name of route.
     *
     * @var string
     */
    public $route;

    /**
     * The name of item.
     *
     * @var string
     */
    public $item;

    /**
     * The target model of actions.
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Create a new component instance.
     *
     * @param  string  $route
     * @param  string  $item
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function __construct($route, $item, $model)
    {
        $this->route = $route;
        $this->item = $item;
        $this->model = $model;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.item-actions');
    }
}

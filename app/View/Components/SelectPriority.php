<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectPriority extends Component
{
    /**
     * The selected value.
     *
     * @var int
     */
    public $value;

    /**
     * The name of the form parameter.
     *
     * @var string
     */
    public $name;

    /**
     * The class of select element.
     *
     * @var string
     */
    public $class;

    /**
     * Create a new component instance.
     *
     * @param  int  $value
     * @param  string  $name
     * @param  string  $class
     * @return void
     */
    public function __construct($value, $name = 'priority', $class = null)
    {
        $this->value = $value;
        $this->name = $name;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.select-priority');
    }
}

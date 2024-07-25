<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Modelo;

class modelos extends Component
{
    public $modelo;
    /**
     * Create a new component instance.
     */
    public function __construct($modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modelos',[
            "modelos" => Modelo::all()
        ]);
    }
}

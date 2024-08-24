<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class cadastroEvento extends Component
{
    public $Registro;
    /**
     * Create a new component instance.
     */
    public function __construct($Registro)
    {
        $this->Registro = $Registro;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cadastro-evento');
    }
}

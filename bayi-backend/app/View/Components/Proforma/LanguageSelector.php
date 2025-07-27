<?php

namespace App\View\Components\Proforma;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LanguageSelector extends Component
{
    public $type;
    public $id;
    public $user_id;
    public $pass;
    /**
     * Create a new component instance.
     */
    public function __construct($type, $id, $userid = null)
    {
        $this->id = $id;
        if ($type == 'approval') {
            $this->type = 'approval';
        } else {
            $this->type = 'preview';
        }

        if ($userid) {
            $this->user_id = $userid;
        }
        //check pass query string
        $pass = request()->query('pass');
        if ($pass) {
            $this->pass = $pass;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.proforma.language-selector');
    }
}

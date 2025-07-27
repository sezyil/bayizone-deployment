<?php

namespace App\View\Components\Mail;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $uri;
    public string $text;
    /**
     * Create a new component instance.
     */
    public function __construct($uri, $text)
    {
        $this->uri = $uri;
        $this->text = $text;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.mail.button', [
            'uri' => $this->uri,
            'text' => $this->text,
        ]);
    }
}

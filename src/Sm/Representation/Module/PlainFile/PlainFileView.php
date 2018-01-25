<?php


namespace Sm\Representation\Module\PlainFile;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Representation\View\View;

class PlainFileView extends View {
    protected $item;
    public static function init() {
        return new static;
    }
    public function setItem(string $item) {
        if (!is_file($item)) throw new InvalidArgumentException("Can only render files");
        $this->item = $item;
        return $this;
    }
    
    public function render($context = null): string {
        return file_get_contents($this->item);
    }
    
}
<?php


namespace Sm\Representation\View\Proxy;


use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Representation\Representation;
use Sm\Representation\View\View;

/**
 * Class ViewProxy
 *
 * Proxy for Views
 */
class ViewProxy extends StandardContextualizedProxy implements Representation {
    public static function init(View $subject) {
        return new static($subject);
    }
    public function render(): ?string {
        return $this->subject->render($this->context);
    }
}
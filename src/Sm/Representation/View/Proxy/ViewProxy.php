<?php


namespace Sm\Representation\View\Proxy;


use Sm\Core\Context\Proxy\StandardContextualizedProxy;
use Sm\Representation\Context\RepresentationContext;
use Sm\Representation\Representation;
use Sm\Representation\View\View;

/**
 * Class ViewProxy
 *
 * Proxy for Views
 */
class ViewProxy extends StandardContextualizedProxy implements Representation {
    public static function init(View $subject, RepresentationContext $context = null) {
        return new static($subject, $context);
    }
    public function render(): ?string {
        return $this->subject->render($this->context);
    }
}
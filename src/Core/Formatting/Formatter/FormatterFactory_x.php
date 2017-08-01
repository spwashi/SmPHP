<?php
///**
// * User: Sam Washington
// * Date: 3/5/17
// * Time: 3:40 PM
// */
//
//namespace Sm\Core\Formatting\Formatter;
//
//
//use Sm\Core\Container\Container;
//use Sm\Core\Factory\AbstractFactory;
//use Sm\Core\Internal\Identification\Identifiable;
//use Sm\Core\Resolvable\FunctionResolvable;
//use Sm\Core\Resolvable\StringResolvable;
//use Sm\Core\Util;
//
///**
// * Class FormFact
// *
// * @
// *
// * @property null|\Sm\Core\Container\Container Aliases
// * @package   Sm\Core\Formatter
// */
//class FormFact extends AbstractFactory {
//    protected $Aliases;
//    /** @var  Container $Rules For cases when we are going to provide some temporary Formatting modifications to something, this is the container for that */
//    protected $Rules;
//    /** @var  array $registered_rule_names An array of the names of Rules that are being applied */
//    protected $registered_rule_names = [];
//    protected $used_rule_names       = [];
//    protected $rules_cache_key;
//
//    /**
//     * FormFact constructor.
//     */
//    public function __construct() {
//        $this->Aliases = new Container;
//        $this->Rules   = (new Container);
//        parent::__construct();
//    }
//    public function __get($name) {
//        if ($name === 'Aliases') {
//            return $this->Aliases;
//        }
//        return parent::__get($name);
//    }
//
//    /**
//     * @param string                             $name
//     * @param string|FunctionResolvable|callable $resolvable
//     *
//     * @return $this
//     */
//    public function addRule(string $name, $resolvable = null) {
//        if (!in_array($name, $this->registered_rule_names)) $this->registered_rule_names[] = $name;
//        if (!isset($resolvable)) return $this;
//        if (!($resolvable instanceof FunctionResolvable) && !($resolvable instanceof StringResolvable)) {
//            $resolvable = is_callable($resolvable) ? FunctionResolvable::init($resolvable) : StringResolvable::init($resolvable);
//        }
//        $this->Rules->register($name, $resolvable);
//        return $this;
//    }
//    /**
//     * Remove a formatting rule
//     *
//     * @param string $name
//     *
//     * @return $this
//     */
//    public function removeRule(string $name) {
//        $index = array_search($name, $this->registered_rule_names);
//        if ($index < 0) {
//            return $this;
//        } else {
//            array_splice($this->registered_rule_names, $index);
//            return $this;
//        }
//    }
//    /**
//     * Make it so that we know we can use the previously "consumed" rules.
//     *
//     * @return $this
//     */
//    public function restoreRules() {
//        $this->used_rule_names = [];
//        return $this;
//    }
//    public function canCreateClass($object_type) {
//        return is_a($object_type, Formatter::class, true);
//    }
//    /**
//     * @param null $item
//     *
//     * @return null
//     */
//    public function build($item = null) {
//        $result = parent::build($item, $this);
//        return PlainStringFormatter::init($result);
//    }
//    public function reset() {
//        $this->endRulesCache(true);
//    }
//    public function format($item_to_format) {
//        $rule_cache_key = $this->startRulesCache();
//        $item           = $this->applyRules($item_to_format, true);
//
//        # Format arrays individually
//        if (is_array($item)) {
//            $formatted_item = [];
//            foreach ($item as $index => $value) {
//                if (!is_numeric($index)) $index = $this->format(StringResolvable::init($index));
//                $formatted_item[ $index ] = $this->format($value);
//            }
//            return $formatted_item;
//        }
//
//        # Build the item like a factory
//        return $this->build($item);
//    }
//    /**
//     * For anything we want to apply Rules to, apply them and return the result.
//     * These are usually formatting quirks, like every time we see a Property, we might want to return a PropertyAsColumnFragment, etc.
//     *
//     * @param      $result
//     *
//     * @param bool $is_item Are we applying the rules on this item
//     *
//     * @return mixed
//     */
//    protected function applyRules($result, $is_item = false) {
//        $original = $result;
//
//        /** @var array $checked_out_array An array of the Rules that we've checked out to apply */
//        $checked_out_array = [];
//
//        # Apply any formatting rules
//        foreach ($this->registered_rule_names as $index => $name) {
//            $Resolver            = $this->Rules->checkout($name);
//            $formatted           = $Resolver->resolve($result, $is_item);
//            $checked_out_array[] = $Resolver;
//
//            # If the rule didn't get applied, add it back in because there probably isn't a risk of recursion
//            if (!isset($formatted)) {
//                $this->Rules->checkBackIn($Resolver);
//                continue;
//            }
//
//            $result = $formatted;
//        }
//
//        if ($original === $result) return $original;
//
//        $result = $this->format($result);
//
//        $checked_out_array = array_filter($checked_out_array);
//        foreach ($checked_out_array as $item) {
//            $success = $this->Rules->checkBackIn($item);
//            if (!$success) var_dump('cannot check back in');
//        }
//
//        return $result;
//    }
//    /**
//     * Get the name of what a Fragment should be in the Fragment registry
//     *
//     * @param \Sm\Core\Internal\Identification\Identifiable $item
//     * @param                                         $fragment_type
//     *
//     * @return string
//     */
//    protected function getFragmentName(Identifiable $item, $fragment_type) {
//        return "{$fragment_type}|{$item->getObjectId()}";
//    }
//    protected static function resultIsComplete($item) {
//        return is_array($item) ? $item : $item instanceof Formatter;
//    }
//    private function startRulesCache() {
//        $cache_key             = Util::generateRandomString(4);
//        $this->rules_cache_key = $this->rules_cache_key ?? $cache_key;
//        $this->Rules->Cache->start($cache_key);
//        return $cache_key;
//    }
//    /**
//     * @param bool $final Whether or not we want the Cache to permanently end
//     * @param null $key
//     *
//     * @return bool true on success
//     */
//    private function endRulesCache($final = false, $key = null) {
//        $this->Rules->Cache->end($final ? $this->rules_cache_key : $key);
//        $success = !$this->Rules->Cache->isCaching();
//
//        if (!$success) return false;
//
//        $this->Rules->resetConsumedItems();
//        return $success;
//    }
//}
<?php
/**
 * User: Sam Washington
 * Date: 2/2/17
 * Time: 5:38 PM
 */

namespace Sm\Core;


class Util {
    const ALPHA     = 'abcdefghijjlmnopqrstuvwkyz';
    const ALPHA_ALL = 'abcdefghijjlmnopqrstuvwkyzABCDEFGHIJJLMNOPQRSTUVWKYZ';
    
    /**
     * Return a string representing the general structure of the item as a pipe-delimited string
     *
     * @param $result
     *
     * @return string
     */
    public static function getShapeOfItem($result) {
        $arguments = func_get_args();
        if (count($arguments) > 1) {
            $string = [];
            foreach ($arguments as $item) {
                $string [] = static::getShapeOfItem($item);
            }
            return implode('|', $string);
        } else if (is_array($result)) {
            $string = 'array[';
            foreach ($result as $item) {
                $string .= static::getShapeOfItem($item) . '|';
            }
        
            $string = trim($string, '|');
            $string .= ']';
            return $string;
        }
        return is_object($result) ? get_class($result) : gettype($result);
    }
    /**
     * Is there a good way for us to convert this into a string?
     *
     * @param $var
     *
     * @return bool
     */
    public static function canBeString($var) {
        return $var === null || is_scalar($var) || is_callable([ $var, '__toString' ]);
    }
    /**
     * Get a string with just the characters of the alphabet
     *
     * @param bool $both_cases
     *
     * @return string
     */
    public static function getAlphaCharacters($both_cases = true) {
        $alpha = 'abcdefghijjlmnopqrstuvwkyz';
        
        return $both_cases ? ($alpha . strtoupper($alpha)) : $alpha;
    }
    /**
     * Generate a pseudo random string that is of the length provided using only the characters that are provided
     *
     * @param int    $length            The number of characters to make the string
     * @param string $characters_to_use The characters that are allowed in the string
     *
     * @return string THe random string
     */
    public static function generateRandomString($length, $characters_to_use = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-') {
        $str   = '';
        $count = strlen($characters_to_use);
        while ($length--) {
            /** Take the character string, pick out a random index between the start and the end, and choose the matching character to go along with it */
            $str .= $characters_to_use[ mt_rand(0, $count - 1) ];
        }
        
        return $str;
    }
    /**
     * Gives us a way to search the registry while also allowing for inheritance
     *
     * @param string $item         The class name or identifying string to search for.
     * @param array  $search_array Reference to the array to sarch for the Item in
     *
     * @return mixed|null
     */
    public static function getItemByClassAncestry(string $item, array &$search_array) {
        # If the class is non-existent, all we can really do is check to see if
        #   we have that name registered in whatever array
        if (!class_exists($item)) {
            return $search_array[ $item ] ?? null;
        }
        
        # Iterate through the ancestor classes to see if we have something registered for them.
        $ancestors = Util::getAncestorClasses($item, true);
        foreach ($ancestors as $class_name) {
            # If there's something in here for the ancestor class (or just this item's class), return that item
            if (isset($search_array[ $class_name ])) {
                return $search_array[ $class_name ];
            }
        }
        
        # If we can't find the item, return null
        return null;
    }
    /**
     * Get an array of the classes that an object inherits from. The first indices are the highest ancestors.
     *
     * @param      $class_name
     * @param bool $append_interfaces
     *
     * @return array
     */
    public static function getAncestorClasses($class_name, $append_interfaces = false) {
        $classes = [];
        $class   = $class_name;
        for ($classes[] = $class; $class = get_parent_class($class); $classes[] = $class) ;
        if ($append_interfaces) {
            $classes += class_implements($class_name);
        }
        return $classes;
    }
    /**
     * Return a "backtrace" array with the most relevant information about a line
     *
     * @todo reconsider moving this function
     *
     * @param int $level
     * @param int $count
     *
     * @return array
     */
    public static function backtrace($level = 0, $count = 1) {
        $max_len   = 1 + $level + $count + 1;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//        array_shift($backtrace);
        $end           = [];
        $previous_line = null;
        $previous_file = null;
        
        # iterate backwards (to keep track of line/file info) and construct an array with only the relevant information.
        for ($i = $max_len; $i--;) {
            $item                  = $backtrace[ $i ];
            $repl_item             = [];
            $previous_file         = $repl_item['file'] = $item['file'] ?? $previous_file;
            $previous_line         = $repl_item['line'] = $item['line'] ?? $previous_line;
            $repl_item['function'] = $item['function'] ?? null;
            $repl_item['class']    = $item['class'] ?? null;
            if ($i < $level) {
                break;
            }
            if ($i > $max_len || $i - $level >= $count) {
                continue;
            }
            $end[] = $s = array_filter($repl_item, function ($r) { return !!($r??false); });
        }
        $end = array_reverse($end);
        if ($count === 1) {
            return $end[0] ?? [];
        }
        return $end;
    }
    /**
     * Check to see if something is one of a fre listed types
     *
     * @param $subject
     * @param $potential_types
     *
     * @return bool
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public static function isOneOfListedTypes($subject, $potential_types) {
        $class = get_class($subject);
        if ($len = count($potential_types)) {
            # iterate through the potential types and see if we're allowed to continue;
            for ($i = 0; $i < $len; $i++) {
                if ($class === $potential_types[ $i ] || is_subclass_of($class, $potential_types[ $i ])) {
                    return true;
                } else if ($i === $len - 1) {
                    return false;
                }
            }
        }
        return true;
    }
}
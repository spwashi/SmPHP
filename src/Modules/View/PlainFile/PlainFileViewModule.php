<?php

namespace Sm\Modules\View\PlainFile;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Util;
use Sm\Representation\Module\RepresentationModule;
use Sm\Representation\View\Proxy\ViewProxy;

class PlainFileViewModule extends RepresentationModule {
    /** @var array $search_directories */
    protected $search_directories = [];
    protected $allowed_extensions = [ 'json', 'js', 'html' ];
    public function __construct() {
        parent::__construct();
        
        $module = $this;
        
        $this->registerRepresentationResolvers(
            [
                /**
                 * @param string $string The name of the template. Just to get very basic templating going
                 * @param array  $vars   An object/array of variables that are going to go into the View as variables
                 */
                function ($filename = null, $search_directories = []) use ($module) {
                    # Only for function calls that are like 'twig_template_name.twig', $vars
                    if (!(is_string($filename))) return null;
                    if (!strlen($filename)) return null;
                    
                    $has_valid_extension = false;
                    foreach ($module->allowed_extensions as $allowed_extension) {
                        if (Util::endsWith($filename, ".{$allowed_extension}")) {
                            $has_valid_extension = true;
                            break;
                        }
                    }
                    
                    if (!$has_valid_extension) return null;
                    
                    $full_filename = null;
                    
                    // This is not an absolute path
                    if ($filename[0] !== '/') {
                        $search_directories = array_reverse(array_merge($this->search_directories, $search_directories));
                        foreach ($search_directories as $search_directory) {
                            $potential_filename = $search_directory . '/' . $filename;
                            $potential_filename = str_replace('//', '/', $potential_filename);
                            if (file_exists($potential_filename)) {
                                $full_filename = $potential_filename;
                                break;
                            }
                        }
                    } else {
                        $full_filename = $filename;
                    }
                    
                    if (!isset($full_filename)) return null;
                    
                    $view = PlainFileView::init()->setItem($full_filename);
                    return ViewProxy::init($view);
                },
            ]);
    }
    public function registerSearchDirectories(array $search_directories) {
        foreach ($search_directories as $search_directory) {
            if (!is_dir($search_directory)) {
                throw new InvalidArgumentException("Can only register directory names");
            }
            $this->search_directories[] = $search_directory;
        }
        return $this;
    }
}
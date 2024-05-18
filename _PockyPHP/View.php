<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

namespace PockyPHP;

class View {

    public static $css_file_location = ROOT.'/www/_elements/styles.css';
    public static $js_file_location  = ROOT.'/www/_elements/scripts.js';

    public static function render(string $view_file, array $variables=[], bool $_is_element = false): string {
        $filename = ROOT.'/Views/'. $view_file. '.php';
        if (!file_exists($filename)) {
            error_log('Error: Could not find view template: '. $view_file);
            return '';
        }
        
        if ($_is_element && (
               !file_exists(static::$css_file_location)
            || !file_exists(static::$js_file_location)
            || filemtime($filename) >= filemtime(static::$css_file_location)
            || filemtime($filename) >= filemtime(static::$js_file_location)
        )) {
            static::rebuildElementFiles();
        }

        $__unlikely_varname_collision_holder = [
            'filename'    => $filename,
            'variables'   => $variables,
            '_is_element' => $_is_element,
        ];
        extract($variables);
        $unique_view_id = 'uid_'.strtr(str_replace([' ','.'], '', microtime()), '0123456789', 'xyzghimnop');
        ob_start();
        require($__unlikely_varname_collision_holder['filename']);
        $contents = ob_get_clean();
        extract($__unlikely_varname_collision_holder);
        $tags = static::locateAllTags($contents);
        $tags = array_reverse($tags);
        foreach ($tags as $tag) {
            $analysis = static::analyzeTag($contents, $tag['name'], $tag['loc']);
            $replacement = null;
            if ($analysis === null) {
                continue; // TagAnalysis failed
            } elseif ($tag['name'] === 'child-content') {
                $replacement = $variables['_child_content'] ?? '';
            } elseif ($_is_element && $tag['name'] === 'style' && !array_key_exists('no_template', $analysis['attributes'])) {
                $replacement = '';
            } elseif ($_is_element && $tag['name'] === 'script' && !array_key_exists('no_template', $analysis['attributes']) && !array_key_exists('src', $analysis['attributes'])) {
                $replacement = '';
            } elseif ($template = static::getElementPath($tag['name'])) {
                $replacement = static::render(
                    $template,
                    array_merge($analysis['attributes'], ['_child_content' => $analysis['contents']]),
                    true
                );
            }
            if ($replacement !== null) {
                $contents = substr($contents,0,$tag['loc']). $replacement. substr($contents, $tag['loc']+$analysis['length']);
            }
        }
        return $contents;
    }

    protected static function rebuildElementFiles() {
        $file_list = [];
        $find_elements = function($path) use (&$file_list, &$find_elements) {
            foreach(scandir($path) as $file) {
                if (substr($file, 0, 1) === '.') continue;
                if (is_dir($path. '/'. $file)) {
                    $find_elements($path. '/'. $file);
                } elseif (substr($file, -4) === '.php') {
                    $file_list[] = $path. '/'. $file;
                }
            }
        };
        $find_elements(ROOT.'/Views/_elements');

        $final_css = '';
        $final_js = '';
        foreach($file_list as $file) {
            $contents = file_get_contents($file);
            $tags = static::locateAllTags($contents);
            foreach($tags as $tag) {
                if ($tag['name'] === 'style' || $tag['name'] === 'script') {
                    $analysis = static::analyzeTag($contents, $tag['name'], $tag['loc']);
                    // Do not use if "no_template" or if it loads a source
                    if (array_key_exists('no_template', $analysis['attributes'])) continue;
                    if ($tag['name'] === 'script' && array_key_exists('src', $analysis['attributes'])) continue;

                    if (strpos($analysis['contents'], '<?php') !== false || strpos($analysis['contents'], '<?=') !== false) {
                        error_log('Error: PHP should not be used in '. $tag['name']. ' in '. $file);
                        exit('Invalid PHP code found in '. $tag['name']. ' tag');
                        $analysis['contents'] = '';
                    }
                    if ($tag['name'] === 'style') {
                        $final_css .= "\n". $analysis['contents'];
                    } elseif ($tag['name'] === 'script') {
                        $final_js .= "\n". $analysis['contents'];
                    }
                }
            }
        }

        // Clear full-line comments
        $final_js = preg_replace('/^\s*\/\/.*$/m', '', $final_js);
        $final_js = preg_replace('/\/\*[\s\S]*?\*\//m', '', $final_js);
        // Trim leading and trailing whitespace
        $final_css = preg_replace('/^\s+/m', '', $final_css);
        $final_css = preg_replace('/\s+$/m', '', $final_css);
        $final_js = preg_replace('/^\s+/m', '', $final_js);
        $final_js = preg_replace('/\s+$/m', '', $final_js);

        // Remove specific newlines
        $final_js = preg_replace('/\{\n/', '{', $final_js);
        $final_js = preg_replace('/\n\}/', '}', $final_js);

        // Write files
        if (file_put_contents(static::$css_file_location, $final_css) === false) {
            error_log('Error: Unable to write '. static::$css_file_location);
        }
        if (file_put_contents(static::$js_file_location, $final_js) === false) {
            error_log('Error: Unable to write '. static::$js_file_location);
        }
    }

    protected static function locateAllTags(string $contents): array {
        $ignoredBookends = static::findIgnoredBoundaries($contents);
        $tags = [];
        preg_match_all('~<[a-zA-Z0-9\-_]+~', $contents, $openings, PREG_OFFSET_CAPTURE);
        foreach($openings[0] as $opening) {
            $tag = ltrim($opening[0], '<');
            $location = $opening[1];
            if (!static::isBetweenAnyPoints($location, $ignoredBookends)) {
                $tags[] = [
                    'name' => $tag,
                    'loc'  => $location
                ];
            }
        }
        return $tags;
    }

    protected static function findIgnoredBoundaries(string $contents): array {
        $patterns = [
            '/(<script.*?>).*?(<\/script>)/is',
            '/(<\!\-\-).*?(\-\->)/s',
        ];
        $results = [];
        foreach($patterns as $pattern) {
            preg_match_all($pattern, $contents, $matches, PREG_OFFSET_CAPTURE);
            foreach($matches[1] as $index => $match) {
                $results[] = [
                    $match[1] + strlen($match[0]),
                    $matches[2][$index][1]
                ];
            }
        }
        return $results;
    }

    protected static function isBetweenAnyPoints(?int $point, array $point_pair_list): bool {
        if ($point == null) return false;
        foreach($point_pair_list as $point_pair) {
            if (($point_pair[0] ?? 0) <= $point && $point <= ($point_pair[1] ?? 0)) {
                return true;
            }
        }
        return false;
    }

    protected static function analyzeTag(string $contents, string $tag, int $location) {
        $result = [
            'tag' => $tag,
            'attributes' => [],
            'contents' => '',
            'length' => 0,
        ];
        $past_opening = 0;
        $self_closed = false;
        $current_attribute = null;
        $attribute_quote_mark = null;
        $gathered = '';
        for ($i = $location+1+strlen($tag); $i < strlen($contents); $i++) {
            $c = substr($contents, $i, 1);
            if ($current_attribute === null) {
                if ($c === '>') {
                    if (!empty($gathered)) {
                        $result['attributes'][$gathered] = '';
                    }
                    $self_closed = (substr($contents, $i-1) === '/' ? true : false);
                    $past_opening = $i+1;
                    $result['length'] = $i - $location + 1;
                    break;
                } elseif ($c === '=' && !empty($gathered)) {
                    $current_attribute = preg_replace('/[^a-zA-Z0-9_]/', '', $gathered);
                    $gathered = '';
                    $attribute_quote_mark = null;
                    continue;
                } elseif ($c === ' ') {
                    if (!empty($gathered)) {
                        $result['attributes'][$gathered] = '';
                    }
                    $gathered = '';
                    continue;
                } elseif ($c === '<') {
                    error_log(__FILE__.' ('.__LINE__.') Error parsing view character `'. $c. '` at '. $i);
                    return null;
                }
            } else {
                if (($c === '"' || $c === "'") && $attribute_quote_mark === null && empty($gathered)) {
                    $attribute_quote_mark = $c;
                    continue;
                } elseif ($c === $attribute_quote_mark) {
                    if (substr($gathered,-1) === '\\') {
                        $gathered = substr($gathered, 0, -1). $c;
                    } else {
                        $result['attributes'][$current_attribute] = $gathered;
                        $current_attribute = null;
                        $gathered = '';
                        $attribute_quote_mark = null;
                    }
                    continue;
                } elseif ($c === '>' && ($attribute_quote_mark === null || $attribute_quote_mark === ' ')) {
                    $result['attributes'][$current_attribute] = $gathered;
                    $current_attribute = null;
                    $gathered = '';
                    $attribute_quote_mark = null;
                    $i--;
                    continue;
                } elseif ($attribute_quote_mark === null) {
                    $attribute_quote_mark = ' ';
                }
            }
            $gathered .= $c;
        }
        if (!$self_closed) {
            $close_tag = '</'.$tag.'>';
            if ($tag === 'script') {
                $end_loc = strpos($contents, $close_tag, $i);
            } else {
                $ignoredBoundaries = static::findIgnoredBoundaries($contents);
                do {
                    $end_loc = strpos($contents, $close_tag, $i);
                    $i = $end_loc;
                } while ($end_loc !== false && static::isBetweenAnyPoints($end_loc, $ignoredBoundaries));
            }
            if ($end_loc !== false) {
                $result['length'] = $end_loc + strlen($close_tag) - $location;
                $result['contents'] = substr($contents, $past_opening, $end_loc-$past_opening);
            }
        }
        return $result;
    }

    protected static $_element_path_map = [];
    protected static function getElementPath(string $tag_name): ?string {
        if (empty(static::$_element_path_map)) {
            require_once(ROOT.'/Views/_elements/_element_map.php');
            foreach($_element_path_map as $tag => $location) {
                $loc = '_elements/'. $location;
                if (!file_exists(ROOT.'/Views/'.$loc.'.php')) {
                    error_log('Cannot find '. $tag. ' element definition at Views/'.$loc.'.php');
                    continue;
                }
                static::$_element_path_map[$tag] = $loc;
            }
        }
        if (!array_key_exists($tag_name, static::$_element_path_map)) {
            $path = '_elements/'. str_replace('-', '/', $tag_name);
            if (file_exists(ROOT.'/Views/'. $path. '.php')) {
                static::$_element_path_map[$tag_name] = $path;
            } else {
                static::$_element_path_map[$tag_name] = null;
            }
        }
        return static::$_element_path_map[$tag_name];
    }

    public static function getAttributes(array $attributes): array {
        $result = [];
        foreach($attributes as $key => $value) {
            if (is_null($value)) continue;
            $result[] = $key. '="'. str_replace('"', '\"', $value). '"';
        }
        return $result;
    }

}

<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

/**
 * Custom element tags are interpreted as hyphen-separated folder and file names. For example, the tag <foo-bar /> would
 * look for code at Views/_elements/foo/bar.php.
 * 
 * To deviate from that convention, use the $_element_path_map array. The array keys are the name of the custom element
 * tag. The array values are the file location under the /Views/_elements/ folder without the .php extension.
**/
$_element_path_map = [
    'alerts' => 'common/alerts',  // <alerts /> is found at /Views/_elements/common/alerts.php
    'card'   => 'common/card',
    'card-row' => 'common/card-row',
];

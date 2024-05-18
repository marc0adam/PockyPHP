<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/


$attributes = View::getAttributes([
    'id' => $id ?? null,
    'class' => 'card-row '. ($class ?? ''),
    'style' => $style ?? null,
    'onclick' => $onclick ?? null,
]);

?>
<div <?= implode(' ', $attributes); ?>><child-content /></div>
<style>
.card-row {
    display: flex;
    flex-wrap: wrap;
}
</style>
<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

$class = $class ?? '';

$type = $type ?? 'button';
if ($type=='link' || isset($href)) {
    $type = 'link';
    $attributes = View::getAttributes([
        'id' => $id ?? null,
        'class' => 'button '. ($class ?? ''),
        'onclick' => $onclick ?? null,
        'href' => $href ?? null,
        'target' => $target ?? null,
        'style' => $style ?? null,
        'download' => $download ?? null,
    ]);
} else {
    if ($type == 'submit') {
        $class .= ' primary';
    }
    $attributes = View::getAttributes([
        'id' => $id ?? null,
        'class' => $class ?? null,
        'onclick' => $onclick ?? null,
        'style' => $style ?? null,
        'type' => $type,
    ]);
}

?>
<?php if ($type=='link'): ?>
    <a <?= implode(' ', $attributes); ?>><?= $pre_content; ?><child-content /></a>
<?php else: ?>
    <button <?= implode(' ', $attributes); ?>><?= $pre_content; ?><child-content /></button>
<?php endif; ?>

<style>
a.button, button {
    display: inline-block;
    padding: 0.6rem 1rem;
    margin-right: 0.6rem;
    font-weight: 600;
    font-size: 1rem;
    font-family: inherit;
    line-height: 1.6rem;
    color: #fff !important;
    text-align: center;
    background: #999999;
    border:none;
    border-radius: 0.6rem;
    cursor: pointer;
}
a.button,button { background:#999999; } /*  a.button:active,button:active { background:#444; } */
a.button.primary, button.primary, button[type=submit] { background: #0060cc; }
a.button.success, button.success { background: #40a181; }
a.button.warning, button.warning { background: #bd9149; }
a.button.danger,  button.danger  { background: #f44336; }
a.button.info,    button.info    { background: #222958; }

a.button > i, button > i { font-size:1.2em; vertical-align:text-bottom; }

a.button.sm, button.sm {
    padding: 0.1rem 0.2rem;
    border-radius: 0.3rem;
}

</style>
<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
?>
<div id="error_bin"></div>

<template id="alert-template">
    <div class="alert-message">
        <a href="javascript:void(0)" onclick="hideAlert(this,0.25)"><icon name="xmark" /></a>
        <span></span>
    </div>
</template>

<script>
function showAlert(message, level, duration = 5.0) {
    let $alert = $($('template#alert-template').html());
    $alert.addClass(level);
    $alert.find('span').html(message);
    $('#error_bin').append($alert);
    $alert.fadeIn(250);
    setTimeout(()=>{ hideAlert($alert); }, duration*1000);
}
function hideAlert(link, fade_time) {
    let $targets = (link != undefined ? $(link).closest('.alert-message') : $('#error_bin > .alert-message'));
    if (fade_time == null) fade_time = 1.0;
    $targets.fadeOut(1000*fade_time, function() {
        $(this).css({visibility:'hidden',display:'block'}).slideUp(250, function() {
            $(this).remove();
        });
    });
}
</script>

<style>
#error_bin {
    position: fixed;
    top: 0.3rem;
    right:1rem;

    display: flex;
    flex-direction: column;
}
.alert-message {
    display: none;
    padding: 0.5rem 0.3rem 0.3rem 1rem;
    margin-bottom: 0.6rem;
    background: #fff;
    border-radius: 0.3rem;
    box-shadow: 0 0 10px #333;
}
    .alert-message > a {
        display: block;
        float: right;
        margin-left: 0.3rem;
        font-size: 1.25rem;
        color: #070707;
    }

.alert-message.error  { background-color:#fbb; }
.alert-message.warning { background-color:#fccf71; }
.alert-message.success { background-color:#7ed6a5; }
</style>
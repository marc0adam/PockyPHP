<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/
?>

<div class="form-group <?= $class ?? ''; ?>">
    <?php if (!empty($label)): ?>
        <label><?= $label; ?></label>
    <?php endif; ?>
    <child-content />
</div>

<style type="text/css">
.form-group {
    display: block;
    margin-bottom: 1rem;
}
.form-group > label {
    display: block;
    margin-bottom: 0.3rem;
}
.form-group input[type=text],
.form-group input[type=password],
.form-group textarea {
    font-family: inherit;
    font-size: inherit;
    padding: 0.3rem 0.6rem;
    width: clamp(200px, 85%, 700px);
    margin: 0 0.3rem 0 0;
    border: 1px solid #7f7f7f;
    border-radius: 0.3rem;
}
.form-group textarea { height:6.25rem; }

.form-group select {
    font-family: inherit;
    font-size: inherit;
    padding: 0.3rem 0.6rem;
    border: 1px solid #7f7f7f;
    border-radius: 0.3rem;
    background: #fff;
}

.form-group input[type=text]:focus,
.form-group input[type=password]:focus,
.form-group textarea:focus,
.form-group select:focus {
    box-shadow: 0 0 4px #09f;
    outline:none;
}

</style>

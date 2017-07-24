<?php
use dmstr\widgets\Alert;

?>
<div class="content-wrapper">
    <div class="content">
        <?= Alert::widget() ?>
        <section class="content-header">
            <h1 class="page-title"><?= $this->title ?></h1>
        </section>
        <section class="content-body">
            <?= $content ?>
        </section>
    </div>
</div>

<div class='control-sidebar-bg'></div>
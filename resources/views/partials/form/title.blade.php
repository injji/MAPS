<?php
$required = $required ?? false;
?>
<div class="">
    <div class="page-title-box" style="margin-left: 1rem">
        <div class="fs-5 fw-bold">
            <span>@lang($title)</span>
            @if ($required)
                <span class="text-primary position-relative" style="left: -5px;top: -5px;">*</span>
            @endif
        </div>
    </div>
</div>

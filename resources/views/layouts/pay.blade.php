<?php 
//CCC 20220514
?>
<!-- Range Slider CSS -->
<link rel="stylesheet" href="/assets/css/store/pay_style.css">
<!--Only for demo purpose - no need to add.-->
<link rel="stylesheet" href="/assets/css/store/pay.css" />

<script>
    function numberWithCommas_store(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>

<section>
    <div class="rt-container">
        <div class="col-rt-12">
            <div class="Scriptcontent">

                <!-- Range Slider HTML -->
                <div slider id="slider-distance">
                    <div>
                        <div inverse-left style="width:70%;"></div>
                        <div inverse-right style="width:70%;"></div>
                        <div range style="left:30%;right:40%;"></div>
                        <span thumb style="left:30%;"></span>
                        <span thumb style="left:60%;"></span>
                        <div sign style="left:30%;">
                            <span id="value" class="min_value">600,000</span>
                        </div>
                        <div sign style="left:60%;">
                            <span id="value" class="max_value">1,200,000</span>
                        </div>
                    </div>

                    <input type="range" id="min_input" name="min_price" tabindex="0" value="{{ request()->min_price ?? 600000 }}" max="2000000" min="0" step="1000" oninput="rangeOne(this)" />

                    <input type="range" id="max_input" name="max_price" tabindex="0" value="{{ request()->max_price ?? 1200000 }}" max="2000000" min="1000" step="1000" oninput="rangeTwo(this)" />
                </div>
                <!-- End Range Slider HTML -->

            </div>
        </div>
    </div>
</section>

<script>
    rangeOne(document.getElementById('min_input'));
    rangeTwo(document.getElementById('max_input'));
    
    function rangeOne(item) {
        item.value=Math.min(item.value,item.parentNode.childNodes[5].value-1);
        var value=(100/(parseInt(item.max)-parseInt(item.min)))*parseInt(item.value)-(100/(parseInt(item.max)-parseInt(item.min)))*parseInt(item.min);
        var children = item.parentNode.childNodes[1].childNodes;
        children[1].style.width=value+'%';
        children[5].style.left=value+'%';
        children[7].style.left=value+'%';children[11].style.left=value+'%';
        children[11].childNodes[1].innerHTML=numberWithCommas_store(item.value);
    }

    function rangeTwo(item) {
        item.value=Math.max(item.value,item.parentNode.childNodes[3].value-(-1));
        var value=(100/(parseInt(item.max)-parseInt(item.min)))*parseInt(item.value)-(100/(parseInt(item.max)-parseInt(item.min)))*parseInt(item.min);
        var children = item.parentNode.childNodes[1].childNodes;
        children[3].style.width=(100-value)+'%';
        children[5].style.right=(100-value)+'%';
        children[9].style.left=value+'%';children[13].style.left=value+'%';
        children[13].childNodes[1].innerHTML=numberWithCommas_store(item.value);
    }
</script>

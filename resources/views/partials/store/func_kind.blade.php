<!-- CCC 20220527 -->
<div class="item_product">
    <a href="/funtioninf?id={{ $service->id }}">
        <div class="item_img">
            <h4><img width="80" src="{{ Storage::url($service->icon) }}"></h4>
        </div>
        <div class="item_content">
            <?php
                $categorys = explode(',',$service->kind);
                $category = [];
                $lang = \Lang::getLocale();
                foreach($categorys as $id){
                    $category_name = \App\Models\Agent\ServiceCategory::find($id);
                    if($category_name){
                        array_push($category, $category_name->$lang);
                    }
                }
            ?>
            <h5>{{ $service->title }}</h5>
            <span>{{ implode(' , ',$category) }}</span>
        </div>
    </a>
</div>

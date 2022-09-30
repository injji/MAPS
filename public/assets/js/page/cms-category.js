$('#form-tab-menu a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
})

$(document).ready(function() {
    $('#category-list-ko, #category-list-en').nestable({
        maxDepth: 2
    });

    $('.expand-all').on('click', function() {
        $(".dd").nestable("expandAll")
    });

    $('.collapse-all').on('click', function() {
        $(".dd").nestable("collapseAll")
    });

    $('.add-category').on('keyup', function(event) {
        if (event.keyCode == 13) {
            var lang = $(this).parents('.tab-pane').attr('id');
            let depth = $(this).data('depth') ?? 1
            let parent = $(this).data('parent') ?? 0
            let title = $(this).val()
            let length = depth == 1 ? $(`#category-list-${lang} > .dd-list > .dd-item`).length + 1 : $(`#category-list-${lang} .dd-item[data-id=${parent}] li`).length + 1;
            console.log(depth, parent, title)
            if (depth == 1 && !parent) {
                $(`#category-list-${lang} > ol`).append(`<li class="dd-item dd3-item" data-id="${length}"><div class="dd-handle dd3-handle"></div>
                <div class="dd3-content">
                    ${title}
                    <span class="float-right text-danger" style="cursor: pointer;" onclick="deleteCategory()"><i class="mdi mdi-close"></i></span>
                </div><ol class="dd-list"><li><input type="text" class="form-control add-category" placeholder="2차 카테고리를 입력하세요. (Enter를 누를 시 카테고리가 추가됩니다.)" data-parent="${length}" data-depth="2"/></li></ol></li>`);
            } else if (depth == 2 && parent > 0) {
                $(this).before(`<li class="dd-item dd3-item" data-id="${length}"><div class="dd-handle dd3-handle"></div>
                <div class="dd3-content">
                    ${title}
                    <span class="float-right text-danger" style="cursor: pointer;" onclick="deleteCategory()"><i class="mdi mdi-close"></i></span>
                </div>`)
            } else {
                toastr.error('카테고리를 생성할 수 없습니다.')
            }

            $(`#category-list-${lang}`).nestable("reinit")
            return false;
        }
    })
});

let deleteCategory = (obj) => {
    console.log('delete category : ', obj)
}

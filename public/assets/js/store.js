//CCC 20220514
let query = new URLSearchParams(window.location.search)

$(document).scroll((event) => {
    $('.fixed-description').toggleClass('active', window.scrollY > 250)
})

$('#lang-change').click((event) => {
    $(event.currentTarget).find('i').toggleClass('spin')

    if ($(event.currentTarget).find('i').hasClass('spin')) {
        $(event.currentTarget).next('.lang-menu').fadeIn(300)
    } else {
        $(event.currentTarget).next('.lang-menu').fadeOut(300)
    }
})

if (query.get('search_type') === 'amount') {
    $(document).ready(() => {
        $('[href="#tab2"]').click()
        $('[href="#tab2"]').addClass('active')
    })
}


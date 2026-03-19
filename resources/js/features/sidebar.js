$(function () {
    const currentUrl = window.location.href

    $('.sidebar-sublink').each(function () {
        const link = $(this)

        if (currentUrl.includes(link.attr('href'))) {
            link.addClass('active')

            const collapse = link.closest('.collapse')
            collapse?.addClass('show')

            const parent = collapse?.prev()
            parent?.removeClass('collapsed').addClass('active')
        }
    })
})

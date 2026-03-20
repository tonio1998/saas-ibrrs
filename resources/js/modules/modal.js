$(document).on('mousedown', '[data-drag]', function (e) {
    const dialog = $(this).closest('.modal-dialog')
    let isDragging = true
    let startX = e.clientX
    let startY = e.clientY

    $(document).on('mousemove.modalDrag', function (e) {
        if (!isDragging) return
        const dx = e.clientX - startX
        const dy = e.clientY - startY
        dialog.css('transform', `translate(${dx}px, ${dy}px)`)
    })

    $(document).on('mouseup.modalDrag', function () {
        isDragging = false
        dialog.css('transform', '')
        $(document).off('.modalDrag')
    })
})

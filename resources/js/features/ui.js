$(document).on('click', '.deleteType', function (e) {
    e.preventDefault()

    const btn = $(this)
    const id = btn.data('id')
    if (!id) return

    Swal.fire({
        icon: 'warning',
        title: 'Delete this certificate type?',
        showCancelButton: true
    }).then(res => {
        if (!res.isConfirmed) return

        btn.prop('disabled', true)

        $.ajax({
            url: `/certificate-types/destroy/${id}`,
            type: 'DELETE',
            success: () => location.reload(),
            error: xhr => {
                Swal.fire('Error', xhr.responseJSON?.message || 'Delete failed', 'error')
            },
            complete: () => btn.prop('disabled', false)
        })
    })
})

$(document).on("click", ".password-toggle", function () {
    const input = this.previousElementSibling
    if (!input) return

    input.type = input.type === "password" ? "text" : "password"

    this.classList.toggle("bi-eye")
    this.classList.toggle("bi-eye-slash")
})

$(document).on('click', '.btn-password', function () {
    const url = $(this).data('url')
    const type = $(this).data('type')

    if (!url) return

    const isRegen = type === 'regenerate'

    Swal.fire({
        icon: 'warning',
        title: isRegen ? 'Regenerate Password?' : 'Generate Password?',
        showCancelButton: true
    }).then(result => {
        if (!result.isConfirmed) return

        $.post(url)
            .done(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        html:
                            `<b>Username:</b> ${res.username}<br>` +
                            `<b>Password:</b> ${res.password}`
                    })
                } else {
                    Swal.fire('Error', res.message, 'error')
                }
            })
            .fail(() => {
                Swal.fire('Error', 'Server error', 'error')
            })
    })
})

import $ from 'jquery'

let table = null
let cache = {}
const CACHE_TTL = 10000

const TABLE_ID = '#requestsTable'
const MODAL_ID = '#filterModal'

const getFilters = () => {
    const data = {}

    $(`${MODAL_ID} [data-filter]`).each(function () {
        const key = $(this).data('filter')
        const val = $(this).val()

        if (val !== null && val !== '') {
            data[key] = val
        }
    })

    return data
}

const getKey = () => JSON.stringify(getFilters())

const isValid = (key) => {
    return cache[key] && (Date.now() - cache[key].time < CACHE_TTL)
}

const loadTable = (force = false) => {
    if (!table) return

    const key = getKey()

    if (!force && isValid(key)) {
        table.clear().rows.add(cache[key].data).draw(false)
        return
    }

    table.ajax.reload(() => {
        cache[key] = {
            data: table.rows().data().toArray(),
            time: Date.now()
        }
    }, false)
}

$(function () {

    const initTable = () => {

        if ($.fn.DataTable.isDataTable(TABLE_ID)) {

            table = $(TABLE_ID).DataTable()

            attachFilters()
            attachEvents()

        } else {
            setTimeout(initTable, 100)
        }
    }

    const attachFilters = () => {

        $(TABLE_ID).on('preXhr.dt', function (e, settings, data) {
            Object.assign(data, getFilters())
        })

    }

    const attachEvents = () => {

        // APPLY
        $(`${MODAL_ID} [data-apply]`)
            .off('click')
            .on('click', () => {

                cache = {} // invalidate cache when filters change

                $(MODAL_ID).modal('hide')
                loadTable(true)
            })

        // RESET
        $(`${MODAL_ID} [data-reset]`)
            .off('click')
            .on('click', () => {

                cache = {}

                $(`${MODAL_ID} [data-filter]`).val('')
                $(MODAL_ID).modal('hide')

                loadTable(true)
            })

        // ENTER KEY = APPLY
        $(`${MODAL_ID} [data-filter]`)
            .off('keydown')
            .on('keydown', (e) => {

                if (e.key === 'Enter') {
                    e.preventDefault()
                    $(`${MODAL_ID} [data-apply]`).click()
                }

            })

        // REFRESH (pull-to-refresh equivalent)
        $('#refreshTable')
            .off('click')
            .on('click', () => {
                cache = {}
                loadTable(true)
            })
    }

    initTable()
})

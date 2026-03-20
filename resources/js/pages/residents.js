import $ from 'jquery'

let table
let cache = {}
const CACHE_TTL = 10000
const TABLE_ID = '#residentsTable'
const MODAL_ID = '#residentFilter'

const getFilters = () => {
    const data = {}
    $(`${MODAL_ID} [data-filter]`).each(function () {
        data[$(this).data('filter')] = $(this).val()
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
        table.clear().rows.add(cache[key].data).draw()
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

        // APPLY (ONLY trigger fetch here)
        $(`${MODAL_ID} [data-apply]`)
            .off('click')
            .on('click', () => {
                $(MODAL_ID).modal('hide')
                loadTable(true)
            })

        // RESET
        $(`${MODAL_ID} [data-reset]`)
            .off('click')
            .on('click', () => {
                $(`${MODAL_ID} [data-filter]`).val('')
                $(MODAL_ID).modal('hide')
                loadTable(true)
            })

        // OPTIONAL: Enter key = Apply
        $(`${MODAL_ID} [data-filter]`).on('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault()
                $(`${MODAL_ID} [data-apply]`).click()
            }
        })

        // Manual refresh
        $('#refreshTable')
            .off('click')
            .on('click', () => loadTable(true))
    }

    initTable()
})

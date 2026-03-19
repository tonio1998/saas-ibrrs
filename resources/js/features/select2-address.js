import $ from 'jquery'
import 'select2'
import 'select2/dist/css/select2.min.css'

window.$ = $
window.jQuery = $

const TTL = 1000 * 60 * 60 * 24

const cacheKey = (type, parent = '') => `addr_${type}_${parent}`

const getCache = key => {
    try {
        const raw = localStorage.getItem(key)
        if (!raw) return null
        const parsed = JSON.parse(raw)
        if (Date.now() > parsed.exp) {
            localStorage.removeItem(key)
            return null
        }
        return parsed.data
    } catch {
        return null
    }
}

const setCache = (key, data) => {
    try {
        localStorage.setItem(key, JSON.stringify({
            data,
            exp: Date.now() + TTL
        }))
    } catch {}
}

const baseConfig = {
    theme: 'bootstrap-5',
    width: '100%',
    placeholder: 'Select option',
    allowClear: true
}

const getVal = el => {
    const v = el.val()
    return v !== undefined && v !== '' ? v : null
}

const createAjaxConfig = (type, url, extraData = () => ({})) => ({
    dataType: 'json',
    delay: 250,
    transport: (params, success, failure) => {
        const data = params.data || {}
        const parentId = data.region || data.province || data.citymun || ''
        const key = cacheKey(type, parentId)

        if (
            (type === 'provinces' && !data.region) ||
            (type === 'cities' && !data.province) ||
            (type === 'barangays' && !data.citymun)
        ) {
            return success({ items: [] })
        }

        const cached = getCache(key)
        if (cached) {
            return success({ items: cached })
        }

        $.ajax({
            url,
            data: params.data
        })
            .then(res => {
                const items = res.items || res || []
                setCache(key, items)
                success({ items })
            })
            .catch(failure)
    },
    data: params => {
        const payload = {
            search: params.term || '',
            page: params.page || 1,
            ...extraData()
        }

        console.log('SELECT2 REQUEST:', payload)

        return payload
    },
    processResults: data => ({
        results: data.items || []
    }),
    cache: false
})

const resetSelect = el => {
    el.val(null).trigger('change')
    el.empty().trigger('change')
}

const initSelect2 = () => {

    const region = $('#aregion')
    const province = $('#aprovince')
    const city = $('#acity')
    const barangay = $('#abarangay')

    region.select2({
        ...baseConfig,
        ajax: createAjaxConfig('regions', '/select2/address/regions')
    })

    province.select2({
        ...baseConfig,
        ajax: createAjaxConfig('provinces', '/select2/address/provinces', () => ({
            region: getVal(region)
        }))
    })

    city.select2({
        ...baseConfig,
        ajax: createAjaxConfig('cities', '/select2/address/cities', () => ({
            province: getVal(province)
        }))
    })

    barangay.select2({
        ...baseConfig,
        ajax: createAjaxConfig('barangays', '/select2/address/barangays', () => ({
            citymun: getVal(city)
        }))
    })

    region.on('select2:select', () => {
        resetSelect(province)
        resetSelect(city)
        resetSelect(barangay)
        province.trigger('select2:open')
    })

    province.on('select2:select', () => {
        resetSelect(city)
        resetSelect(barangay)
        city.trigger('select2:open')
    })

    city.on('select2:select', () => {
        resetSelect(barangay)
        barangay.trigger('select2:open')
    })
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSelect2)
} else {
    initSelect2()
}

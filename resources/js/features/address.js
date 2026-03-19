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

const initSelect = (el, url, paramFn) => {
    el.select2({
        width: '100%',
        placeholder: el.data('placeholder') || 'Select',
        allowClear: true,
        ajax: {
            delay: 250,
            transport: function (params, success, failure) {
                const extra = paramFn ? paramFn() : {}
                const key = cacheKey(url, JSON.stringify(extra) + params.data.term)

                const cached = getCache(key)
                if (cached) {
                    success({ results: cached })
                    return
                }

                $.ajax({
                    url,
                    data: {
                        search: params.data.term,
                        ...extra
                    },
                    success: function (res) {
                        setCache(key, res)
                        success({ results: res })
                    },
                    error: failure
                })
            }
        }
    })
}

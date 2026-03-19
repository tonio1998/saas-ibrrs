import Chart from 'chart.js/auto'

let genderChart, purokChart
let currentRequest = 0

function showLoader(show = true) {
    if (!window.dashboardLoader || !window.dashboardContent) return
    dashboardLoader.style.display = show ? 'block' : 'none'
    dashboardContent.style.display = show ? 'none' : 'block'
}

function initCharts() {
    genderChart = new Chart(genderChartEl, {
        type: 'doughnut',
        data: { labels: [], datasets: [{ data: [] }] },
        options: { plugins: { legend: { position: 'bottom' } } }
    })

    purokChart = new Chart(purokChartEl, {
        type: 'bar',
        data: { labels: [], datasets: [{ data: [] }] },
        options: { plugins: { legend: { display: false } } }
    })
}

function updateChart(chart, dataset) {
    chart.data.labels = Object.keys(dataset || {})
    chart.data.datasets[0].data = Object.values(dataset || {})
    chart.update()
}

function getCacheKey(purokId) {
    return `dashboard_${purokId}`
}

function setCache(key, data) {
    localStorage.setItem(key, JSON.stringify({ data, time: Date.now() }))
}

function getCache(key, ttl = 30000) {
    try {
        const cached = localStorage.getItem(key)
        if (!cached) return null

        const parsed = JSON.parse(cached)
        if (Date.now() - parsed.time > ttl) return null

        return parsed.data
    } catch {
        return null
    }
}

async function fetchDashboard(purokId = 'all', force = false) {
    const requestId = ++currentRequest
    const cacheKey = getCacheKey(purokId)

    if (!force) {
        const cached = getCache(cacheKey)
        if (cached) {
            renderDashboard(cached)
            showLoader(false)
        }
    }

    try {
        showLoader(true)

        const res = await fetch(`/dashboard/data?purok_id=${purokId}`)
        if (!res.ok) throw new Error('Network error')

        const data = await res.json()

        if (requestId !== currentRequest) return

        setCache(cacheKey, data)
        renderDashboard(data)

    } catch (e) {
        if (requestId !== currentRequest) return
        alert('Dashboard load failed')
    } finally {
        if (requestId === currentRequest) {
            showLoader(false)
        }
    }
}

function renderDashboard(data) {
    residentsCount.innerText = data?.cards?.residents ?? 0
    householdsCount.innerText = data?.cards?.households ?? 0

    updateChart(genderChart, data?.charts?.gender)
    updateChart(purokChart, data?.charts?.purok)
}

document.addEventListener('DOMContentLoaded', () => {

    window.genderChartEl = document.getElementById('genderChart')
    window.purokChartEl = document.getElementById('purokChart')
    window.dashboardLoader = document.getElementById('dashboardLoader')
    window.dashboardContent = document.getElementById('dashboardContent')

    window.residentsCount = document.getElementById('residentsCount')
    window.householdsCount = document.getElementById('householdsCount')

    initCharts()
    fetchDashboard()

    document.getElementById('purokFilter').addEventListener('change', function () {
        fetchDashboard(this.value)
    })
})

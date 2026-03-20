import Chart from 'chart.js/auto'

let charts = {}
let currentController = null

const loader = document.getElementById('dashboardLoader')
const purokFilter = document.getElementById('purokFilter')
const yearFilter = document.getElementById('yearFilter')

const showLoader = () => loader?.classList.remove('d-none')
const hideLoader = () => loader?.classList.add('d-none')

async function fetchDashboard(force = false) {
    const purokId = purokFilter?.value || 'all'
    const year = yearFilter?.value || new Date().getFullYear()

    if (currentController) currentController.abort()

    currentController = new AbortController()
    showLoader()

    try {
        const res = await fetch(`/dashboard/data?purok_id=${purokId}&year=${year}`, {
            signal: currentController.signal
        })

        if (!res.ok) throw new Error('Request failed')

        const data = await res.json()

        updateUI(data)

    } catch (e) {
        if (e.name !== 'AbortError') console.error(e)
    } finally {
        hideLoader()
    }
}

function updateUI(data = {}) {
    if (data.cards) updateCards(data.cards)

    if (data.charts) {
        updateChart('genderChart', 'doughnut', data.charts.gender)
        updateChart('purokChart', 'bar', data.charts.purok)
        updateChart('ageChart', 'bar', data.charts.age_groups)
        updateChart('civilChart', 'pie', data.charts.civil_status)
    }

    if (data.operations) {
        updateChart('certChart', 'doughnut', data.operations.certificates)
        updateChart('monthlyChart', 'bar', normalizeMonthly(data.operations.monthly))
    }
}

function updateCards(cards) {
    animate('residentsCount', cards.residents || 0)
    animate('householdsCount', cards.households || 0)
    animate('votersCount', cards.voters || 0)
    animate('seniorCount', cards.senior || 0)

    const avg = document.getElementById('avgHousehold')
    if (avg) avg.innerText = cards.avg_household_size ?? 0
}

function animate(id, value) {
    const el = document.getElementById(id)
    if (!el) return

    const start = parseInt(el.innerText) || 0
    const duration = 400
    const startTime = performance.now()

    const run = (time) => {
        const progress = Math.min((time - startTime) / duration, 1)
        el.innerText = Math.floor(start + (value - start) * progress)
        if (progress < 1) requestAnimationFrame(run)
    }

    requestAnimationFrame(run)
}

function normalizeMonthly(data) {
    if (!data || !data.labels?.length) return { labels: [], data: [] }

    const monthMap = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']

    return {
        labels: data.labels.map(m => monthMap[m - 1] || m),
        data: data.data
    }
}

function updateChart(id, type, data) {
    const canvas = document.getElementById(id)
    if (!canvas) return
    if (!data || !data.labels?.length || !data.data?.length) return

    let chart = charts[id]

    if (chart) {
        chart.data.labels = data.labels
        chart.data.datasets[0].data = data.data
        chart.update()
        return
    }

    const existing = Chart.getChart(canvas)
    if (existing) existing.destroy()

    charts[id] = new Chart(canvas.getContext('2d'), {
        type,
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                borderWidth: 1,
                tension: type === 'line' ? 0.4 : 0,
                fill: type === 'line'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: type === 'line' || type === 'bar'
                ? { y: { beginAtZero: true } }
                : {}
        }
    })
}

async function autoRefresh() {
    if (!document.hidden) {
        await fetchDashboard()
    }
    setTimeout(autoRefresh, 15000)
}

function init() {
    if (!purokFilter) return

    purokFilter.addEventListener('change', fetchDashboard)
    yearFilter?.addEventListener('change', fetchDashboard)

    fetchDashboard()
    autoRefresh()
}

document.addEventListener('DOMContentLoaded', init)

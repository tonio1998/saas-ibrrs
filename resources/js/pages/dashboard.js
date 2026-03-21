import Chart from 'chart.js/auto'

let charts = {}
let controller = null
const formatter = new Intl.NumberFormat('en-PH')

const purokFilter = document.getElementById('purokFilter')
const yearFilter = document.getElementById('yearFilter')
function showChartLoader(id) {
    const el = document.getElementById(id)
    if (!el) return
    el.closest('.chart-card')?.classList.remove('loaded')
}
function generateColors(n) {
    if (n <= 0) return ['#607D8B']

    const baseHue = 140
    const step = 360 / n

    return Array.from({ length: n }, (_, i) => {
        const hue = (baseHue + i * step) % 360
        return `hsl(${hue}, 55%, 52%)`
    })
}
function hideChartLoader(id) {
    const el = document.getElementById(id)
    if (!el) return
    el.closest('.chart-card')?.classList.add('loaded')
}

function abort() {
    if (controller) controller.abort()
    controller = new AbortController()
    return controller.signal
}

async function fetchJSON(url, signal) {
    const res = await fetch(url, { signal })
    if (!res.ok) throw new Error('Request failed')
    return res.json()
}

async function load() {
    const purok = purokFilter.value
    const year = yearFilter.value
    const signal = abort()

    try {
        await loadCards(purok, year, signal)
        await loadCharts(purok, signal)
        await loadOperations(purok, year, signal)
    } catch (e) {
        if (e.name !== 'AbortError') console.error(e)
    }
}

async function loadCards(purok, year, signal) {
    const data = await fetchJSON(`/dashboard/cards?purok_id=${purok}&year=${year}`, signal)
    updateCards(data)
}

async function loadCharts(purok, signal) {
    const ids = ['genderChart','purokChart','ageChart','civilChart']
    ids.forEach(showChartLoader)

    try {
        const data = await fetchJSON(`/dashboard/charts?purok_id=${purok}`, signal)

        updateChart('genderChart','doughnut',data?.gender)
        updateChart('purokChart','bar',data?.purok)
        updateChart('ageChart','bar',data?.age_groups)
        updateChart('civilChart','pie',data?.civil_status)

    } catch (e) {
        if (e.name !== 'AbortError') console.error(e)
    } finally {
        ids.forEach(hideChartLoader)
    }
}

async function loadOperations(purok, year, signal) {
    const ids = ['certChart','monthlyChart']
    ids.forEach(showChartLoader)

    try {
        const data = await fetchJSON(`/dashboard/operations?purok_id=${purok}&year=${year}`, signal)

        updateChart('certChart','doughnut',data.certificates)
        updateChart('monthlyChart','bar',normalizeMonthly(data.monthly))
    } catch (e) {
        if (e.name !== 'AbortError') console.error(e)
    } finally {
        ids.forEach(hideChartLoader)
    }
}

function updateCards(c) {
    animate('revenue', c.revenue)
    animate('residentsCount', c.residents)
    animate('householdsCount', c.households)
    animate('votersCount', c.voters)
    animate('seniorCount', c.senior)

    document.getElementById('avgHousehold').innerText = c.avg_household_size
}

function animate(id, val) {
    const el = document.getElementById(id)
    const start = parseInt(el.innerText.replace(/,/g,'')) || 0
    const t0 = performance.now()

    const run = t => {
        const p = Math.min((t - t0)/400,1)
        el.innerText = formatter.format(Math.floor(start+(val-start)*p))
        if(p<1) requestAnimationFrame(run)
    }

    requestAnimationFrame(run)
}

function normalizeMonthly(d){
    const m=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
    return {labels:d.labels.map(x=>m[x-1]),data:d.data}
}

function updateChart(id, type, data = {}) {
    const canvas = document.getElementById(id)
    if (!canvas) return

    const labels = Array.isArray(data.labels) && data.labels.length ? data.labels : ['No Data']
    const values = Array.isArray(data.data) && data.data.length ? data.data : [0]

    const colors = generateColors(values.length)

    let chart = charts[id]

    if (chart) {
        chart.data.labels = labels
        chart.data.datasets[0].data = values
        chart.data.datasets[0].backgroundColor = colors
        chart.update()
        return
    }

    charts[id] = new Chart(canvas.getContext('2d'), {
        type,
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: type === 'bar'
                ? { y: { beginAtZero: true } }
                : {}
        }
    })
}

function init(){
    purokFilter.addEventListener('change',load)
    yearFilter.addEventListener('change',load)
    load()
}

document.addEventListener('DOMContentLoaded',init)

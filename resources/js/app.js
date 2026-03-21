import './core/jquery'
import './core/bootstrap'
import './core/plugins'
import './core/globals'

import './features/ajax'
import './features/password'
import './features/select2'
import './features/select2-address.js'
import './features/sidebar'
import './features/address'

import './pages/dashboard.js'
import './modules/roles-drag'
import './modules/permissions-drag'
import './pages/household.js'
import './modules/modal.js'
import './pages/residents.js'
import './utils/iosConfirm.js'
import './pages/certiciates.js'
import './helper.js'

const sidebar = document.getElementById('sidebar')
const overlay = document.getElementById('sidebarOverlay')
const toggle = document.getElementById('toggleSidebar')

if(toggle){
    toggle.onclick = () => {
        sidebar.classList.toggle('show')
        overlay.classList.toggle('show')
    }
}

if(overlay){
    overlay.onclick = () => {
        sidebar.classList.remove('show')
        overlay.classList.remove('show')
    }
}

document.querySelectorAll('.sidebar-link, .sidebar-sublink').forEach(link=>{
    link.addEventListener('click', ()=>{
        if(window.innerWidth < 992){
            sidebar.classList.remove('show')
            overlay.classList.remove('show')
        }
    })
})

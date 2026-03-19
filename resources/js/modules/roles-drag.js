import Sortable from "sortablejs"
import Fuse from "fuse.js"

const available = document.getElementById('availableRoles')
const assigned = document.getElementById('assignedRoles')
const searchInput = document.getElementById('searchRoles')
const rolesForm = document.getElementById('rolesForm')

if(available && assigned){

    new Sortable(available,{
        group:'roles',
        animation:150
    })

    new Sortable(assigned,{
        group:'roles',
        animation:150
    })

}

let roles = []

if(available){

    roles = [...available.querySelectorAll('.role-item')].map(el=>{
        return {
            name: el.innerText.trim(),
            element: el
        }
    })

}

if(searchInput && roles.length){

    const fuse = new Fuse(roles,{
        keys:['name'],
        threshold:0.3
    })

    searchInput.addEventListener('keyup',e=>{

        const keyword = e.target.value.trim()

        if(!keyword){

            roles.forEach(r=>{
                r.element.style.display=''
            })

            return
        }

        const results = fuse.search(keyword).map(r=>r.item.name)

        roles.forEach(r=>{
            r.element.style.display = results.includes(r.name) ? '' : 'none'
        })

    })

}

if(rolesForm && assigned){

    rolesForm.addEventListener('submit',function(){

        const container = document.getElementById('roleInputs')
        if(!container) return

        container.innerHTML=''

        assigned.querySelectorAll('.role-item').forEach(role=>{

            const input = document.createElement('input')

            input.type='hidden'
            input.name='roles[]'
            input.value=role.dataset.role

            container.appendChild(input)

        })

    })

}

import Sortable from "sortablejs"
import Fuse from "fuse.js"

const available = document.getElementById('availablePermissions')
const assigned = document.getElementById('assignedPermissions')
const searchInput = document.getElementById('searchPermissions')
const roleForm = document.getElementById('roleForm')

if(available && assigned){

    new Sortable(available,{
        group:'permissions',
        animation:150
    })

    new Sortable(assigned,{
        group:'permissions',
        animation:150
    })

}

let permissions=[]

if(available){

    permissions=[...available.querySelectorAll('.perm-item')].map(el=>{
        return{
            name:el.innerText.trim(),
            element:el
        }
    })

}

if(searchInput && permissions.length){

    const fuse=new Fuse(permissions,{
        keys:['name'],
        threshold:0.3
    })

    searchInput.addEventListener('keyup',e=>{

        const keyword=e.target.value.trim()

        if(!keyword){

            permissions.forEach(p=>{
                p.element.style.display=''
            })

            return
        }

        const results=fuse.search(keyword).map(r=>r.item.name)

        permissions.forEach(p=>{
            p.element.style.display=results.includes(p.name)?'':'none'
        })

    })

}

if(roleForm && assigned){

    roleForm.addEventListener('submit',function(){

        const container=document.getElementById('permissionInputs')
        if(!container) return

        container.innerHTML=''

        assigned.querySelectorAll('.perm-item').forEach(permission=>{

            const input=document.createElement('input')

            input.type='hidden'
            input.name='permissions[]'
            input.value=permission.dataset.permission

            container.appendChild(input)

        })

    })

}

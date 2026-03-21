let resolver = null

const modal = document.getElementById('iosConfirm')
const titleEl = document.getElementById('iosConfirmTitle')
const msgEl = document.getElementById('iosConfirmMessage')
const okBtn = document.getElementById('iosOk')
const cancelBtn = document.getElementById('iosCancel')

function open({title, message}) {
    titleEl.innerText = title
    msgEl.innerText = message
    modal.classList.remove('d-none')
}

function close() {
    modal.classList.add('d-none')
    resolver = null
}

okBtn.onclick = ()=>{
    resolver?.(true)
    close()
}

cancelBtn.onclick = ()=>{
    resolver?.(false)
    close()
}

window.iosConfirm = ({title='Confirm', message='Are you sure?'})=>{
    return new Promise((resolve)=>{
        resolver = resolve
        open({title, message})
    })
}

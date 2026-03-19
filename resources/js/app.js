import './bootstrap'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import $ from 'jquery'
window.$ = window.jQuery = $

import select2 from 'select2'
select2($)
import { Html5QrcodeScanner } from "html5-qrcode"

import 'datatables.net-bs5'
import 'datatables.net-buttons-bs5'
import 'datatables.net-buttons/js/buttons.html5'
import 'datatables.net-buttons/js/buttons.print'
import JSZip from 'jszip'
window.JSZip = JSZip

import Chart from 'chart.js/auto'
window.Chart = Chart

import Swal from 'sweetalert2'
window.Swal = Swal
import './roles-drag.js'
import './permissions-drag.js'
import './dashboard.js'
document.addEventListener("DOMContentLoaded",function(){
    document.querySelectorAll(".password-toggle").forEach(toggle=>{
        toggle.addEventListener("click",function(){
            const input = this.previousElementSibling
            const type = input.type === "password" ? "text" : "password"
            input.type = type
            this.classList.toggle("bi-eye")
            this.classList.toggle("bi-eye-slash")
        })
    })
})

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.select2').each(function () {

        const el = $(this)

        if (el.hasClass('select2-hidden-accessible')) return

        const ajaxUrl = el.data('ajax')
        const placeholder = el.data('placeholder') || 'Select option'
        const value = el.data('value')
        const text = el.data('selected')

        if (value && text && el.find("option[value='" + value + "']").length === 0) {
            const option = new Option(text, value, true, true)
            el.append(option)
        }

        if (ajaxUrl) {

            el.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
                ajax: {
                    url: ajaxUrl,
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function (params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    }
                }
            })

        } else {

            el.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                allowClear: true
            })

        }

    })

    $(document).on('click', '.deleteType', function(e){
        e.preventDefault();

        const btn = $(this);
        const id = btn.data('id');

        if(!confirm('Delete this certificate type?')) return;

        btn.addClass('disabled');

        let url = "/certificate-types/destroy/:id";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(res){
                location.reload();
            },
            error: function(xhr){
                let msg = 'Delete failed';

                if(xhr.responseJSON && xhr.responseJSON.message){
                    msg = xhr.responseJSON.message;
                }

                alert(msg);
            },
            complete: function(){
                btn.removeClass('disabled');
            }
        });

    });

})
document.addEventListener('DOMContentLoaded', function(){
    const currentUrl = window.location.href;
    document.querySelectorAll('.sidebar-sublink').forEach(link => {
        if(currentUrl.includes(link.getAttribute('href'))){
            link.classList.add('active');
            const collapse = link.closest('.collapse');
            if(collapse){
                collapse.classList.add('show');
            }
            const parentLink = collapse?.previousElementSibling;
            if(parentLink){
                parentLink.classList.remove('collapsed');
                parentLink.classList.add('active');
            }

        }

    });

    $(document).on('click','.btn-password',function(){
        let url = $(this).data('url');
        let type = $(this).data('type');
        let title = type === 'regenerate'
            ? 'Regenerate Password?'
            : 'Generate Password?';
        let text = type === 'regenerate'
            ? 'This will reset the existing user password.'
            : 'This will create a login account and generate a password.';
        Swal.fire({
            icon:'warning',
            title:title,
            text:text,
            showCancelButton:true,
            confirmButtonText:'Yes, proceed',
            cancelButtonText:'Cancel'
        }).then(function(result){
            if(!result.isConfirmed) return;
            console.log(url);
            $.ajax({
                url:url,
                type:'POST',
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(res){
                    if(res.success){
                        Swal.fire({
                            icon:'success',
                            title:res.message,
                            html:
                                '<b>Username:</b> '+res.username+
                                '<br><b>Password:</b> '+res.password
                        });
                    }else{
                        Swal.fire({
                            icon:'error',
                            title:'Error',
                            text:res.message
                        });
                    }
                },
                error:function(){
                    Swal.fire({
                        icon:'error',
                        title:'Server Error',
                        text:'Something went wrong.'
                    });
                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const qrContainer = document.getElementById("qr-reader")
    if(qrContainer){
        const qrScanner = new Html5QrcodeScanner(
            "qr-reader",
            {
                fps:10,
                qrbox:250
            },
            false
        )
        qrScanner.render(
            (decodedText)=>{
                document.getElementById("qr-result").value = decodedText
            },
            (error)=>{}
        )
    }

    const nfcBtn = document.getElementById("scan-nfc")

    if(nfcBtn){
        nfcBtn.addEventListener("click", async ()=>{
            if(!("NDEFReader" in window)){
                alert("NFC not supported on this device")
                return
            }
            try{
                const ndef = new NDEFReader()
                await ndef.scan()
                ndef.onreading = event => {
                    const decoder = new TextDecoder()
                    for(const record of event.message.records){
                        const text = decoder.decode(record.data)
                        document.getElementById("nfc-result").value = text
                    }
                }
            }catch(err){
                alert("NFC scan failed")
            }
        })
    }
})

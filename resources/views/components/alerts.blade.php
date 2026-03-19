@php
    $success = session()->pull('success');
    $error = session()->pull('error');
    $warning = session()->pull('warning');
    $info = session()->pull('info');
@endphp

@if($success || $error || $warning || $info)

    <script>
        document.addEventListener('DOMContentLoaded',function(){

            const alerts = {
                success: @json($success),
                error: @json($error),
                warning: @json($warning),
                info: @json($info)
            };

            Object.keys(alerts).forEach(type=>{
                if(alerts[type]){
                    Swal.fire({
                        icon:type,
                        title:type.charAt(0).toUpperCase()+type.slice(1),
                        text:alerts[type],
                        timer:2000,
                        showConfirmButton:false
                    });
                }
            });

        });
    </script>

@endif

$(function(){

    $('.select2').each(function(){

        let ajaxUrl = $(this).data('ajax')

        if(ajaxUrl){

            $(this).select2({
                theme:'bootstrap-5',
                width:'100%',
                placeholder:'Select option',
                allowClear:true,
                ajax:{
                    url:ajaxUrl,
                    dataType:'json',
                    delay:250,
                    data:function(params){
                        return {
                            search: params.term
                        }
                    },
                    processResults:function(data){
                        return {
                            results:data
                        }
                    }
                }
            })

        }else{

            $(this).select2({
                theme:'bootstrap-5',
                width:'100%',
                placeholder:'Select option'
            })

        }

    })

})

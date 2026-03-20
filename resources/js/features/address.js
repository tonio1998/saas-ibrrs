
$(function () {

    function toggleBusinessField() {
        const text = $('#certificate_type_id').find('option:selected').text().toLowerCase();
        const isBusinessClearance = text.includes('business clearance');
        $('.business-field').toggleClass('d-none', !isBusinessClearance);
    }

    $('#certificate_type_id').on('change', function () {
        toggleBusinessField();
    });

    toggleBusinessField();

    $(".get-resident-business").select2({
        theme:'bootstrap-5',
        width:'100%',
        placeholder:'Select business',
        allowClear:true,
        ajax:{
            url: '/select2/businesses/search',
            dataType:'json',
            delay:250,
            data:function(params){
                return {
                    search: params.term,
                    resident_id: $('#resident_id').val()
                }
            },
            processResults:function(data){
                return {results:data}
            }
        }
    })
});

$(function(){

    $('#toggleAddressForm').on('click', function(){
        $('.addr-form').toggleClass('d-none')
    })

    $('#toggleBusinessForm').on('click', function(){
        $('.biz-form').toggleClass('d-none')
    })

    function initSelect2(el, url, getParams){
        el.select2({
            theme:'bootstrap-5',
            width:'100%',
            placeholder:'Select option',
            allowClear:true,
            ajax:{
                url:url,
                dataType:'json',
                delay:250,
                data:function(params){
                    return Object.assign({search:params.term}, getParams($(this)))
                },
                processResults:function(data){
                    return {results:data}
                }
            }
        })
    }

    $('.addr-form, .biz-form').each(function(){

        const form = $(this)

        initSelect2(form.find('.region'), '/select2/address/regions', ()=>({}))
        initSelect2(form.find('.province'), '/select2/address/provinces', f=>({
            region: f.closest('form').find('.region').val()
        }))
        initSelect2(form.find('.city'), '/select2/address/cities', f=>({
            province: f.closest('form').find('.province').val()
        }))
        initSelect2(form.find('.barangay'), '/select2/address/barangays', f=>({
            city: f.closest('form').find('.city').val()
        }))

        const build = () => {
            const full = [
                form.find('.unit').val(),
                form.find('.street').val(),
                form.find('.purok').val() ? 'Purok '+form.find('.purok').val() : '',
                form.find('.barangay option:selected').text(),
                form.find('.city option:selected').text(),
                form.find('.province option:selected').text(),
                form.find('.region option:selected').text()
            ].filter(v => v && v !== 'Select option').join(', ')

            form.find('.full_address').val(full)
        }

        form.on('change','.region, .province, .city, .barangay',build)
        form.on('input','.unit, .street, .purok',build)

    })



    $('#operatorType').on('change', function () {
        const isResident = $(this).val() === 'resident';

        $('.operator-resident').toggleClass('d-none', !isResident);
        $('.operator-custom').toggleClass('d-none', isResident);
    }).trigger('change');

    $('#business_id').on('change', function () {
        const isResident = $(this).val() === 'resident';

        $('.resident-business').toggleClass('d-none', !isResident);
    }).trigger('change');


    document.addEventListener('DOMContentLoaded', () => {
        const toggleOperator = (container) => {
            const type = container.querySelector('input.operator-type:checked')?.value;

            const residentField = container.querySelector('.operator-resident');
            const customField = container.querySelector('.operator-custom');

            if (type === 'resident') {
                residentField.classList.remove('d-none');
                customField.classList.add('d-none');
            } else {
                residentField.classList.add('d-none');
                customField.classList.remove('d-none');
            }
        };

        document.querySelectorAll('.biz-form').forEach(form => {
            form.querySelectorAll('.operator-type').forEach(radio => {
                radio.addEventListener('change', () => toggleOperator(form));
            });

            toggleOperator(form);
        });

    });

})

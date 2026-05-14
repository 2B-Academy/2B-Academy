<!-- Bootstrap bundle JS -->
<script src="{{ asset('admin_dashboard/assets/js/bootstrap.bundle.min.js')}}"></script>
<!--plugins-->
<script src="{{ asset('admin_dashboard/assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('admin_dashboard/assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
<script src="{{ asset('admin_dashboard/assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
<script src="{{ asset('admin_dashboard/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')}}"></script>
<script src="{{ asset('admin_dashboard/assets/js/pace.min.js')}}"></script>
<!--app-->
<script src="{{ asset('admin_dashboard/assets/js/app.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('admin_dashboard/assets/js/tinymce.min.js')}}"></script>
<script src="{{ asset('admin_dashboard/assets/plugins/select2/js/select2.min.js')}}"></script>
<script src="{{ asset('admin_dashboard/assets/js/form-select2.js')}}"></script>

<!--Custom-->
<script>
    $('.select2').select2();
    //Add New Row
        $(document).on('click','#addNew', function(){
            var row = $(this).parent().parent().find('#row').clone();
            $(row).find("input").val("");
            $(row).find("a").remove();
            $(row).appendTo( $(this).parent().parent().find('#rows'));
        });

        //Remove Row
        $(document).on('click','#removeRow', function(){
            if($("#rows").children().length >1){
                $(this).parent().parent().remove();
            }
        });


    $(document).on('click', '#addNewQuestion', function () {
        // Get the number of current question-boxes to determine the next index
        let index = $('#rows .question-box').length;

        // Clone the first row
        let row = $('#rows .question-box:first').clone();

        // Reset input values
        row.find("input").not("[type='radio']").val("");
        row.find("input[type='radio']").prop('checked', false);
        // Update all input names with new index
        row.find('input').each(function () {
            let name = $(this).attr('name');
            if (name) {
                name = name.replace(/questions\[\d+\]/, 'questions[' + index + ']');
                $(this).attr('name', name);
            }
        });
        // Append the new row
        $('#rows').append(row);
    });


    $(window).on('scroll', function() {
        if ($(window).scrollTop() >= 450) {
            $('#addNewQuestion').addClass('fixed-add-new');
        }
        else {
            $('#addNewQuestion').removeClass('fixed-add-new');
        }
    });

</script>
<script type="text/javascript">
    tinymce.init({
        selector: 'textarea.tiny',
        height: 200,
        directionality: 'rtl',
        language: 'ar', // Set Arabic language
        language_url: '{{asset('admin_dashboard/assets/js/tinymc_ar.min.js')}}',
        menubar: 'edit view insert format tools table tc',
        autosave_ask_before_unload: true,
        autosave_interval: "30s",
        autosave_prefix: "{path}{query}-{id}-",
        autosave_restore_when_empty: false,
        image_dimensions: true,
        image_caption: true,
        content_style: 'body { direction: rtl; text-align: right; font-family: Arial, sans-serif; }',

        image_class_list: [{
            title: 'Responsive',
            value: 'img-fluid'
        }],
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks fullscreen',
            'insertdatetime media table paste wordcount'
        ],
        toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media  template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',

        file_picker_callback: function(callback, value, meta) {
            if (meta.filetype === 'image') {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = input.files[0];
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        uploadImage(file).then(function(url) {
                            callback(url, { alt: file.name });
                        });
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            }
        }
    });

    // Image upload function
    function uploadImage(file) {
        const formData = new FormData();
        formData.append('file', file);
        return fetch("{{ route('admin.upload.tiny') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
            .then(response => response.json())
            .then(data => {
                return data.url;
            })
            .catch(error => {
                console.error("Error uploading image:", error);
            });
    }
</script>
<script>
    $(document).ready(function() {
        if (window.File && window.FileList && window.FileReader) {
            $(".fileInput").on("change", function(e) {
                var files = e.target.files,
                    filesLength = files.length;
                var input_name = $(this).attr('name');
                for (var i = 0; i < filesLength; i++) {
                    var f = files[i]
                    var fileReader = new FileReader();
                    fileReader.onload = (function(e) {
                        var file = e.target;
                        $('.preview_'+input_name+' img').attr('src', e.target.result);
                    });
                    fileReader.readAsDataURL(f);
                }
            });
        } else {

        }
    });
</script>
<script>
    var imgUpload = document.getElementById('upload_imgs')
        , imgPreview = document.getElementById('img_preview')
        , totalFiles
        , previewTitle
        , previewTitleText
        , img;

    imgUpload.addEventListener('change', previewImgs, false);

    function previewImgs(event) {
        totalFiles = imgUpload.files.length;

        if(!!totalFiles) {
            imgPreview.classList.remove('quote-imgs-thumbs--hidden');
            previewTitle = document.createElement('p');
            previewTitle.style.fontWeight = 'bold';
            previewTitleText = document.createTextNode(totalFiles + ' Total Images Selected');
            previewTitle.appendChild(previewTitleText);
            imgPreview.appendChild(previewTitle);
        }

        for(var i = 0; i < totalFiles; i++) {
            img = document.createElement('img');
            img.src = URL.createObjectURL(event.target.files[i]);
            img.classList.add('img-preview-thumb');
            imgPreview.appendChild(img);
        }
    }

</script>

<script>
    $('#allUsers').select2({
        placeholder: 'اختر',
        allowClear: true,
        ajax: {
            url: '{{route('admin.ajax.users')}}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.machine_code+' - '+item.name+' - '+item.email+' - '+item.department_name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        }
    });

    $('#allUsersCodes').select2({
        placeholder: 'اختر',
        allowClear: true,
        ajax: {
            url: '{{route('admin.ajax.users')}}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results:  $.map(data, function (item) {
                        return {
                            text: item.machine_code+' - '+item.name+' - '+item.email+' - '+item.department_name,
                            id: item.machine_code
                        }
                    })
                };
            },
            cache: true
        }
    });
</script>

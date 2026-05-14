<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script type="text/javascript">
    $('.delete').click(function(event) {
        event.preventDefault();
        swal({
            title: `هل أنت متأكد من حذف كل العنصر ؟`,
            text: "",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "إلغاء", // Cancel button text
                    visible: true,
                    closeModal: true,
                },
                confirm: {
                    text: "نعم، احذف", // OK/Confirm button text
                    visible: true,
                    closeModal: true
                }
            },
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    let url = $(this).attr('href');
                    var link = this;
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function() {
                            swal({
                                title: "تم الحذف",
                                text: "تم الحذف بنجاح.",
                                icon: "success"
                            });
                            location.reload();
                        }
                    })
                }
            });
    });
</script>

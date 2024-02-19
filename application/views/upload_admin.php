<html>

<head>
    <title>Upload Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" integrity="sha512-rt/SrQ4UNIaGfDyEXZtNcyWvQeOq0QLygHluFQcSjaGB04IxWhal71tKuzP6K8eYXYB6vJV4pHkXcmFGGQ1/0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/10.16.11/sweetalert2.css" integrity="sha512-IThEP8v8WRHuDqEKg3D6V0jROeRcQXGu/02HzCudtHKlLSzl6F6EycdHw34M3gsBA5zsUyR4ynW6j5vS1qE4wQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/10.16.11/sweetalert2.all.js" integrity="sha512-zQu5NZx4gpoe2uy/Qz7/RfcUNSwqfwWXSeWGMZKqBKA0p07pj46Hd9doXX3YmaDx6oensjTS82rw2NSjIKz0jg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <form id="form-foto">
        <!-- Profile picture image-->
        <img id="myprofile" class="img-fluid img-thumbnail" alt="">
        <!-- Profile picture help block-->
        <div class="small font-italic text-muted mb-4">JPG / PNG tidak lebih dari 2 MB</div>
        <!-- Profile picture upload button-->
        <div class="custom-file mb-4">
            <input type="file" class="form-control-sm custom-file-input" id="fileFoto" name="fileFoto">
            <label class="custom-file-label" for="customFile">Pilih Foto</label>
        </div>
        <button type="submit" class="btn btn-primary" id="btn-upload" type="button"><i class="fa fa-upload"></i> Unggah foto</button>
    </form>
</body>

<script>
    $(document).ready(function() {
        
    }).on('submit', '#form-foto', function(e) {
        e.preventDefault();
        var b = $('#btn-upload'),
            i = b.find('i'),
            cls = i.attr('class');

        var dt = new FormData();
        dt.append('fileFoto', $('input#fileFoto')[0].files[0]);
        $.ajax({
            url: '<?= base_url('Admins/do_upload_profile') ?>/1' ,
            dataType: 'json', 
            cache: false,
            contentType: false,
            processData: false,
            data: dt,
            type: 'post',
            beforeSend: function() {
                b.attr("disabled", true);
                i.removeClass().addClass('fa fa-spin fa-spinner');
            },
            success: function(data) {
                if (data.status) {
                    sweetMsg('success', data.message);
                    $('#fileFoto').next('label').html('Choose File');
                    $("#fileFoto").val('');
                    // refresh_files();
                    initialProfil();
                    // set session us foto
                } else {
                    sweetMsg('error', data.message);
                }

                b.removeAttr("disabled");
                i.removeClass().addClass(cls);
            },
            error: function(e) {
                sweetMsg('error', 'Terjadi kesalahan!!');
                i.removeClass().addClass(cls);
                b.removeAttr("disabled");
            }
        });
    });
</script>

</html>
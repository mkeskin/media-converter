<!DOCTYPE html>
<html lang="en">
<head>
    <title>Media Converter</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <div class="col-md-4 mx-auto mt-5">
            <form id="upload-form" action="upload.php" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label for="customFile">File:</label>
                    <div class="custom-file">
                        <input type="file" name="uploadfile" accept="audio/*" class="custom-file-input" id="customFile" required>
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="customFile">Convert to:</label>
                    <select class="custom-select" name="format" required>
                        <option selected disabled>Format Type</option>
                        <option value="mp3">mp3</option>
                        <option value="aac">aac</option>
                        <option value="wav">wav</option>
                    </select>
                </div>
                <div class="progress mb-3 d-none">
                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
                <div class="form-group d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">Convert</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
        $(function() {
            var form = $('#upload-form'),
                button = $('.btn', form),
                input = $('#customFile'),
                progress = $('.progress', form),
                progress_bar = $('.progress-bar', progress)
                filename = null;

            input.on('change', function (event) {
                filename = event.target.files[0].name;

                $(this).next('.custom-file-label').html(filename);
            });

            form.on('submit', function (event) {
                event.preventDefault();

                button.html('<i class="fas fa-circle-notch fa-spin"></i> Processing...').attr('disabled', 'disabled');

                var format = $('select', form).val();

                $.ajax({
                    method: 'POST',
                    url: 'upload.php',
                    data: new FormData(this),
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    async: false,
                    success: function (response) {
                        if (response.status == 'error') {
                            console.error(response.message);

                            return;
                        }

                        var interval = setInterval(function() {
                            var file = response.data.file.split('.').slice(0, -1).join('.');
                            $.ajax({
                                method: 'POST',
                                url: 'check.php',
                                data: { s: file },
                                success: function (response) {
                                    if (response.status == 'error') {
                                        console.error(response.message);

                                        //clearInterval(interval);

                                        return;
                                    }

                                    if (response.status == 'success') {
                                        clearInterval(interval);

                                        button.html('<i class="fas fa-cloud-download-alt"></i> Download Now!').attr('type', 'button').removeAttr('disabled');
                                        button.on('click', function() {
                                            window.location = "download.php?file=" + file + '&title=' + filename.split('.').slice(0, -1).join('.') + '&format=' + format;
                                        });
                                    }

                                    progress.removeClass('d-none');

                                    var state = response.data.progress;
                                    progress_bar.width(state).attr('aria-valuenow', state).html(state + '%');
                                }
                            });
                        }, 1000);
                        
                        $.ajax({
                            method: 'POST',
                            url: 'convert.php',
                            data: { file: response.data.file, format: format },
                            async: true,
                            success: function (response) {
                                if (response.status == 'error') {
                                    console.error(response.message);

                                    return;
                                }
                            }
                        })
                    }
                })             
            })
        })
    </script>
</body>
</html>


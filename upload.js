$(document).ready(function() {
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        var uploaderName = $('#uploaderName').val();
        var fileInput = $('#fileInput')[0].files[0];

        if (!uploaderName || !fileInput) {
            alert('Please provide both uploader name and file.');
            return;
        }

        var formData = new FormData();
        var date = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format
        var newFileName = `${date}_${uploaderName}_${fileInput.name}`;

        formData.append('file', fileInput, newFileName);
        formData.append('uploaderName', uploaderName);
        formData.append('date', date);

        $.ajax({
            url: 'upload.php', // Change this to your server-side upload handler
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert('File uploaded successfully.');
                // ...existing code...
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('File upload failed.');
                // ...existing code...
            }
        });
    });
});

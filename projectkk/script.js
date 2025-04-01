$(document).ready(function() {
    const fileInput = $('#fileInput');
    const chooseFileButton = $('#chooseFile');
    const notification = $('#notification');
    const fileList = $('#fileList');
    const dropZone = $('#dropZone');

    chooseFileButton.click(() => fileInput.click());

    fileInput.change((e) => {
        const file = e.target.files[0];
        if (file) uploadFile(file);
    });

    function uploadFile(file) {
        if (file.size > 5 * 1024 * 1024) {
            showNotification("❌ File size exceeds 5 MB limit.", "error");
            return;
        }

        const formData = new FormData();
        formData.append("file", file);

        $.ajax({
            url: "upload.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, "success");
                    loadFiles();
                } else {
                    showNotification(response.message, "error");
                }
            },
            error: function() {
                showNotification("❌ Upload failed. Try again!", "error");
            }
        });
    }

    function showNotification(message, type) {
        notification.text(message).attr('class', `notification ${type}`).removeClass('hidden');
        setTimeout(() => notification.addClass('hidden'), 3000);
    }

    function loadFiles() {
        $.getJSON("fetch_files.php", function(files) {
            fileList.empty();
            files.forEach(file => {
                const li = $(`
                    <li>
                        <span class="file-name" title="${file.file_name}">${file.file_name}</span>
                        <div class="action-btns">
                            <button class="download-btn" onclick="downloadFile(${file.id})">Download</button>
                            <button class="delete-btn" onclick="deleteFile(${file.id})">Delete</button>
                        </div>
                    </li>
                `);
                fileList.append(li);
            });
        }).fail(function() {
            showNotification("❌ Failed to load files. Try again!", "error");
        });
    }

    window.downloadFile = (id) => {
        window.location.href = `download.php?id=${id}`;
    };

    window.deleteFile = (id) => {
        if (confirm("Are you sure you want to delete this file?")) {
            $.post("delete_file.php", { id }, function(response) {
                if (response.success) {
                    showNotification(response.message, "success");
                    loadFiles();
                } else {
                    showNotification(response.message, "error");
                }
            }).fail(function() {
                showNotification("❌ Failed to delete file. Try again!", "error");
            });
        }
    };

    dropZone.on('dragover', (e) => {
        e.preventDefault();
        dropZone.addClass('drag-over');
    });

    dropZone.on('dragleave', () => dropZone.removeClass('drag-over'));

    dropZone.on('drop', (e) => {
        e.preventDefault();
        dropZone.removeClass('drag-over');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length) uploadFile(files[0]);
    });

    loadFiles();
});

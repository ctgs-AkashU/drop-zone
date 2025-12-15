<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Secure File Transfer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .bg-slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-size: cover;
            background-position: center;
            transition: opacity 1s ease-in-out;
        }
        .upload-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: rgba(0, 0, 0, 0.4);
            padding-left: 3%;
        }
        .upload-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            width: 350px;
            max-width: 90%;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .floating-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9999;
            padding: 14px 22px;
            font-size: 15px;
            border-radius: 50px;
            opacity: 0.85;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .upload-container {
                justify-content: center;
                padding-left: 0;
                padding: 1rem;
            }
            .upload-box {
                width: 100%;
                max-width: 500px;
                padding: 25px;
            }
            .floating-btn {
                bottom: 15px;
                right: 15px;
                padding: 10px 16px;
                font-size: 13px;
            }
        }
        .dropzone {
            border: 2px dashed #007bff;
            border-radius: 10px;
            background: #f8f9fa;
            min-height: 200px;
            padding: 20px;
        }
        .dropzone:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>

    <div id="bg-slideshow" class="bg-slideshow"></div>

    <div class="upload-container">
        <div class="upload-box">
            <h2 class="text-center mb-4">Send Files</h2>
            
            <style>
                .dz-preview-container {
                    max-height: 220px;
                    overflow-y: auto;
                    margin-top: 20px;
                    padding-right: 5px; /* Prevent scrollbar overlay */
                }
                /* Custom scrollbar styling */
                .dz-preview-container::-webkit-scrollbar {
                    width: 8px;
                }
                .dz-preview-container::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 4px;
                }
                .dz-preview-container::-webkit-scrollbar-thumb {
                    background: #888; 
                    border-radius: 4px;
                }
                .dz-preview-container::-webkit-scrollbar-thumb:hover {
                    background: #555; 
                }
                
                #total-progress {
                    opacity: 0;
                    transition: opacity 0.3s linear;
                }
            </style>
            
            <form action="{{ route('upload.store') }}" class="dropzone" id="fileUploadDropzone">
                @csrf
                @method('POST')
                <input type="hidden" name="session_id" id="session_id" value="{{ Str::random(32) }}">
                <div class="dz-message" data-dz-message>
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-cloud-plus text-primary" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5z"/>
                            <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                        </svg>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4">+ Files</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" id="uploadFolderBtn">+ Folder</button>
                    </div>
                    <p class="text-muted mt-2 small">Drag & drop files or click the buttons</p>
                </div>
                <!-- Hidden input for folder selection -->
                <input type="file" id="folderInput" webkitdirectory directory multiple style="display: none;">
                
                <!-- Container for previews to handle scroll -->
                <div class="dz-preview-container" id="previews"></div>
            </form>

            <div class="mt-4">
                <!-- Global Progress Bar -->
                <div id="total-progress" class="progress mb-3" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                         style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>

                <form action="{{ route('upload.complete') }}" method="POST" id="completeForm">
                    @csrf
                    <input type="hidden" name="session_id" id="form_session_id">
                    <div class="mb-3">
                        <textarea class="form-control" name="message" placeholder="Add a message (optional)" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="sendBtn" disabled>Upload</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Floating Admin Panel Button -->
<a href="{{ url('/login') }}" class="btn btn-dark shadow-lg floating-btn"
   onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.85'">
    🔐 Go to Admin Panel
</a>

    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <script>
    // Background slideshow (unchanged)
    const backgrounds = @json($backgrounds->pluck('path'));
    let currentBg = 0;
    const bgElement = document.getElementById('bg-slideshow');

    function changeBackground() {
        if (backgrounds.length > 0) {
            bgElement.style.backgroundImage = `url('public/storage/${backgrounds[currentBg]}')`;
            currentBg = (currentBg + 1) % backgrounds.length;
        }
    }
    if (backgrounds.length > 0) {
        changeBackground();
        setInterval(changeBackground, 5000);
    } else {
        bgElement.style.backgroundColor = '#f0f2f5';
    }

    // ============== DROPZONE CONFIGURATION ==============
    Dropzone.autoDiscover = false;
    const sessionId = document.getElementById('session_id').value;
    document.getElementById('form_session_id').value = sessionId;

    const submitButton = document.getElementById('sendBtn');
    const totalProgress = document.getElementById("total-progress");
    const progressBar = document.querySelector("#total-progress .progress-bar");

    const myDropzone = new Dropzone("#fileUploadDropzone", {
        url: "{{ route('upload.store') }}",
        paramName: "file",
        maxFilesize: 5120,           // 5GB
        timeout: 300000,             // 5 minutes
        chunking: true,
        forceChunking: true,         // Force chunking even for small files (more reliable)
        chunkSize: 5000000,          // 5MB chunks (better than 2MB for speed)
        parallelChunkUploads: false, // Important: sequential chunks
        retryChunks: true,
        retryChunksLimit: 5,
        parallelUploads: 10,          // Allow 2 files at once (smooth but not overwhelming)
        autoProcessQueue: false,
        addRemoveLinks: true,
        previewsContainer: "#previews",
        clickable: [".btn-outline-primary", "#uploadFolderBtn", ".dz-message"],

        init: function () {
            const dz = this;

            // Folder input
            document.getElementById('uploadFolderBtn').addEventListener('click', (e) => {
                e.stopPropagation();
                document.getElementById('folderInput').click();
            });

            document.getElementById('folderInput').addEventListener('change', (e) => {
                [...e.target.files].forEach(file => dz.addFile(file));
                e.target.value = '';
            });

            // Enable send button when files added
            this.on("addedfile", function (file) {
                submitButton.disabled = false;

                // Preserve folder structure
                if (file.webkitRelativePath) {
                    file.relativePath = file.webkitRelativePath;
                }
            });

            this.on("removedfile", function () {
                if (dz.files.length === 0) {
                    submitButton.disabled = true;
                    totalProgress.style.opacity = 0;
                    progressBar.style.width = "0%";
                    progressBar.innerText = "0%";
                }
            });

            // Append session_id & relative_path
            this.on("sending", function (file, xhr, formData) {
                formData.append("session_id", sessionId);
                if (file.relativePath) {
                    formData.append("relative_path", file.relativePath);
                }
            });

            // Global progress (smooth & accurate)
            this.on("totaluploadprogress", function (progress, totalBytes, sentBytes) {
                totalProgress.style.opacity = 1;
                progressBar.style.width = progress + "%";
                progressBar.innerText = Math.round(progress) + "%";
            });

            // When all files + chunks are done
            this.on("queuecomplete", function () {
                submitButton.innerText = 'Finalizing...';
                submitButton.disabled = true;

                // Small delay to ensure last chunk is processed
                setTimeout(() => {
                    document.getElementById('completeForm').submit();
                }, 1500);
            });

            // Error handling
            this.on("error", function (file, errorMessage) {
                console.error("Upload error:", errorMessage);
                if (!file.accepted) return;

                // Show error in preview
                if (file.previewElement) {
                    file.previewElement.classList.add("dz-error");
                    const errorSpan = file.previewElement.querySelector(".dz-error-message") || document.createElement("span");
                    errorSpan.className = "dz-error-message";
                    errorSpan.textContent = errorMessage.message || errorMessage;
                    file.previewElement.appendChild(errorSpan);
                }
            });
        }
    });

    // Start upload when clicking "Upload"
    submitButton.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (myDropzone.getQueuedFiles().length === 0 && myDropzone.files.length === 0) {
            document.getElementById('completeForm').submit();
            return;
        }

        submitButton.innerText = 'Uploading...';
        submitButton.disabled = true;
        totalProgress.style.opacity = 1;
        progressBar.style.width = "0%";
        progressBar.innerText = "0%";

        myDropzone.processQueue();
    });
</script>
</body>
</html>

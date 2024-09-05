@extends('layout.master')

@section('content')
    <div class="upload-wrapper">
        <div class="upload-container">
            <h2>Upload Your Document</h2>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="file" id="file" name="file" accept=".pdf,.docx" class="file-input" required>
                <button type="submit" class="btn">Upload</button>
            </form>
            <div class="message" id="message"></div>
        </div>

        <div class="data-display">
            <h3>Extracted Data</h3>
            <textarea id="extractedContent" readonly></textarea>
        </div>
    </div>

    <hr>

    <div class="documents-list">
        <h3>Uploaded Documents</h3>
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Document Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody id="documentsList">

            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById("file").addEventListener("change", function(event) {
        var formData = new FormData();
        var file = document.getElementById("file").files[0];
        formData.append("file", file);

        fetch("{{ route('api.extract-content') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("extractedContent").value = data.content;
            } else {
                document.getElementById("message").textContent = data.message || "File extraction failed.";
                document.getElementById("message").className = "error";
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById("message").textContent = "Error: " + error;
            document.getElementById("message").className = "error";
        });
    });

    function clearForm() {
        document.getElementById("uploadForm").reset();
        document.getElementById("extractedContent").value = "";
    }

    document.getElementById("uploadForm").addEventListener("submit", function(event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append('extracted_content', document.getElementById("extractedContent").value);

        // for (let [key, value] of formData.entries()) {
        //     console.log(`${key}: ${value}`);
        // }

        fetch("{{ route('api.upload-document') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("message").textContent = "File Uploaded Successfully";
                document.getElementById("message").className = "success";
                loadDocuments();
                clearForm();
            } else {
                document.getElementById("message").textContent = data.message;
                document.getElementById("message").className = "error";
            }
        })
        .catch(error => {
            document.getElementById("message").textContent = "Error: " + error;
            document.getElementById("message").className = "error";
        });
    });

    function loadDocuments() {
        fetch("{{ route('api.get-documents') }}")
        .then(response => response.json())
        .then(data => {
            const documentsList = document.getElementById("documentsList");
            documentsList.innerHTML = "";

            data.documents.forEach((doc, index) => {
                let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${doc.file_name}</td>
                    <td>${doc.description}</td>
                </tr>`;
                documentsList.innerHTML += row;
            });
        });
    }

    window.onload = loadDocuments;
</script>
@endsection

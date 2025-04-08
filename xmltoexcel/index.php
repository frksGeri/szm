<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XML to XLSX Converter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dropzone {
            border: 2px dashed #ccc;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .dropzone.dragover {
            background-color: #e1e1e1;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>XML to XLSX Converter</h2>
        <form id="uploadForm" enctype="multipart/form-data" method="POST" action="process.php">
            <div class="dropzone" id="dropzone">
                Húzd ide az XML fájlt, vagy kattints a fájl kiválasztásához<br><br>
                <input type="file" name="xmlFile" id="xmlFile" accept=".xml" style="display: none;" required>
                <span id="fileName">Nincs fájl kiválasztva</span>
            </div>
            <button type="submit">XLSX Generálása</button>
        </form>
    </div>

    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('xmlFile');
        const fileNameDisplay = document.getElementById('fileName');

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileNameDisplay.textContent = files[0].name;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            }
        });
    </script>
</body>
</html>
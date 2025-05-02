<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $keyword = $_POST['keyword'];
    $operation = $_POST['operation'];
    $outputType = $_POST['output_type'];

    $uploadedFile = $_FILES['filename']['tmp_name'];
    $originalName = $_FILES['filename']['name'];
    $lines = file($uploadedFile);
    $modifiedLines = [];
    echo "<h3>Hasil Pencarian:</h3><pre>";
    foreach ($lines as $line) {
        if (stripos($line, $keyword) !== false) {
            if ($operation === 'redact') {
                $redactedLine = preg_replace("/(" . preg_quote($keyword, '/') . ")/i", "***", $line);
                echo htmlspecialchars($redactedLine);
                $modifiedLines[] = $redactedLine;
            } else {
                echo htmlspecialchars($line);
                $modifiedLines[] = $line;
            }
        } else {
            $modifiedLines[] = $line;
        }
    }
    echo "</pre>";
    if ($operation === 'redact') {
        if ($outputType === 'O') {
            file_put_contents($uploadedFile, implode("", $modifiedLines));
            echo "<p><strong>Perubahan disimpan ke file yang sama.</strong></p>";
        } else {
            if (!is_dir('output')) {
                mkdir('output', 0777, true);
            }
            $pathinfo = pathinfo($originalName);
            $newFilename = "output/" . $pathinfo['filename'] . "-new." . $pathinfo['extension'];
            file_put_contents($newFilename, implode("", $modifiedLines));
            echo "<p><strong>Perubahan disimpan ke file baru:</strong> <code>$newFilename</code></p>";
        }
    }
} else {
    echo "Akses tidak sah.";
}
?>
Add-Type -AssemblyName System.Drawing

$sourcePath = "C:\xampp\htdocs\PrimeLand-Hotel\public\services_images"

Get-ChildItem -Path $sourcePath -Filter "*.jpg" | ForEach-Object {
    $file = $_.FullName
    Write-Host "Processing: $file"
    
    $originalImage = [System.Drawing.Image]::FromFile($file)
    $originalWidth = $originalImage.Width
    $originalHeight = $originalImage.Height
    
    if ($originalWidth -gt 800) {
        $ratio = 800.0 / $originalWidth
        $newHeight = [math]::Round($originalHeight * $ratio)
        
        $newImage = New-Object System.Drawing.Bitmap(800, $newHeight)
        $graphics = [System.Drawing.Graphics]::FromImage($newImage)
        $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
        $graphics.DrawImage($originalImage, 0, 0, 800, $newHeight)
        
        $originalImage.Dispose()
        $graphics.Dispose()
        
        $tempFile = $file + ".tmp.jpg"
        $newImage.Save($tempFile, [System.Drawing.Imaging.ImageFormat]::Jpeg)
        $newImage.Dispose()
        
        Remove-Item $file -Force
        Rename-Item $tempFile $file
        Write-Host " -> Resized to 800px width."
    } else {
        $originalImage.Dispose()
        Write-Host " -> Already small enough."
    }
}
Write-Host "Done resizing."

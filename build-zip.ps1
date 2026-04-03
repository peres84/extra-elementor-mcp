#
# Build a distributable zip for the Extra Elementor MCP plugin.
# Usage: powershell -File build-zip.ps1
# Output: .\dist\extra-elementor-mcp.zip
#

$ErrorActionPreference = "Stop"

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$PluginSlug = "extra-elementor-mcp"
$ScriptDir  = Split-Path -Parent $MyInvocation.MyCommand.Path
$OutputDir  = Join-Path $ScriptDir "dist"
$OutputFile = Join-Path $OutputDir "$PluginSlug.zip"
$TempDir    = Join-Path $env:TEMP "build-$PluginSlug-$(Get-Random)"
$Dest       = Join-Path $TempDir $PluginSlug

Write-Host "Building $PluginSlug.zip ..."

# Create output directory
New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null

# Create temp plugin folder
New-Item -ItemType Directory -Path $Dest -Force | Out-Null

# Copy only the files WordPress needs
Copy-Item "$ScriptDir\extra-elementor-mcp.php" -Destination $Dest
Copy-Item "$ScriptDir\readme.txt"              -Destination $Dest
Copy-Item "$ScriptDir\LICENSE"                 -Destination $Dest
Copy-Item "$ScriptDir\includes" -Destination "$Dest\includes" -Recurse
Copy-Item "$ScriptDir\assets"   -Destination "$Dest\assets"   -Recurse

# Remove old zip if it exists.
if (Test-Path $OutputFile) {
	Remove-Item $OutputFile -Force
}

# Build zip manually with forward-slash entry paths (WordPress requirement)
$zip = [System.IO.Compression.ZipFile]::Open($OutputFile, [System.IO.Compression.ZipArchiveMode]::Create)

Get-ChildItem -Path $Dest -Recurse -File | ForEach-Object {
    $relativePath = $_.FullName.Substring($TempDir.Length + 1).Replace('\', '/')
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
        $zip, $_.FullName, $relativePath,
        [System.IO.Compression.CompressionLevel]::Optimal
    ) | Out-Null
}

$zip.Dispose()

# Cleanup
Remove-Item $TempDir -Recurse -Force

Write-Host "Done -> $OutputFile"

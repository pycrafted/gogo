
$headers = @{
    "Content-Type" = "application/json"
}

$body = @{
    training_id = 1
    first_name = "Test"
    last_name = "PowerShell"
    email = "testpowershell@example.com"
    phone = "0123456789"
    company = "TestCorp"
    position = "Développeur"
    notes = "Test PowerShell"
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/participants.php" -Method POST -Headers $headers -Body $body
    Write-Host "Status: $($response.StatusCode)"
    Write-Host "Response: $($response.Content)"
} catch {
    Write-Host "Error: $($_.Exception.Message)"
    Write-Host "Response: $($_.Exception.Response)"
}

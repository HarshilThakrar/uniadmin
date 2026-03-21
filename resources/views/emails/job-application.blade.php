<!DOCTYPE html>
<html>
<head>
    <title>New Job Application</title>
</head>
<body>
    <h2>New Job Application Received</h2>
    <p><strong>Position Applied For:</strong> {{ $data['position'] }}</p>
    <p><strong>Applicant Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Email Address:</strong> {{ $data['email'] }}</p>
    <p><strong>Phone Number:</strong> {{ $data['phone'] }}</p>
    <p><br>The applicant's resume is attached to this email.</p>
</body>
</html>
